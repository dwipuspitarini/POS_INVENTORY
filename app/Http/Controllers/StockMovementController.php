<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    /**
     * Halaman tambah stok
     */
    public function create()
    {
        $products = Product::with([
                'category',
            ])
            ->orderBy('nama')
            ->get();

        $suppliers = Supplier::orderBy('nama')
            ->get();

        $productData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'nama' => $product->nama,
                'kategori' => $product->category->nama ?? '-',
                'stok' => $product->stok,
            ];
        })->values();

        return view('stocks.create', compact(
            'products',
            'suppliers',
            'productData'
        ));
    }


    /**
     * Simpan stok masuk
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'tanggal_stok' => [
                'required',
                'date',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],

            'items.*.jumlah' => [
                'required',
                'integer',
                'min:1',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'supplier_id.required' =>
                'Supplier harus dipilih.',

            'supplier_id.exists' =>
                'Supplier yang dipilih tidak ditemukan.',

            'tanggal_stok.required' =>
                'Tanggal stok harus diisi.',

            'tanggal_stok.date' =>
                'Tanggal stok tidak valid.',

            'items.required' =>
                'Belum ada produk yang dipilih.',

            'items.min' =>
                'Tambahkan minimal satu produk.',

            'items.*.product_id.required' =>
                'Produk harus dipilih.',

            'items.*.product_id.distinct' =>
                'Produk tidak boleh dipilih lebih dari satu kali.',

            'items.*.product_id.exists' =>
                'Produk yang dipilih tidak ditemukan.',

            'items.*.jumlah.required' =>
                'Jumlah stok harus diisi.',

            'items.*.jumlah.integer' =>
                'Jumlah stok harus berupa angka.',

            'items.*.jumlah.min' =>
                'Jumlah stok minimal 1.',

            'keterangan.max' =>
                'Keterangan maksimal 500 karakter.',
        ]);


        DB::transaction(function () use ($validated) {

            foreach ($validated['items'] as $item) {

                $product = Product::lockForUpdate()
                    ->findOrFail($item['product_id']);


                /*
                |--------------------------------------------------------------------------
                | Tambahkan stok produk
                |--------------------------------------------------------------------------
                */

                $product->increment(
                    'stok',
                    $item['jumlah']
                );


                /*
                |--------------------------------------------------------------------------
                | Simpan riwayat stok
                |--------------------------------------------------------------------------
                */

                StockMovement::create([
                    'product_id' => $product->id,

                    'supplier_id' =>
                        $validated['supplier_id'],

                    'user_id' =>
                        Auth::id(),

                    'jenis' =>
                        'masuk',

                    'jumlah' =>
                        $item['jumlah'],

                    'tanggal_stok' =>
                        $validated['tanggal_stok'],

                    'keterangan' =>
                        $validated['keterangan']
                        ?? 'Penambahan stok',
                ]);
            }
        });


        return redirect()
            ->route('stocks.index')
            ->with(
                'success',
                'Stok berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan riwayat pergerakan stok
     */
    public function index(Request $request)
    {
        $query = StockMovement::with([
            'product.category',
            'supplier',
            'user',
        ])->latest('tanggal_stok');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // SKU dan nama produk
                $q->whereHas('product', function ($productQuery) use ($search) {

                    $productQuery
                        ->where('sku', 'like', '%' . $search . '%')
                        ->orWhere('nama', 'like', '%' . $search . '%');

                })

                // Kategori
                ->orWhereHas('product.category', function ($categoryQuery) use ($search) {

                    $categoryQuery->where(
                        'nama',
                        'like',
                        '%' . $search . '%'
                    );

                })

                // Supplier
                ->orWhereHas('supplier', function ($supplierQuery) use ($search) {

                    $supplierQuery->where(
                        'nama',
                        'like',
                        '%' . $search . '%'
                    );

                })

                // Petugas
                ->orWhereHas('user', function ($userQuery) use ($search) {

                    $userQuery->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    );

                })

                // Jenis
                ->orWhere(
                    'jenis',
                    'like',
                    '%' . $search . '%'
                )

                // Jumlah
                ->orWhere(
                    'jumlah',
                    'like',
                    '%' . $search . '%'
                )

                // Keterangan
                ->orWhere(
                    'keterangan',
                    'like',
                    '%' . $search . '%'
                )

                // Tanggal sesuai format yang ditampilkan
                ->orWhereRaw(
                    "DATE_FORMAT(tanggal_stok, '%d-%m-%Y') LIKE ?",
                    ['%' . $search . '%']
                );

            });
        }

        // Filter tanggal mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal_stok',
                '>=',
                $request->tanggal_mulai
            );
        }

        // Filter tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal_stok',
                '<=',
                $request->tanggal_akhir
            );
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where(
                'jenis',
                $request->jenis
            );
        }

        // Filter produk
        if ($request->filled('product_id')) {
            $query->where(
                'product_id',
                $request->product_id
            );
        }

        // 1. Hitung Ringkasan Statistik dari SELURUH DATA yang terfilter (sebelum dipaginate)
        $allMovements = (clone $query)->get();
        $totalMasuk   = $allMovements->where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar  = $allMovements->where('jenis', 'keluar')->sum('jumlah');

        // 2. Gunakan PAGINATE (10 data per halaman) dan sertakan query string filter saat ini
        $movements = $query->paginate(10)->withQueryString();

        $products = Product::orderBy('nama')->get();

        return view('stocks.history', compact(
            'movements',
            'products',
            'totalMasuk',
            'totalKeluar'
        ));
    }
}