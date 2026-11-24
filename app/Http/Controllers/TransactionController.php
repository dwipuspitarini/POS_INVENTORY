<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
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

        $products = $products = Product::orderBy('nama')->get([
            'id',
            'sku',
            'nama',
            'harga_jual',
            'stok',
        ]);

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
            'uang_diterima' => [
                'required_if:metode_pembayaran,tunai',
                'nullable',
                'numeric',
                'min:0',
            ],
            'metode_pembayaran' => [
                'required',
                'in:tunai,qris,edc'
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
        $tanggalTransaksi->lt(Carbon::parse('2026-11-01 00:00:00')) ||
        $tanggalTransaksi->gt(Carbon::parse('2026-11-30 23:59:59'))
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'tanggal_transaksi' =>
                    'Tanggal transaksi harus berada pada November 2026.',
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


                    $jumlahBayar = (float) $validated['jumlah_bayar'];

                    $statusPembayaran = $validated['status_pembayaran'];
                    $metodePembayaran = $validated['metode_pembayaran'];
                    // Uang diterima hanya dicatat untuk pembayaran tunai.
                    $uangDiterima = $metodePembayaran === 'tunai'
                    ? (float) $validated['uang_diterima']
                    : null;


// Jumlah pembayaran tidak boleh melebihi total transaksi.
if ($jumlahBayar > $totalAkhir) {
    throw new \Exception(
        'Jumlah pembayaran tidak boleh melebihi total transaksi.'
    );
}


// Pembayaran tunai harus mencukupi jumlah yang dibayarkan.
if (
    $metodePembayaran === 'tunai' &&
    $uangDiterima < $jumlahBayar
) {
    throw new \Exception(
        'Uang diterima tidak boleh kurang dari jumlah pembayaran.'
    );
}


// Pembayaran lunas harus sama persis dengan total transaksi.
if (
    $statusPembayaran === 'lunas' &&
    $jumlahBayar != $totalAkhir
) {
    throw new \Exception(
        'Jumlah pembayaran lunas harus sama dengan total transaksi.'
    );
}


// Pembayaran DP harus lebih dari 0.
if (
    $statusPembayaran === 'dp' &&
    $jumlahBayar <= 0
) {
    throw new \Exception(
        'Jumlah pembayaran DP harus lebih dari 0.'
    );
}


// DP harus lebih kecil daripada total transaksi.
if (
    $statusPembayaran === 'dp' &&
    $jumlahBayar >= $totalAkhir
) {
    throw new \Exception(
        'Jumlah DP harus lebih kecil dari total transaksi. Gunakan status lunas jika pembayaran sudah penuh.'
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

                           'jumlah_bayar' => $jumlahBayar,
                           'jumlah_dp' => $statusPembayaran === 'dp'
                           ? $jumlahBayar
                           : 0,
                           'uang_diterima' => $uangDiterima,
                           'uang_diterima_pelunasan' => null,
                           'metode_pembayaran' => $metodePembayaran,
                           'status_pembayaran' => $statusPembayaran,
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

    $rules = [
        'jumlah_pelunasan' => [
            'required',
            'numeric',
            'min:0',
        ],
    ];

    if ($transaction->metode_pembayaran === 'tunai') {
        $rules['uang_diterima'] = [
            'required',
            'numeric',
            'min:0',
        ];
    }

    $messages = [
        'jumlah_pelunasan.required' =>
            'Jumlah pelunasan harus diisi.',
        'jumlah_pelunasan.numeric' =>
            'Jumlah pelunasan harus berupa angka.',
        'uang_diterima.required' =>
            'Uang yang diterima harus diisi.',
        'uang_diterima.numeric' =>
            'Uang yang diterima harus berupa angka.',
        'uang_diterima.min' =>
            'Uang yang diterima tidak boleh kurang dari 0.',
    ];

    $validated = $request->validate($rules, $messages);

    try {
        DB::transaction(function () use ($transaction, $validated) {

            $transaksi = Transaction::where(
                'id',
                $transaction->id
            )->lockForUpdate()->firstOrFail();

            if ($transaksi->status_pembayaran !== 'dp') {
                throw new \Exception(
                    'Transaksi ini sudah lunas.'
                );
            }

            $sisaPembayaran = max(
                (float) $transaksi->total_akhir -
                (float) $transaksi->jumlah_bayar,
                0
            );

            $jumlahPelunasan = (float) $validated['jumlah_pelunasan'];

            if (round($jumlahPelunasan, 2) !== round($sisaPembayaran, 2)) {
                throw new \Exception(
                    'Jumlah pelunasan harus sesuai dengan sisa pembayaran.'
                );
            }

            $uangDiterimaPelunasan = null;

            if ($transaksi->metode_pembayaran === 'tunai') {
                $uangDiterimaPelunasan =
                    (float) $validated['uang_diterima'];

                if ($uangDiterimaPelunasan < $sisaPembayaran) {
                    throw new \Exception(
                        'Uang diterima tidak boleh kurang dari sisa pembayaran.'
                    );
                }
            }

            // DP awal dan metode pembayaran awal tetap dipertahankan.
            $transaksi->update([
                'jumlah_bayar' => $transaksi->total_akhir,
                'uang_diterima_pelunasan' => $uangDiterimaPelunasan,
                'status_pembayaran' => 'lunas',
            ]);
        });

        return redirect()
            ->route('transactions.show', $transaction)
            ->with('success', 'Pelunasan transaksi berhasil.');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->withErrors([
                'pelunasan' => $e->getMessage(),
            ]);
    }
}



// LAPORAN KASIR


public function laporanKasir(Request $request)
{
    $user = Auth::user();

    // Hanya owner dan kasir yang boleh mengakses laporan.
    if (!in_array($user->role, ['owner', 'kasir'])) {
        abort(403);
    }

    // Validasi filter.
    $request->validate([
        'tanggal_mulai' => ['nullable', 'date'],
        'tanggal_akhir' => [
            'nullable',
            'date',
            'after_or_equal:tanggal_mulai'
        ],
        'kasir_id' => [
            'nullable',
            'integer',
            Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'kasir');
            }),
        ],
    ]);

    // Periode laporan.
    $tanggalMulai = $request->input(
        'tanggal_mulai',
        now()->toDateString()
    );

    $tanggalAkhir = $request->input(
        'tanggal_akhir',
        now()->toDateString()
    );

    // Daftar kasir untuk owner.
    $kasirs = $user->role === 'owner'
        ? User::where('role', 'kasir')
            ->orderBy('name')
            ->get(['id', 'name'])
        : collect();

    // Query transaksi.
    $query = Transaction::with('user')
        ->whereDate('tanggal_transaksi', '>=', $tanggalMulai)
        ->whereDate('tanggal_transaksi', '<=', $tanggalAkhir);

    // Pembatasan hak akses kasir.
    if ($user->role === 'kasir') {
        $query->where('dibuat_oleh', $user->id);
        $kasirId = $user->id;
    } else {
        $kasirId = $request->input('kasir_id');

        if ($kasirId) {
            $query->where('dibuat_oleh', $kasirId);
        }
    }

    $transaksi = $query
        ->orderBy('tanggal_transaksi')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | DATA RETUR
    |--------------------------------------------------------------------------
    |
    | Retur dihitung berdasarkan tanggal retur.
    | Kasir ditentukan berdasarkan kasir pada transaksi aslinya.
    |
    */

    $queryRetur = \App\Models\SalesReturn::with('transaction')
        ->whereDate('tanggal_retur', '>=', $tanggalMulai)
        ->whereDate('tanggal_retur', '<=', $tanggalAkhir)
        ->whereHas('transaction', function ($q) use ($user, $kasirId) {

            if ($user->role === 'kasir') {
                $q->where('dibuat_oleh', $user->id);
            } elseif ($kasirId) {
                $q->where('dibuat_oleh', $kasirId);
            }
        });

    $retur = $queryRetur->get();

    /*
    |--------------------------------------------------------------------------
    | RINGKASAN PENJUALAN
    |--------------------------------------------------------------------------
    */

    // Jumlah transaksi tetap menghitung invoice asli.
    $jumlahTransaksi = $transaksi->count();

    // Penjualan sebelum diskon.
    $totalPenjualanKotor = (float) $transaksi->sum('total');

    // Total diskon.
    $totalDiskon = (float) $transaksi->sum('diskon');

    // Total retur setelah alokasi diskon.
    // Menggunakan jumlah_pengembalian yang sudah dihitung
    // dan disimpan oleh SalesReturnController.
    $totalRetur = (float) $retur->sum('jumlah_pengembalian');

    // Penjualan bersih setelah diskon dan retur.
    $totalPenjualanBersih = max(
        (float) $transaksi->sum('total_akhir') - $totalRetur,
        0
    );

    /*
    |--------------------------------------------------------------------------
    | RINGKASAN METODE PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    $ringkasanMetode = [
        'tunai' => [
            'nama' => 'Tunai',
            'jumlah_transaksi' => 0,
            'total' => 0,
        ],
        'qris' => [
            'nama' => 'QRIS',
            'jumlah_transaksi' => 0,
            'total' => 0,
        ],
        'edc' => [
            'nama' => 'EDC',
            'jumlah_transaksi' => 0,
            'total' => 0,
        ],
    ];

    $totalDibayar = 0;
    $totalSisa = 0;
    $totalTunaiDiterima = 0;
    $totalKembalian = 0;

    foreach ($transaksi as $item) {

        $metode = $item->metode_pembayaran;

        $jumlahBayar = (float) $item->jumlah_bayar;

        $sisa = max(
            (float) $item->total_akhir - $jumlahBayar,
            0
        );

        $totalDibayar += $jumlahBayar;
        $totalSisa += $sisa;

        if (isset($ringkasanMetode[$metode])) {
            $ringkasanMetode[$metode]['jumlah_transaksi']++;
            $ringkasanMetode[$metode]['total'] += $jumlahBayar;
        }

        // Perhitungan uang tunai dan kembalian.
        if ($metode === 'tunai') {

            $jumlahDp = (float) ($item->jumlah_dp ?? 0);

            $pembayaranAwal = $jumlahDp > 0
                ? $jumlahDp
                : $jumlahBayar;

            $jumlahPelunasan = $jumlahDp > 0
                ? max($jumlahBayar - $jumlahDp, 0)
                : 0;

            $uangAwal = (float) ($item->uang_diterima ?? 0);

            $uangPelunasan = (float) (
                $item->uang_diterima_pelunasan ?? 0
            );

            $kembalianAwal = max(
                $uangAwal - $pembayaranAwal,
                0
            );

            $kembalianPelunasan = max(
                $uangPelunasan - $jumlahPelunasan,
                0
            );

            $totalTunaiDiterima += $uangAwal + $uangPelunasan;

            $totalKembalian +=
                $kembalianAwal + $kembalianPelunasan;
        }
    }

    $totalTunaiBersih = max(
        $totalTunaiDiterima - $totalKembalian,
        0
    );

    /*
    |--------------------------------------------------------------------------
    | DATA KE BLADE
    |--------------------------------------------------------------------------
    */

    return view('owner.laporan.kasir', compact(
        'transaksi',
        'retur',
        'jumlahTransaksi',
        'ringkasanMetode',
        'totalPenjualanKotor',
        'totalDiskon',
        'totalRetur',
        'totalPenjualanBersih',
        'totalDibayar',
        'totalSisa',
        'totalTunaiDiterima',
        'totalKembalian',
        'totalTunaiBersih',
        'kasirs',
        'kasirId',
        'tanggalMulai',
        'tanggalAkhir'
    ));
}

