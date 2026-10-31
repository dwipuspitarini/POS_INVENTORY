<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\SalesReturnDetail;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi
     */
    public function index(Request $request)
    {
    $query = Transaction::with(['customer','user'
    ])->latest('tanggal_transaksi');
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nomor_invoice','like','%' . $search . '%')
            ->orWhereHas('customer', function ($customer) use ($search) {
                $customer->where('nama','like','%' . $search . '%');
            });
        });
    }
    $transactions = $query
        ->paginate(10)
        ->withQueryString();
    return view('transactions.index', compact('transactions'));
    }


    /**
     * Menampilkan form transaksi baru
     */
    public function create()
    {
        $customers = Customer::orderBy('nama')->get();

        $products = Product::where('stok', '>', 0)
            ->orderBy('nama')
            ->get();

        return view(
            'transactions.create',
            compact(
                'customers',
                'products'
            )
        );
    }


    /**
     * Menyimpan transaksi
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI DATA
        |--------------------------------------------------------------------------
        */

        $allowBackdate = (bool) config(
            'transactions.allow_backdate',
            false
        );

        $validated = $request->validate([

            'tanggal_transaksi' => $allowBackdate
            ? ['required', 'date_format:Y-m-d\TH:i']
            : ['nullable'],


            'pelanggan_id' => [
                'nullable',
                'exists:customers,id'
            ],

            'diskon' => [
                'required',
                'numeric',
                'min:0'
            ],

            'jumlah_bayar' => [
                'required',
                'numeric',
                'min:0'
            ],

            'metode_pembayaran' => [
                'required',
                'in:tunai,qris,mesin'
            ],

            'status_pembayaran' => [
                'required',
                'in:lunas,dp'
            ],

            'cart' => [
                'required',
                'json'
            ],
        ]);


        if ($allowBackdate) {
    $tanggalTransaksi = Carbon::createFromFormat(
        'Y-m-d\TH:i',
        $validated['tanggal_transaksi']
    );

    if (
        $tanggalTransaksi->lt(Carbon::parse('2026-09-01 00:00:00')) ||
        $tanggalTransaksi->gt(Carbon::parse('2026-09-30 23:59:59'))
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'tanggal_transaksi' =>
                    'Tanggal transaksi harus berada pada September 2026.',
            ]);
    }
} else {
    $tanggalTransaksi = now();
}


        /*
        |--------------------------------------------------------------------------
        | DECODE CART
        |--------------------------------------------------------------------------
        */

        $cart = json_decode(
            $validated['cart'],
            true
        );


        if (!is_array($cart) || count($cart) === 0) {

            return back()
                ->withInput()
                ->withErrors([
                    'cart' =>
                        'Keranjang transaksi masih kosong.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI ITEM CART
        |--------------------------------------------------------------------------
        */

        
$normalizedCart = [];

foreach ($cart as $item) {

    if (
        !is_array($item) ||
        !isset($item['product_id'], $item['quantity']) ||
        filter_var($item['product_id'], FILTER_VALIDATE_INT) === false ||
        filter_var($item['quantity'], FILTER_VALIDATE_INT) === false ||
        (int) $item['product_id'] < 1 ||
        (int) $item['quantity'] < 1
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'cart' => 'Data produk atau jumlah dalam keranjang tidak valid.'
            ]);
    }

    $productId = (int) $item['product_id'];
    $quantity = (int) $item['quantity'];

    // Gabungkan jumlah jika ID produk yang sama dikirim berulang.
    $normalizedCart[$productId] =
        ($normalizedCart[$productId] ?? 0) + $quantity;
}

// Ubah kembali menjadi format keranjang yang digunakan controller.
$cart = [];

foreach ($normalizedCart as $productId => $quantity) {
    $cart[] = [
        'product_id' => $productId,
        'quantity' => $quantity,
    ];
}



        /*
        |--------------------------------------------------------------------------
        | PROSES DATABASE
        |--------------------------------------------------------------------------
        */

        try {

            $transaction = DB::transaction(
                function () use (
                    $validated,
                    $cart,
                    $tanggalTransaksi
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Ambil ID PRODUK
                    |--------------------------------------------------------------------------
                    */

                    $productIds = collect($cart)
                        ->pluck('product_id')
                        ->map(function ($id) {
                            return (int) $id;
                        })
                        ->unique()
                        ->values();


                    /*
                    |--------------------------------------------------------------------------
                    | Ambil produk dari database
                    |--------------------------------------------------------------------------
                    |
                    | Harga dan stok diambil langsung dari database.
                    | Data dari JavaScript tidak dipercaya untuk harga.
                    |
                    */

                    $products = Product::whereIn(
                            'id',
                            $productIds
                        )
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');


                    /*
                    |--------------------------------------------------------------------------
                    | SIAPKAN ITEM TRANSAKSI
                    |--------------------------------------------------------------------------
                    */

                    $items = [];


                    foreach ($cart as $item) {

                        $productId =
                            (int) $item['product_id'];

                        $quantity =
                            (int) $item['quantity'];


                        /*
                        |--------------------------------------------------------------------------
                        | Produk harus ada
                        |--------------------------------------------------------------------------
                        */

                        if (!isset($products[$productId])) {

                            throw new \Exception(
                                'Produk tidak ditemukan.'
                            );
                        }


                        $product =
                            $products[$productId];


                        /*
                        |--------------------------------------------------------------------------
                        | Cek stok
                        |--------------------------------------------------------------------------
                        */

                        if ($quantity > $product->stok) {

                            throw new \Exception('Stok produk "' . $product->nama .
                                '" tidak mencukupi. ' . 'Stok tersedia: ' . $product->stok
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Ambil harga dari database
                        |--------------------------------------------------------------------------
                        */

                        $harga =
                            (float) $product->harga_jual;


                        /*
                        |--------------------------------------------------------------------------
                        | Hitung subtotal
                        |--------------------------------------------------------------------------
                        */

                        $subtotal =
                            $harga * $quantity;


                        $items[] = [

                            'product' =>
                                $product,

                            'quantity' =>
                                $quantity,

                            'harga' =>
                                $harga,

                            'subtotal' =>
                                $subtotal,
                        ];
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HITUNG TOTAL
                    |--------------------------------------------------------------------------
                    */

                    $total = collect($items)
                        ->sum('subtotal');


                    $diskon =
                        (float) $validated['diskon'];


                    /*
                    |--------------------------------------------------------------------------
                    | Diskon tidak boleh lebih besar dari total
                    |--------------------------------------------------------------------------
                    */

                    if ($diskon > $total) {

                        throw new \Exception(
                            'Diskon tidak boleh lebih besar dari total transaksi.'
                        );
                    }


                    $totalAkhir =
                        $total - $diskon;


                    $jumlahBayar =
                        (float) $validated['jumlah_bayar'];


                    $statusPembayaran =
                        $validated['status_pembayaran'];


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI PEMBAYARAN LUNAS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $statusPembayaran === 'lunas' &&
                        $jumlahBayar < $totalAkhir
                    ) {

                        throw new \Exception(
                            'Jumlah bayar belum mencukupi untuk pembayaran lunas.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI PEMBAYARAN DP
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $statusPembayaran === 'dp' &&
                        $jumlahBayar <= 0
                    ) {

                        throw new \Exception(
                            'Jumlah pembayaran DP harus lebih dari 0.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DP tidak boleh melebihi total
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $statusPembayaran === 'dp' &&
                        $jumlahBayar > $totalAkhir
                    ) {

                        throw new \Exception(
                            'Jumlah DP tidak boleh lebih besar dari total transaksi.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GENERATE NOMOR INVOICE
                    |--------------------------------------------------------------------------
                    */

                    do {

                        $nomorInvoice =
                            'INV-' .
                            now()->format('YmdHis') .
                            '-' .
                            random_int(100, 999);

                    } while (
                        Transaction::where(
                            'nomor_invoice',
                            $nomorInvoice
                        )->exists()
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN TRANSAKSI UTAMA
                    |--------------------------------------------------------------------------
                    */

                    $transaction =
                        Transaction::create([

                            'nomor_invoice' =>
                                $nomorInvoice,

                            'pelanggan_id' =>
                                $validated['pelanggan_id'] ?? null,

                            'dibuat_oleh' =>
                                Auth::id(),

                            'tanggal_transaksi' =>
                                $tanggalTransaksi,

                            'total' =>
                                $total,

                            'diskon' =>
                                $diskon,

                            'total_akhir' =>
                                $totalAkhir,

                            'jumlah_bayar' =>
                                $jumlahBayar,

                            'metode_pembayaran' =>
                                $validated[
                                    'metode_pembayaran'
                                ],

                            'status_pembayaran' =>
                                $statusPembayaran,
                        ]);


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPAN DETAIL TRANSAKSI
                    |--------------------------------------------------------------------------
                    */

                    foreach ($items as $item) {

                        $transaction
                            ->details()
                            ->create([

                                'product_id' =>
                                    $item['product']->id,

                                'quantity' =>
                                    $item['quantity'],

                                'harga' =>
                                    $item['harga'],

                                'subtotal' =>
                                    $item['subtotal'],
                            ]);


                        /*
                        |--------------------------------------------------------------------------
                        | KURANGI STOK
                        |--------------------------------------------------------------------------
                        */

                        $item['product']->decrement(
                            'stok',
                            $item['quantity']
                        );

                        //Catat Riwayat Stok Keluar
                        StockMovement::create([
                            'product_id' => $item['product']->id,
                            'supplier_id' => null,
                            'user_id' => Auth::id(),
                            'jenis' => 'keluar',
                            'jumlah' => $item['quantity'],
                            'tanggal_stok' => $transaction->tanggal_transaksi,
                            'keterangan' => 'Penjualan ' . $transaction->nomor_invoice,
                            ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Kembalikan transaksi
                    |--------------------------------------------------------------------------
                    */

                    return $transaction;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | BERHASIL
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'transactions.show',
                    $transaction
                )
                ->with(
                    'success',
                    'Transaksi berhasil disimpan.'
                );


        } catch (\Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | GAGAL
            |--------------------------------------------------------------------------
            */

            return back()
                ->withInput()
                ->withErrors([
                    'transaction' =>
                        $e->getMessage()
                ]);
        }
    }


    /**
     * Detail transaksi
     */
    public function show(Transaction $transaction)
    {
        $transaction->load([
            'customer',
            'user',
            'details.product',
        ]);

        return view(
            'transactions.show',
            compact('transaction')
        );
    }

    //PELUNASAN

    public function pelunasanForm(Transaction $transaction)
    {
    $transaction->load([
        'customer',
        'user',
        'details.product',
    ]);

    if ($transaction->status_pembayaran !== 'dp') {
        return redirect()
            ->route('transactions.show', $transaction)
            ->with('error', 'Transaksi ini sudah lunas.');
    }

    $sisaPembayaran = max(
        $transaction->total_akhir - $transaction->jumlah_bayar,
        0
    );

    return view('transactions.pelunasan', compact(
        'transaction',
        'sisaPembayaran'
    ));
    }


    public function pelunasan(Request $request, Transaction $transaction)
    {
    if ($transaction->status_pembayaran !== 'dp') {
        return redirect()
            ->route('transactions.show', $transaction)
            ->with('error', 'Transaksi ini sudah lunas.');
    }

    $sisaPembayaran = max(
        $transaction->total_akhir - $transaction->jumlah_bayar,
        0
    );

    $validated = $request->validate([
        'metode_pembayaran' => [
            'required',
            'in:tunai,qris,mesin',
        ],

        'jumlah_pelunasan' => [
            'required',
            'numeric',
            'min:' . $sisaPembayaran,
            'max:' . $sisaPembayaran,
        ],
    ], [
        'metode_pembayaran.required' =>
            'Metode pembayaran harus dipilih.',

        'metode_pembayaran.in' =>
            'Metode pembayaran tidak valid.',

        'jumlah_pelunasan.required' =>
            'Jumlah pelunasan harus diisi.',

        'jumlah_pelunasan.min' =>
            'Jumlah pelunasan harus sesuai dengan sisa pembayaran.',

        'jumlah_pelunasan.max' =>
            'Jumlah pelunasan tidak boleh melebihi sisa pembayaran.',
    ]);

    $transaction->update([
        'jumlah_bayar' => $transaction->total_akhir,
        'metode_pembayaran' => $validated['metode_pembayaran'],
        'status_pembayaran' => 'lunas',
    ]);

    return redirect()
        ->route('transactions.show', $transaction)
        ->with('success', 'Pelunasan transaksi berhasil.');
    }

    // LAPORAN PENDAPATAN
    public function laporanPendapatan(Request $request)
    {
        $query = Transaction::with('customer')
            ->latest('tanggal_transaksi');

        // Filter tanggal mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal_transaksi',
                '>=',
                $request->tanggal_mulai
            );
        }

        // Filter tanggal akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal_transaksi',
                '<=',
                $request->tanggal_akhir
            );
        }

        // 1. Hitung Ringkasan Statistik DARI SELURUH DATA yang terfilter (sebelum dipaginate)
        $totalTransaksi = (clone $query)->count();

        $totalRetur = $this->hitungTotalRetur($request);
        $totalPenjualan = (clone $query)->sum('total_akhir') - $totalRetur;

        $totalDibayar   = (clone $query)->sum('jumlah_bayar');

        $totalPiutang   = (clone $query)->get()->sum(function ($transaction) {
            return max(
                $transaction->total_akhir - $transaction->jumlah_bayar,
                0
            );
        });

        // 2. Gunakan PAGINATE (10 data per halaman) dan bawa query filter
        $transactions = $query->paginate(10)->withQueryString();

        return view('owner.laporan.pendapatan', compact(
            'transactions',
            'totalTransaksi',
            'totalPenjualan',
            'totalDibayar',
            'totalPiutang',
            'totalRetur'
        ));
    }

    // CETAK PDF LAPORAN PENDAPATAN
    public function laporanPendapatanPdf(Request $request)
    {
    $query = Transaction::with('customer')
        ->latest('tanggal_transaksi');

    // Filter tanggal mulai
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate(
            'tanggal_transaksi',
            '>=',
            $request->tanggal_mulai
        );
    }

    // Filter tanggal akhir
    if ($request->filled('tanggal_akhir')) {
        $query->whereDate(
            'tanggal_transaksi',
            '<=',
            $request->tanggal_akhir
        );
    }

    $transactions = $query->get();

    $totalTransaksi = $transactions->count();

    $totalRetur = $this->hitungTotalRetur($request);
    $totalPenjualan = $transactions->sum('total_akhir') - $totalRetur;

    $totalDibayar = $transactions->sum('jumlah_bayar');

    $totalPiutang = $transactions->sum(function ($transaction) {
        return max(
            $transaction->total_akhir - $transaction->jumlah_bayar,
            0
        );
    });

    $pdf = Pdf::loadView(
        'owner.laporan.pdf.pendapatan',
        compact(
            'transactions',
            'totalTransaksi',
            'totalPenjualan',
            'totalDibayar',
            'totalPiutang',
            'totalRetur'
        )
    );

    $pdf->setPaper('A4', 'landscape');

    return $pdf->download(
        'laporan-pendapatan.pdf'
    );
    }

    // LAPORAN PENJUALAN
    

public function laporanPenjualan(Request $request)
{
    $search = trim($request->input('search', ''));

    // DETAIL PENJUALAN
    $query = TransactionDetail::with([
        'transaction',
        'product.category',
    ])->whereHas('transaction', function ($q) use ($request) {
        if ($request->filled('tanggal_mulai')) {
            $q->whereDate(
                'tanggal_transaksi',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_akhir')) {
            $q->whereDate(
                'tanggal_transaksi',
                '<=',
                $request->tanggal_akhir
            );
        }
    });

    // Cari berdasarkan invoice ATAU nama produk
    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->whereHas('transaction', function ($t) use ($search) {
                $t->where(
                    'nomor_invoice',
                    'like',
                    '%' . $search . '%'
                );
            })->orWhereHas('product', function ($p) use ($search) {
                $p->where(
                    'nama',
                    'like',
                    '%' . $search . '%'
                );
            });
        });
    }

    $totalTransaksi = (clone $query)
        ->distinct()
        ->count('transaksi_id');

    $totalProdukTerjual = (clone $query)->sum('quantity');

    $totalPenjualanKotor = (float) (clone $query)->sum('subtotal');

    $details = (clone $query)
        ->latest()
        ->paginate(10)
        ->withQueryString();

    // DETAIL RETUR PENJUALAN
    $queryRetur = SalesReturnDetail::with([
        'salesReturn.transaction',
        'product',
    ])->whereHas('salesReturn.transaction', function ($q) use ($request) {
        if ($request->filled('tanggal_mulai')) {
            $q->whereDate(
                'tanggal_transaksi',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_akhir')) {
            $q->whereDate(
                'tanggal_transaksi',
                '<=',
                $request->tanggal_akhir
            );
        }
    });

    // Pencarian yang sama juga berlaku untuk retur
    if ($search !== '') {
        $queryRetur->where(function ($q) use ($search) {
            $q->whereHas('salesReturn.transaction', function ($t) use ($search) {
                $t->where(
                    'nomor_invoice',
                    'like',
                    '%' . $search . '%'
                );
            })->orWhereHas('product', function ($p) use ($search) {
                $p->where(
                    'nama',
                    'like',
                    '%' . $search . '%'
                );
            });
        });
    }

    $returns = (clone $queryRetur)
        ->latest('created_at')
        ->get();

    $totalRetur = (float) (clone $queryRetur)->sum('subtotal');

    $totalPenjualanBersih =
        $totalPenjualanKotor - $totalRetur;

    return view('owner.laporan.penjualan', compact(
        'details',
        'totalTransaksi',
        'totalProdukTerjual',
        'totalPenjualanKotor',
        'totalRetur',
        'totalPenjualanBersih',
        'returns'
    ));
}



    // CETAK PDF LAPORAN PENJUALAN
    
public function laporanPenjualanPdf(Request $request)
{
    $query = TransactionDetail::with([
        'transaction',
        'product.category',
    ])->whereHas('transaction', function ($q) use ($request) {

        if ($request->filled('tanggal_mulai')) {
            $q->whereDate(
                'tanggal_transaksi',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_akhir')) {
            $q->whereDate(
                'tanggal_transaksi',
                '<=',
                $request->tanggal_akhir
            );
        }
    });

    $details = $query->latest()->get();

    $totalTransaksi = $details
        ->pluck('transaksi_id')
        ->unique()
        ->count();

    $totalProdukTerjual = $details->sum('quantity');

    $totalPenjualanKotor = (float) $details->sum('subtotal');

    // Retur dikelompokkan menurut tanggal transaksi asal.
    $queryRetur = SalesReturnDetail::with([
        'salesReturn.transaction',
        'product',
    ])->whereHas('salesReturn.transaction', function ($q) use ($request) {

        if ($request->filled('tanggal_mulai')) {
            $q->whereDate(
                'tanggal_transaksi',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_akhir')) {
            $q->whereDate(
                'tanggal_transaksi',
                '<=',
                $request->tanggal_akhir
            );
        }
    });

    $returns = $queryRetur->latest('created_at')->get();

    $totalRetur = (float) $returns->sum('subtotal');

    $totalPenjualanBersih = $totalPenjualanKotor - $totalRetur;

    $pdf = Pdf::loadView(
        'owner.laporan.pdf.penjualan',
        compact(
            'details',
            'totalTransaksi',
            'totalProdukTerjual',
            'totalPenjualanKotor',
            'totalRetur',
            'totalPenjualanBersih',
            'returns'
        )
    );

    $pdf->setPaper('A4', 'landscape');

    return $pdf->download('laporan-penjualan.pdf');
}

    
/**
 * Menghitung total nilai retur berdasarkan tanggal transaksi asal.
 */
private function hitungTotalRetur(Request $request): float
{
    $query = SalesReturnDetail::whereHas(
        'salesReturn.transaction',
        function ($q) use ($request) {

            if ($request->filled('tanggal_mulai')) {
                $q->whereDate(
                    'tanggal_transaksi',
                    '>=',
                    $request->tanggal_mulai
                );
            }

            if ($request->filled('tanggal_akhir')) {
                $q->whereDate(
                    'tanggal_transaksi',
                    '<=',
                    $request->tanggal_akhir
                );
            }
        }
    );

    return (float) $query->sum('subtotal');
}

}