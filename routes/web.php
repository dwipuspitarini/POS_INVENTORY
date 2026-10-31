<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\SupplierController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\StockOpnameController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalesReturnController;


Route::get('/', function () {
    return redirect()->route('login');
});


// =========================
// LOGIN
// =========================

Route::get('/login', [LoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');


// =========================
// ROLE OWNER
// =========================

Route::middleware(['auth', 'role:owner'])
    ->group(function () {

        // Dashboard Owner
        Route::get('/owner/dashboard', [DashboardController::class, 'owner'])
            ->name('dashboard.owner');


        // Kategori
        Route::resource('owner/categories', CategoryController::class)
            ->except(['show'])
            ->names('owner.categories');


        // Supplier
        Route::resource('owner/suppliers', SupplierController::class)
            ->except(['show'])
            ->names('owner.suppliers');


        // Produk
        Route::resource('owner/products', ProductController::class)
            ->except(['show'])
            ->names('owner.products');


        // =========================
        // LAPORAN
        // =========================

        // Laporan Pendapatan
        Route::get('/laporan/pendapatan', [TransactionController::class, 'laporanPendapatan'])
            ->name('owner.laporan.pendapatan');

        // PDF Pendapatan
        Route::get('/laporan/pendapatan/pdf', [TransactionController::class, 'laporanPendapatanPdf'])
            ->name('owner.laporan.pendapatan.pdf');


        // Laporan Penjualan
        Route::get('/laporan/penjualan', [TransactionController::class, 'laporanPenjualan'])
            ->name('owner.laporan.penjualan');

        // PDF Penjualan
        Route::get('/laporan/penjualan/pdf', [TransactionController::class, 'laporanPenjualanPdf'])
            ->name('owner.laporan.penjualan.pdf');


        // Laporan Stok
        Route::get('/laporan/stok', [StockController::class, 'laporan'])
            ->name('owner.laporan.stok');

        // PDF Stok
        Route::get('/laporan/stok/pdf', [StockController::class, 'laporanPdf'])
            ->name('owner.laporan.stok.pdf');


        // =========================
        // STOK KHUSUS OWNER
        // =========================

        // Tambah Stok
        Route::get('/stocks/tambah', [StockMovementController::class, 'create'])
            ->name('owner.stocks.create');

        Route::post('/stocks/tambah', [StockMovementController::class, 'store'])
            ->name('owner.stocks.store');

        // Riwayat Stok
        Route::get('/stocks/riwayat', [StockMovementController::class, 'index'])
            ->name('owner.stocks.history');



        // =============== STOK OPNAME
        Route::get('/owner/stock-opnames', [StockOpnameController::class, 'index'])
        ->name('owner.stock-opnames.index');

        Route::get('/owner/stock-opnames/create', [StockOpnameController::class, 'create'])
        ->name('owner.stock-opnames.create');

        Route::post('/owner/stock-opnames', [StockOpnameController::class, 'store'])
        ->name('owner.stock-opnames.store');

        Route::get('/owner/stock-opnames/{stockOpname}', [StockOpnameController::class, 'show'])
        ->name('owner.stock-opnames.show');


        // =========================
        // PETUGAS
        // =========================

        Route::get('/petugas', [UserController::class, 'index'])
            ->name('owner.petugas.index');

        Route::get('/petugas/tambah', [UserController::class, 'create'])
            ->name('owner.petugas.create');

        Route::post('/petugas/tambah', [UserController::class, 'store'])
            ->name('owner.petugas.store');

        Route::get('/petugas/{user}/edit', [UserController::class, 'edit'])
            ->name('owner.petugas.edit');

        Route::put('/petugas/{user}', [UserController::class, 'update'])
            ->name('owner.petugas.update');

    });


// =========================
// ROLE KASIR
// =========================

Route::middleware(['auth', 'role:kasir'])
    ->group(function () {

        // Dashboard Kasir
        Route::get('/kasir/dashboard', [DashboardController::class, 'kasir'])
            ->name('dashboard.kasir');
    });


// =========================
// CUSTOMER & TRANSAKSI
// =========================

Route::middleware('auth')
    ->group(function () {

        // Pelanggan
        Route::resource('customers', CustomerController::class)
            ->except(['show']);


        // Transaksi
        Route::resource('transactions', TransactionController::class)
            ->only(['index', 'create', 'store', 'show']);


        // Pelunasan
        Route::get(
            '/transactions/{transaction}/pelunasan',
            [TransactionController::class, 'pelunasanForm']
        )->name('transactions.pelunasan.form');

        Route::post(
            '/transactions/{transaction}/pelunasan',
            [TransactionController::class, 'pelunasan']
        )->name('transactions.pelunasan');


        // Daftar Stok
        // Bisa dilihat Owner dan Kasir
        Route::get('/stocks', [StockController::class, 'index'])
            ->name('stocks.index');

        // Retur Penjualan
        Route::resource('sales-returns', SalesReturnController::class)
            ->only(['index', 'create', 'store', 'show']);
    });