public function laporanKasirPdf(Request $request)
{
    $user = Auth::user();

    // Batasi akses hanya untuk owner dan kasir.
    if (!in_array($user->role, ['owner', 'kasir'])) {
        abort(403);
    }

    // Validasi filter.
    $request->validate([
        'tanggal_mulai' => ['nullable', 'date'],
        'tanggal_akhir' => [
            'nullable',
            'date',
            'after_or_equal:tanggal_mulai'
        ],
        'kasir_id' => [
            'nullable',
            'integer',
            Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'kasir');
            }),
        ],
    ]);

    // Periode laporan.
    $tanggalMulai = $request->input(
        'tanggal_mulai',
        now()->toDateString()
    );

    $tanggalAkhir = $request->input(
        'tanggal_akhir',
        now()->toDateString()
    );

    // Query transaksi.
    $query = Transaction::with('user')
        ->whereDate('tanggal_transaksi', '>=', $tanggalMulai)
        ->whereDate('tanggal_transaksi', '<=', $tanggalAkhir);

    // Pembatasan berdasarkan akun.
    if ($user->role === 'kasir') {
        $query->where('dibuat_oleh', $user->id);
        $kasirId = $user->id;
        $namaKasir = $user->name;
    } else {
        $kasirId = $request->input('kasir_id');

        if ($kasirId) {
            $query->where('dibuat_oleh', $kasirId);
            $namaKasir = User::find($kasirId)?->name ?? '-';
        } else {
            $namaKasir = 'Semua Kasir';
        }
    }

    $transaksi = $query
        ->orderBy('tanggal_transaksi')
        ->get();

    // Query retur berdasarkan tanggal retur.
    $queryRetur = \App\Models\SalesReturn::with('transaction')
        ->whereDate('tanggal_retur', '>=', $tanggalMulai)
        ->whereDate('tanggal_retur', '<=', $tanggalAkhir)
        ->whereHas('transaction', function ($q) use ($user, $kasirId) {

            if ($user->role === 'kasir') {
                $q->where('dibuat_oleh', $user->id);
            } elseif ($kasirId) {
                $q->where('dibuat_oleh', $kasirId);
            }
        });

    $retur = $queryRetur
        ->orderBy('tanggal_retur')
        ->get();

    // Ringkasan penjualan.
    $jumlahTransaksi = $transaksi->count();

    $totalPenjualanKotor = (float) $transaksi->sum('total');

    $totalDiskon = (float) $transaksi->sum('diskon');

    $totalRetur = (float) $retur->sum('jumlah_pengembalian');

    $jumlahRetur = $retur->count();

    $totalPenjualanBersih = max(
        (float) $transaksi->sum('total_akhir') - $totalRetur,
        0
    );

    // Ringkasan pembayaran.
    $ringkasanMetode = [
        'tunai' => [
            'nama' => 'Tunai',
            'jumlah_transaksi' => 0,
            'total' => 0,
        ],
        'qris' => [
            'nama' => 'QRIS',
            'jumlah_transaksi' => 0,
            'total' => 0,
        ],
        'edc' => [
            'nama' => 'EDC',
            'jumlah_transaksi' => 0,
            'total' => 0,
        ],
    ];

    foreach ($transaksi as $item) {
        $metode = $item->metode_pembayaran;

        if (isset($ringkasanMetode[$metode])) {
            $ringkasanMetode[$metode]['jumlah_transaksi']++;
            $ringkasanMetode[$metode]['total'] +=
                (float) $item->jumlah_bayar;
        }
    }

    // Buat PDF.
    $pdf = Pdf::loadView('owner.laporan.pdf.kasir', compact(
        'transaksi',
        'retur',
        'jumlahTransaksi',
        'jumlahRetur',
        'totalPenjualanKotor',
        'totalDiskon',
        'totalRetur',
        'totalPenjualanBersih',
        'ringkasanMetode',
        'tanggalMulai',
        'tanggalAkhir',
        'namaKasir'
    ));

    $pdf->setPaper('a4', 'landscape');

    return $pdf->stream(
        'laporan-kasir-' . $tanggalMulai . '-sampai-' . $tanggalAkhir . '.pdf'
    );
}

