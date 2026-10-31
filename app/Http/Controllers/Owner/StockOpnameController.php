<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockOpnameController extends Controller
{
    public function index()
    {
        $stockOpnames = StockOpname::with('user')
            ->withCount('details')
            ->latest()
            ->paginate(10);

        return view('owner.stock_opnames.index', compact('stockOpnames'));
    }

    public function create()
    {
        $products = Product::orderBy('nama')->get();

        return view('owner.stock_opnames.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_opname' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
            'details.*.stok_fisik' => [
                'required',
                'integer',
                'min:0',
            ],
            'details.*.keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $stockOpname = DB::transaction(function () use ($validated) {
            $detailsInput = collect($validated['details']);

            $productIds = $detailsInput
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $products = Product::whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages([
                    'details' => 'Ada produk yang tidak ditemukan.',
                ]);
            }

            $stockOpname = StockOpname::create([
                'tanggal_opname' => $validated['tanggal_opname'],
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'keterangan' => $validated['keterangan'] ?? null,
                ]);

            $stockOpname->kode = 'OPN-' .
            $stockOpname->tanggal_opname->format('Ymd') . '-' .
            str_pad(
                (string) $stockOpname->id,
                4,
                '0',
                STR_PAD_LEFT
                );

            $stockOpname->save();


            foreach ($detailsInput as $item) {
                $product = $products->get((int) $item['product_id']);

                $stokSistem = (int) $product->stok;
                $stokFisik = (int) $item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                $stockOpname->details()->create([
                    'product_id' => $product->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $item['keterangan'] ?? null,
                ]);

                if ($selisih === 0) {
                    continue;
                }

                $product->stok = $stokFisik;
                $product->save();

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'jenis' => $selisih > 0 ? 'masuk' : 'keluar',
                    'jumlah' => abs($selisih),
                    'tanggal_stok' => $validated['tanggal_opname'],
                    'keterangan' => 'Penyesuaian stok opname ' .
                        $stockOpname->kode .
                        (!empty($item['keterangan'])
                        ? ' - ' . $item['keterangan']
                        : ''),
                ]);
            }

            return $stockOpname;
        });

        return redirect()
            ->route('owner.stock-opnames.show', $stockOpname)
            ->with('success', 'Stok opname berhasil disimpan.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['user', 'details.product']);

        return view('owner.stock_opnames.show', compact('stockOpname'));
    }
}
