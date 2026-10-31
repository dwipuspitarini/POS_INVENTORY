<?php

namespace App\Http\Controllers;

use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SalesReturnController extends Controller
{
    public function index()
    {
        $returns = SalesReturn::with([
            'transaction.customer',
            'user',
            'details.product',
        ])
            ->latest('tanggal_retur')
            ->paginate(10);

        return view('sales_returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $transactions = Transaction::with('customer')
            ->latest('tanggal_transaksi')
            ->get();

        $transaction = null;
        $details = collect();

        if ($request->filled('transaksi_id')) {
            $transaction = Transaction::with([
                'customer',
                'details.product',
            ])->findOrFail($request->transaksi_id);

            $details = $transaction->details;

            foreach ($details as $detail) {
                $detail->jumlah_sudah_diretur =
                    (int) SalesReturnDetail::where(
                        'transaction_detail_id',
                        $detail->id
                    )->sum('quantity');

                $detail->sisa_retur = max(
                    (int) $detail->quantity -
                    $detail->jumlah_sudah_diretur,
                    0
                );
            }
        }

        return view('sales_returns.create', compact(
            'transactions',
            'transaction',
            'details'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaksi_id' => [
                'required',
                'integer',
                'exists:transactions,id',
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array'],
            'items.*.quantity' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'items.*.kondisi' => [
                'nullable',
                'in:layak,rusak',
            ],
            'items.*.alasan' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $items = collect($validated['items'])
            ->filter(function ($item) {
                return isset($item['quantity'])
                    && (int) $item['quantity'] > 0;
            });

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Masukkan jumlah barang yang akan diretur.',
            ]);
        }

        $salesReturn = DB::transaction(function () use (
            $validated,
            $items
        ) {
            $transaction = Transaction::whereKey(
                $validated['transaksi_id']
            )
                ->lockForUpdate()
                ->firstOrFail();

            $returnDetails = [];
            $productIds = [];

            /*
             * Validasi detail transaksi dan sisa retur.
             * Semua perhitungan dilakukan kembali di server.
             */
            foreach ($items as $detailId => $item) {
                $quantity = (int) $item['quantity'];

                $detail = TransactionDetail::whereKey($detailId)
                    ->where('transaksi_id', $transaction->id)
                    ->lockForUpdate()
                    ->first();

                if (!$detail) {
                    throw ValidationException::withMessages([
                        'items' => 'Detail barang tidak sesuai dengan transaksi.',
                    ]);
                }

                $alreadyReturned = (int) SalesReturnDetail::where(
                    'transaction_detail_id',
                    $detail->id
                )->sum('quantity');

                $remaining = (int) $detail->quantity - $alreadyReturned;

                if ($quantity > $remaining) {
                    throw ValidationException::withMessages([
                        'items' => 'Jumlah retur untuk produk ' .
                            ($detail->product->nama ?? '') .
                            ' melebihi sisa yang dapat diretur (' .
                            max($remaining, 0) . ').',
                    ]);
                }

                if (empty($item['kondisi'])) {
                    throw ValidationException::withMessages([
                        'items' => 'Pilih kondisi barang untuk setiap barang yang diretur.',
                    ]);
                }

                $returnDetails[] = [
                    'detail' => $detail,
                    'quantity' => $quantity,
                    'kondisi' => $item['kondisi'],
                    'alasan' => $item['alasan'] ?? null,
                ];

                $productIds[] = $detail->product_id;
            }

            /*
             * Kunci semua produk agar stok tidak diperbarui
             * secara bersamaan oleh proses lain.
             */
            $products = Product::whereIn(
                'id',
                array_unique($productIds)
            )
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($returnDetails as $item) {
                $productId = $item['detail']->product_id;

                if (!isset($products[$productId])) {
                    throw ValidationException::withMessages([
                        'items' => 'Produk pada transaksi tidak ditemukan.',
                    ]);
                }
            }

            $nomorRetur = 'RET-' .
                now()->format('YmdHis') . '-' .
                Str::upper(Str::random(5));

            $salesReturn = SalesReturn::create([
                'nomor_retur' => $nomorRetur,
                'transaksi_id' => $transaction->id,
                'user_id' => Auth::id(),
                'tanggal_retur' => now(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            foreach ($returnDetails as $item) {
                $detail = $item['detail'];
                $quantity = $item['quantity'];

                $subtotal = (float) $detail->harga * $quantity;

                SalesReturnDetail::create([
                    'sales_return_id' => $salesReturn->id,
                    'transaction_detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'quantity' => $quantity,
                    'kondisi' => $item['kondisi'],
                    'harga' => $detail->harga,
                    'subtotal' => $subtotal,
                    'alasan' => $item['alasan'],
                ]);

                /*
                 * Hanya barang layak jual yang menambah stok.
                 */
                /*
 * Semua barang yang diterima kembali menambah stok.
 * Barang rusak yang tidak dapat dijual kembali dapat
 * dikurangi oleh owner melalui fitur Penyesuaian Stok.
 */
$product = $products[$detail->product_id];

// Tambahkan stok produk yang diretur.
$product->increment('stok', $quantity);

// Catat pergerakan stok masuk.
StockMovement::create([
    'product_id' => $product->id,
    'supplier_id' => null,
    'user_id' => Auth::id(),
    'jenis' => 'masuk',
    'jumlah' => $quantity,
    'tanggal_stok' => now(),
    'keterangan' => 'Retur penjualan ' .
        $nomorRetur .
        ' dari invoice ' .
        $transaction->nomor_invoice .
        ' (' . $item['kondisi'] . ')',
]);
            }

            return $salesReturn;
        }, 3);

        return redirect()
            ->route('sales-returns.show', $salesReturn)
            ->with('success', 'Retur penjualan berhasil disimpan.');
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load([
            'transaction.customer',
            'user',
            'details.product',
        ]);

        return view('sales_returns.show', [
            'salesReturn' => $salesReturn,
        ]);
    }
}