// LAPORAN PENJUALAN

public function laporanPenjualan(Request $request)
{
    // VALIDASI FILTER
    $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
        'tanggal_mulai' => ['nullable', 'date'],
        'tanggal_akhir' => [
            'nullable',
            'date',
            'after_or_equal:tanggal_mulai',
        ],
        'kasir_id' => [
            'nullable',
            Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'kasir');
            }),
        ],
    ]);

    $search = trim($request->input('search', ''));
    $kasirId = $request->input('kasir_id');

    // DATA KASIR UNTUK FILTER
    $kasirs = User::where('role', 'kasir')
        ->orderBy('name')
        ->get(['id', 'name']);

    // QUERY UTAMA: SATU BARIS UNTUK SATU INVOICE
    $query = Transaction::with([
        'customer',
        'user',
    ]);

    // FILTER TANGGAL
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate(
            'tanggal_transaksi',
            '>=',
            $request->tanggal_mulai
        );
    }

    if ($request->filled('tanggal_akhir')) {
        $query->whereDate(
            'tanggal_transaksi',
            '<=',
            $request->tanggal_akhir
        );
    }

    // FILTER KASIR
    if ($kasirId) {
        $query->where('dibuat_oleh', $kasirId);
    }

    // PENCARIAN INVOICE ATAU NAMA PRODUK
    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('nomor_invoice', 'like', "%{$search}%")
                ->orWhereHas('details.product', function ($product) use ($search) {
                    $product->where('nama', 'like', "%{$search}%");
                });
        });
    }

    // AMBIL ID TRANSAKSI YANG SESUAI FILTER
    $transactionIds = (clone $query)->pluck('id');

    // DATA TABEL UTAMA: PER INVOICE
    $transactions = (clone $query)
        ->latest('tanggal_transaksi')
        ->paginate(10)
        ->withQueryString();

    // AMBIL DETAIL PRODUK UNTUK PERHITUNGAN RINGKASAN
    $semuaDetail = TransactionDetail::with([
        'transaction',
    ])
        ->whereIn('transaksi_id', $transactionIds)
        ->get();

    // AMBIL DATA RETUR DARI TRANSAKSI YANG SAMA
    $returns = SalesReturnDetail::with([
        'salesReturn.transaction.user',
        'transactionDetail.transaction',
        'product',
    ])
        ->whereHas('salesReturn', function ($q) use ($transactionIds) {
            $q->whereIn('transaksi_id', $transactionIds);
        })
        ->get();

    // HITUNG RINGKASAN
    // Tetap menggunakan helper yang sudah ada.
    $ringkasan = $this->hitungRingkasanPenjualan(
        $semuaDetail,
        $returns
    );

    return view('owner.laporan.penjualan', [
        'transactions' => $transactions,
        'kasirs' => $kasirs,
        'kasirId' => $kasirId,
        'returns' => $returns,
        'ringkasan' => $ringkasan,

        // VARIABEL KARTU RINGKASAN
        'totalTransaksi' => $ringkasan['totalTransaksi'],
        'totalProdukTerjual' => $ringkasan['totalProdukTerjual'],
        'totalPenjualanKotor' => $ringkasan['totalPenjualanKotor'],
        'totalDiskon' => $ringkasan['totalDiskon'],
        'totalRetur' => $ringkasan['totalRetur'],
        'totalPenjualanBersih' => $ringkasan['totalPenjualanBersih'],
    ]);
}

public function detailLaporanPenjualan($id)
{
    // AMBIL DATA TRANSAKSI DAN DETAIL PRODUK
    $transaction = Transaction::with([
        'customer',
        'user',
        'details.product.category',
    ])->findOrFail($id);

    // AMBIL DATA RETUR DARI INVOICE TERSEBUT
    $returns = SalesReturnDetail::with([
        'salesReturn',
        'product',
        'transactionDetail',
    ])
        ->whereHas('salesReturn', function ($query) use ($transaction) {
            $query->where('transaksi_id', $transaction->id);
        })
        ->get();

    // HITUNG TOTAL TRANSAKSI
    $total = (float) $transaction->total_akhir;
    $dibayar = (float) $transaction->jumlah_bayar;
    $sisa = max($total - $dibayar, 0);

    return view('owner.laporan.detail-penjualan', compact(
        'transaction',
        'returns',
        'total',
        'dibayar',
        'sisa'
    ));
}


// LAPORAN PENJUALAN PDF

public function laporanPenjualanPdf(Request $request)
{
    // VALIDASI FILTER
    $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
        'tanggal_mulai' => ['nullable', 'date'],
        'tanggal_akhir' => [
            'nullable',
            'date',
            'after_or_equal:tanggal_mulai',
        ],
        'kasir_id' => [
            'nullable',
            Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'kasir');
            }),
        ],
    ]);

    $search = trim($request->input('search', ''));
    $kasirId = $request->input('kasir_id');

    // QUERY TRANSAKSI: SATU BARIS UNTUK SATU INVOICE
    $query = Transaction::with([
        'customer',
        'user',
    ]);

    // FILTER TANGGAL
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate(
            'tanggal_transaksi',
            '>=',
            $request->tanggal_mulai
        );
    }

    if ($request->filled('tanggal_akhir')) {
        $query->whereDate(
            'tanggal_transaksi',
            '<=',
            $request->tanggal_akhir
        );
    }

    // FILTER KASIR
    if ($kasirId) {
        $query->where('dibuat_oleh', $kasirId);
    }

    // PENCARIAN NOMOR INVOICE ATAU NAMA PRODUK
    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('nomor_invoice', 'like', "%{$search}%")
                ->orWhereHas('details.product', function ($product) use ($search) {
                    $product->where('nama', 'like', "%{$search}%");
                });
        });
    }

    // AMBIL SEMUA TRANSAKSI SESUAI FILTER
    $transactions = (clone $query)
        ->orderBy('tanggal_transaksi', 'desc')
        ->get();

    $transactionIds = $transactions->pluck('id');

    // DETAIL PRODUK UNTUK PERHITUNGAN RINGKASAN
    $details = TransactionDetail::with([
        'transaction',
    ])
        ->whereIn('transaksi_id', $transactionIds)
        ->get();

    // DATA RETUR DARI TRANSAKSI YANG SAMA
    $returns = SalesReturnDetail::with([
        'salesReturn.transaction.user',
        'transactionDetail.transaction',
        'product',
    ])
        ->whereHas('salesReturn', function ($q) use ($transactionIds) {
            $q->whereIn('transaksi_id', $transactionIds);
        })
        ->get();

    // HITUNG RINGKASAN MENGGUNAKAN HELPER YANG SUDAH ADA
    $ringkasan = $this->hitungRingkasanPenjualan(
        $details,
        $returns
    );

    // SIAPKAN DATA PDF
    $data = [
        'transactions' => $transactions,
        'details' => $details,
        'returns' => $returns,
        'search' => $search,
        'tanggalMulai' => $request->tanggal_mulai,
        'tanggalAkhir' => $request->tanggal_akhir,
        'kasirId' => $kasirId,

        'totalTransaksi' => $ringkasan['totalTransaksi'],
        'totalProdukTerjual' => $ringkasan['totalProdukTerjual'],
        'totalPenjualanKotor' => $ringkasan['totalPenjualanKotor'],
        'totalDiskon' => $ringkasan['totalDiskon'],
        'totalRetur' => $ringkasan['totalRetur'],
        'totalPenjualanBersih' => $ringkasan['totalPenjualanBersih'],
    ];

    $pdf = Pdf::loadView(
        'owner.laporan.pdf.penjualan',
        $data
    )->setPaper('a4', 'landscape');

    return $pdf->download('laporan-penjualan-pendapatan.pdf');
}




// FUNGSI BANTU PERHITUNGAN RINGKASAN LAPORAN PENJUALAN

private function hitungRingkasanPenjualan(
    Collection $details, 
    Collection $returns): array
{
    // JUMLAH TRANSAKSI
    $totalTransaksi = $details
        ->pluck('transaksi_id')
        ->unique()
        ->count();

    // JUMLAH PRODUK TERJUAL
    $totalProdukTerjual = (int) $details->sum('quantity');

    // TOTAL PENJUALAN KOTOR
    $totalPenjualanKotor = (float) $details->sum('subtotal');

    // TOTAL DISKON YANG DIALOKASIKAN KE PRODUK
    $totalDiskon = (float) $details->sum(function ($detail) {
        $transaksi = $detail->transaction;

        if (!$transaksi || (float) $transaksi->total <= 0) {
            return 0;
        }

        $diskonTransaksi = (float) $transaksi->diskon;
        $subtotalProduk = (float) $detail->subtotal;
        $totalTransaksi = (float) $transaksi->total;

        return round(
            $diskonTransaksi * ($subtotalProduk / $totalTransaksi),
            2
        );
    });

    // TOTAL RETUR KOTOR
    $totalReturKotor = (float) $returns->sum('subtotal');

    // DISKON YANG MENJADI BAGIAN DARI BARANG YANG DIRETUR
    $totalDiskonRetur = (float) $returns->sum(function ($retur) {
        $detailAsli = $retur->transactionDetail;

        if (!$detailAsli) {
            return 0;
        }

        $transaksi = $detailAsli->transaction;

        if (
            !$transaksi ||
            (float) $transaksi->total <= 0 ||
            (int) $detailAsli->quantity <= 0
        ) {
            return 0;
        }

        // Bagian diskon untuk satu baris produk pada transaksi asli
        $diskonProduk = (float) $transaksi->diskon
            * (
                (float) $detailAsli->subtotal
                / (float) $transaksi->total
            );

        // Bagian diskon sesuai jumlah barang yang diretur
        $diskonRetur = $diskonProduk
            * (
                (int) $retur->quantity
                / (int) $detailAsli->quantity
            );

        return round($diskonRetur, 2);
    });

    // NILAI RETUR SETELAH DIKURANGI ALOKASI DISKON
    $totalRetur = round(
        $totalReturKotor - $totalDiskonRetur,
        2
    );

    // PENJUALAN BERSIH
    $totalPenjualanBersih = round(
        $totalPenjualanKotor
        - $totalDiskon
        - $totalReturKotor
        + $totalDiskonRetur,
        2
    );

    return [
        'totalTransaksi' => $totalTransaksi,
        'totalProdukTerjual' => $totalProdukTerjual,
        'totalPenjualanKotor' => round($totalPenjualanKotor, 2),
        'totalDiskon' => round($totalDiskon, 2),
        'totalReturKotor' => round($totalReturKotor, 2),
        'totalDiskonRetur' => round($totalDiskonRetur, 2),
        'totalRetur' => $totalRetur,
        'totalPenjualanBersih' => $totalPenjualanBersih,
    ];
}

/**
 * Menghitung total retur berdasarkan tanggal transaksi asli.
 */
private function hitungTotalRetur(Request $request): float
{
    $query = SalesReturnDetail::whereHas(
        'salesReturn.transaction.user',
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