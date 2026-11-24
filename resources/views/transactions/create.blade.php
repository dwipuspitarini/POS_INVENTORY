@extends('layouts.app')

@section('title', 'Transaksi Baru')
@section('page-title', 'Transaksi Baru')

@section('content')
<div class="container-fluid transaction-create-page">

<form id="transaction-form"
      action="{{ route('transactions.store') }}"
      method="POST">

    @csrf
    <input type="hidden" name="cart" id="cart-data">

    {{-- =========================================================
         ERROR SERVER
    ========================================================== --}}
    @if($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 8px;">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Transaksi Penjualan
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Buat transaksi penjualan baru
            </p>
        </div>

        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================================================
         DATA PELANGGAN & TANGGAL (JIKA AKTIF)
    ========================================================== --}}
    <div class="form-card mb-20">
        <div class="section-title mb-4">
            Informasi Transaksi
        </div>

        <div class="{{ config('transactions.allow_backdate', false) ? 'info-grid-2' : '' }}">
            {{-- SEARCH PELANGGAN (DEFAULT: PELANGGAN UMUM) --}}
            <div class="form-group mb-0">
                <label for="customer-search" class="form-label">
                    Pelanggan
                </label>

                <div class="custom-search-wrapper" id="customer-search-wrapper">
                    <div class="search-input-box">
                        <span class="search-icon">👤</span>
                        <input
                            type="text"
                            id="customer-search"
                            class="form-control"
                            value="Pelanggan Umum"
                            placeholder="Cari nama atau nomor telepon pelanggan..."
                            autocomplete="off"
                        >
                        <button type="button" id="reset-customer-btn" class="clear-search-btn" title="Set ke Pelanggan Umum">
                            ✕
                        </button>
                    </div>

                    <input type="hidden" id="pelanggan_id" name="pelanggan_id" value="{{ old('pelanggan_id', '') }}">

                    <div id="customer-results" class="custom-dropdown-results">
                        <div class="dropdown-result-item customer-result-item active-default"
                             data-id=""
                             data-label="Pelanggan Umum"
                             data-search="pelanggan umum non member">
                            <div class="result-title">Pelanggan Umum</div>
                            <div class="result-sub">Pembeli reguler / non-member (Default)</div>
                        </div>

                        @foreach($customers as $customer)
                            <div class="dropdown-result-item customer-result-item"
                                 data-id="{{ $customer->id }}"
                                 data-label="{{ $customer->nama }} - {{ $customer->telepon }}"
                                 data-search="{{ strtolower($customer->nama . ' ' . $customer->telepon) }}">
                                <div class="result-title">{{ $customer->nama }}</div>
                                <div class="result-sub">Telepon: {{ $customer->telepon }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TANGGAL TRANSAKSI HISTORIS --}}
            @if(config('transactions.allow_backdate', false))
                <div class="form-group mb-0">
                    <label for="tanggal_transaksi" class="form-label">
                        Tanggal dan Waktu Penjualan
                    </label>

                    <input
                        type="datetime-local"
                        id="tanggal_transaksi"
                        name="tanggal_transaksi"
                        class="form-control"
                        value="{{ old('tanggal_transaksi') }}"
                        min="2026-11-01T00:00"
                        max="2026-11-30T23:59"
                        required
                    >
                </div>
            @endif
        </div>
    </div>

    {{-- =========================================================
         TAMBAH PRODUK (INSTANT SEARCH FULL WIDTH)
    ========================================================== --}}
    <div class="form-card mb-20">
        <div class="section-title mb-4">
            Tambah Produk
        </div>

        <div class="form-group mb-0">
            <label for="product-search" class="form-label">
                Cari Produk
            </label>

            <div class="custom-search-wrapper" id="product-search-wrapper">
                <div class="search-input-box">
                    <span class="search-icon">🔍</span>
                    <input
                        type="text"
                        id="product-search"
                        class="form-control"
                        placeholder="Ketik SKU atau nama produk untuk langsung menambahkan ke keranjang..."
                        autocomplete="off"
                    >
                </div>

                <div id="product-results" class="custom-dropdown-results"></div>
            </div>
        </div>
    </div>

    {{-- =========================================================
         KERANJANG BELANJA
    ========================================================== --}}
    <div class="table-card mb-20">
        <div class="table-card-header">
            <h2>Keranjang Belanja</h2>
        </div>

        <div class="card-body-wrapper" style="padding: 24px;">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 55px;">No</th>
                            <th style="min-width: 220px;">Produk</th>
                            <th style="min-width: 140px;">Harga</th>
                            <th style="width: 140px; text-align: center;">Jumlah</th>
                            <th style="min-width: 150px;">Subtotal</th>
                            <th style="width: 110px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="cart-body">
                        <tr>
                            <td colspan="6" class="empty-data">
                                Belum ada produk dalam keranjang.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================================================
         BAGIAN BAWAH: RINGKASAN & PEMBAYARAN
    ========================================================== --}}
    <div class="transaction-bottom-grid">

        {{-- RINGKASAN TRANSAKSI --}}
        <div class="form-card">
            <div class="section-title mb-4">
                Ringkasan Transaksi
            </div>

            <div class="summary-row">
                <span>Total</span>
                <strong id="display-total" style="font-size: 16px;">Rp 0</strong>
            </div>

            <div class="form-group mt-3">
                <label for="diskon" class="form-label">
                    Diskon (Rp)
                </label>
                <input
                    type="number"
                    id="diskon"
                    name="diskon"
                    class="form-control auto-select-number"
                    value="{{ old('diskon', 0) }}"
                    min="0"
                >
            </div>

            <div class="summary-row total-row">
                <span>Total Akhir</span>
                <strong id="display-total-akhir" style="font-size: 20px; color: #1f2937;">Rp 0</strong>
            </div>
        </div>

        {{-- PEMBAYARAN --}}
        <div class="form-card">
            <div class="section-title mb-4">
                Pembayaran
            </div>

            <div class="info-grid-2">
                {{-- METODE --}}
                <div class="form-group">
                    <label for="metode_pembayaran" class="form-label">
                        Metode Pembayaran
                    </label>
                    <select id="metode_pembayaran" name="metode_pembayaran" class="form-control">
                        <option value="tunai">Tunai</option>
                        <option value="qris">QRIS</option>
                        <option value="edc">EDC</option>
                    </select>
                </div>

                {{-- STATUS --}}
                <div class="form-group">
                    <label for="status_pembayaran" class="form-label">
                        Status Pembayaran
                    </label>
                    <select id="status_pembayaran" name="status_pembayaran" class="form-control">
                        <option value="lunas">Lunas</option>
                        <option value="dp">DP</option>
                    </select>
                </div>
            </div>

            {{-- JUMLAH PEMBAYARAN YANG DICATAT KE TRANSAKSI --}}
            <div class="form-group">
                <label for="jumlah_bayar" class="form-label">
                    Jumlah Pembayaran (Rp)
                </label>
                <input
                    type="number"
                    id="jumlah_bayar"
                    name="jumlah_bayar"
                    class="form-control auto-select-number"
                    min="0"
                    step="0.01"
                    value="{{ old('jumlah_bayar', 0) }}"
                >
            </div>

            {{-- UANG TUNAI YANG DITERIMA --}}
            <div class="form-group" id="uang-diterima-wrapper">
                <label for="uang_diterima" class="form-label">
                    Uang Diterima (Rp)
                </label>
                <input
                    type="number"
                    id="uang_diterima"
                    name="uang_diterima"
                    class="form-control auto-select-number"
                    min="0"
                    step="0.01"
                    value="{{ old('uang_diterima', 0) }}"
                >
            </div>

            {{-- INFORMASI PEMBAYARAN --}}
            <div class="payment-info-box mb-4">
                <div class="summary-row">
                    <span>Total Akhir</span>
                    <strong id="payment-total">Rp 0</strong>
                </div>

                <div class="summary-row">
                    <span>Kembalian</span>
                    <strong id="display-kembalian" style="color: #065f46;">Rp 0</strong>
                </div>

                <div class="summary-row" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
                    <span>Sisa Pembayaran</span>
                    <strong id="display-sisa" style="color: #991b1b;">Rp 0</strong>
                </div>
            </div>

            {{-- ERROR PEMBAYARAN --}}
            <div id="payment-error" class="form-error mb-3" style="display: none;"></div>

            {{-- SIMPAN --}}
            <button
                type="submit"
                id="save-transaction"
                class="btn btn-primary w-100"
                style="background: #1f2937; border-color: #1f2937; width: 100%; padding: 12px 16px; font-size: 15px; font-weight: 600; justify-content: center;"
            >
                Simpan Transaksi
            </button>
        </div>

    </div>

</form>
</div>

@push('styles')
<style>
.transaction-create-page,
.transaction-create-page input,
.transaction-create-page select,
.transaction-create-page textarea,
.transaction-create-page button,
.transaction-create-page .custom-dropdown-results,
.transaction-create-page .dropdown-result-item,
.transaction-create-page .result-title,
.transaction-create-page .result-sub {
    font-family: inherit;
}

.transaction-create-page .mb-20 {
    margin-bottom: 20px;
}

/* =========================================================
   CARDS & LAYOUT GRIDS
========================================================= */
.form-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 24px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    padding-bottom: 14px;
    border-bottom: 1px solid #f3f4f6;
}

.info-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.transaction-bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 24px;
    align-items: start;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 15px;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 11px 14px;
    font-size: 15px;
    color: #1f2937;
    background-color: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.form-control:focus {
    border-color: #1f2937;
    outline: none;
    box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
}

/* Hilangkan panah atas-bawah (spinner) pada input Diskon & Jumlah Bayar */
.auto-select-number::-webkit-outer-spin-button,
.auto-select-number::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.auto-select-number {
    -moz-appearance: textfield;
}

.form-help {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
}

.form-error {
    display: block;
    font-size: 14px;
    color: #dc3545;
    font-weight: 500;
}

/* =========================================================
   CUSTOM SEARCH DROPDOWNS (PELANGGAN & PRODUK)
========================================================= */
.custom-search-wrapper {
    position: relative;
}

.search-input-box {
    position: relative;
}

.search-input-box .form-control {
    padding-left: 42px;
    padding-right: 38px;
    height: 44px;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #6b7280;
    z-index: 2;
    pointer-events: none;
}

.clear-search-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #f3f4f6;
    border: none;
    color: #6b7280;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.clear-search-btn:hover {
    background: #e5e7eb;
    color: #1f2937;
}

.custom-dropdown-results {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    max-height: 270px;
    overflow-y: auto;
    z-index: 200;
    display: none;
}

.dropdown-result-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.12s ease;
}

.dropdown-result-item:last-child {
    border-bottom: none;
}

.dropdown-result-item:hover {
    background: #f3f4f6;
}

.dropdown-result-item.active-default {
    background: #f9fafb;
}

/* PRODUK HABIS */
.dropdown-result-item.product-out-of-stock {
    background-color: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
    opacity: 0.75;
}

.dropdown-result-item.product-out-of-stock .result-title {
    color: #9ca3af;
}

.dropdown-result-item.product-out-of-stock .result-sub {
    color: #9ca3af;
}

.dropdown-result-item.product-out-of-stock:hover {
    background-color: #f3f4f6;
}

.result-title {
    font-weight: 600;
    font-size: 15px;
    color: #1f2937;
    line-height: 1.4;
}

.result-sub {
    font-size: 13px;
    color: #6b7280;
    margin-top: 3px;
    line-height: 1.4;
}

/* =========================================================
   TABLE STYLING
========================================================= */
.table-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.table-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background-color: #ffffff;
}

.table-card-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    padding: 14px 16px;
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    white-space: nowrap;
}

.data-table td {
    padding: 14px 16px;
    font-size: 15px;
    color: #1f2937;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.empty-data {
    text-align: center;
    padding: 40px 20px !important;
    font-size: 15px;
    color: #6b7280;
}

.cart-quantity {
    width: 95px;
    padding: 8px 10px;
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    margin: 0 auto;
    display: block;
    transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
}

.cart-quantity:focus {
    border-color: #1f2937;
    outline: none;
    box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
}

/* Status merah ketika jumlah melebihi stok tersedia */
.cart-quantity.over-stock {
    border-color: #dc2626 !important;
    background-color: #fef2f2 !important;
    color: #dc2626 !important;
}

.cart-quantity.over-stock:focus {
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.18) !important;
}

/* =========================================================
   SUMMARY & PAYMENT BOX
========================================================= */
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    margin-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 15px;
    color: #374151;
}

.total-row {
    margin-top: 16px;
    padding-top: 14px;
    padding-bottom: 0;
    margin-bottom: 0;
    border-top: 2px solid #e5e7eb;
    border-bottom: none;
    font-weight: 700;
}

.payment-info-box {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
}

/* Jumlah pembayaran otomatis saat status lunas */
.payment-readonly {
    background-color: #e5e7eb !important;
    color: #6b7280 !important;
    cursor: not-allowed;
}

@media (max-width: 992px) {
    .transaction-bottom-grid,
    .info-grid-2 {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let cart = [];
    let isManualBayar = false;
    let isManualUangDiterima = {{ old('uang_diterima') !== null ? 'true' : 'false' }};
    const products = @json($products);

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
    }

    // Auto-select angka saat diklik/fokus & cegah perubahan angka saat scroll mouse
    document.querySelectorAll('.auto-select-number').forEach(function (el) {
        el.addEventListener('focus', function () {
            this.select();
        });
        el.addEventListener('mouseup', function (e) {
            e.preventDefault();
        });
        el.addEventListener('wheel', function (e) {
            e.preventDefault();
        }, { passive: false });
    });

    // =========================
    // SINKRONISASI WAKTU LOKAL (JIKA BACKDATE AKTIF)
    // =========================
    const tanggalTransaksiInput = document.getElementById('tanggal_transaksi');
    if (tanggalTransaksiInput && !tanggalTransaksiInput.value) {
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        tanggalTransaksiInput.value = `2026-11-${day}T${hours}:${minutes}`;
    }

    // =========================
    // SEARCH PELANGGAN (DEFAULT PELANGGAN UMUM)
    // =========================
    const customerSearch = document.getElementById('customer-search');
    const customerResults = document.getElementById('customer-results');
    const pelangganIdInput = document.getElementById('pelanggan_id');
    const resetCustomerBtn = document.getElementById('reset-customer-btn');
    const customerItems = document.querySelectorAll('.customer-result-item');

    if (pelangganIdInput.value) {
        customerItems.forEach(item => {
            if (item.dataset.id === pelangganIdInput.value) {
                customerSearch.value = item.dataset.label;
            }
        });
    }

    function filterCustomers(keyword) {
        const query = keyword.toLowerCase().trim();
        let hasVisible = false;

        customerItems.forEach(item => {
            if (item.dataset.id === '') {
                item.style.display = 'block';
                hasVisible = true;
                return;
            }

            if (query === '' || query === 'pelanggan umum' || item.dataset.search.includes(query)) {
                item.style.display = 'block';
                hasVisible = true;
            } else {
                item.style.display = 'none';
            }
        });

        customerResults.style.display = hasVisible ? 'block' : 'none';
    }

    customerSearch.addEventListener('focus', function () {
        if (this.value === 'Pelanggan Umum') {
            this.select();
        }
        filterCustomers(this.value);
    });

    customerSearch.addEventListener('input', function () {
        pelangganIdInput.value = '';
        filterCustomers(this.value);
    });

    customerSearch.addEventListener('blur', function () {
        setTimeout(() => {
            if (!pelangganIdInput.value && this.value.trim() === '') {
                this.value = 'Pelanggan Umum';
            }
        }, 200);
    });

    customerItems.forEach(item => {
        item.addEventListener('click', function () {
            pelangganIdInput.value = this.dataset.id;
            customerSearch.value = this.dataset.label;
            customerResults.style.display = 'none';
        });
    });

    resetCustomerBtn.addEventListener('click', function () {
        pelangganIdInput.value = '';
        customerSearch.value = 'Pelanggan Umum';
        customerResults.style.display = 'none';
    });

    // =========================
    // ELEMENT PRODUK & KERANJANG
    // =========================
    const productSearch = document.getElementById('product-search');
    const productResults = document.getElementById('product-results');
    const cartBody = document.getElementById('cart-body');
    const diskonInput = document.getElementById('diskon');
    const jumlahBayarInput = document.getElementById('jumlah_bayar');
    const uangDiterimaInput = document.getElementById('uang_diterima');
    const uangDiterimaWrapper = document.getElementById('uang-diterima-wrapper');
    const metodePembayaran = document.getElementById('metode_pembayaran');
    const statusPembayaran = document.getElementById('status_pembayaran');
    const transactionForm = document.getElementById('transaction-form');
    const cartData = document.getElementById('cart-data');

    // =========================
    // PENCARIAN & TAMBAH PRODUK INSTAN (PRIORITAS STOK TERSEDIA)
    // =========================
    productSearch.addEventListener('input', function () {
        const keyword = this.value.trim().toLowerCase();
        productResults.innerHTML = '';

        if (!keyword) {
            productResults.style.display = 'none';
            return;
        }

        const results = products
            .filter(function (product) {
                return (
                    (product.sku && product.sku.toLowerCase().includes(keyword)) ||
                    (product.nama && product.nama.toLowerCase().includes(keyword))
                );
            })
            .sort(function (a, b) {
                const stokA = parseInt(a.stok ?? 0, 10) > 0 ? 1 : 0;
                const stokB = parseInt(b.stok ?? 0, 10) > 0 ? 1 : 0;

                // Prioritaskan produk yang stoknya > 0 di atas, stok habis di bawah
                if (stokB !== stokA) {
                    return stokB - stokA;
                }

                // Jika sama-sama tersedia atau sama-sama habis, urutkan berdasarkan SKU
                return String(a.sku ?? '').localeCompare(String(b.sku ?? ''));
            });

        if (results.length === 0) {
            productResults.innerHTML = `
                <div style="padding: 14px 16px; color: #6b7280; font-size: 14px; text-align: center;">
                    Produk tidak ditemukan.
                </div>
            `;

            productResults.style.display = 'block';
            return;
        }

        results.forEach(function (product) {
            const hargaProduk = parseFloat(product.harga_jual ?? product.harga ?? 0);
            const stokProduk = parseInt(product.stok ?? 0, 10);
            const produkHabis = stokProduk <= 0;

            const item = document.createElement('div');

            item.className = produkHabis
                ? 'dropdown-result-item product-out-of-stock'
                : 'dropdown-result-item';

            item.innerHTML = `
                <div class="result-title">
                    ${product.sku ? product.sku + ' — ' : ''}${product.nama}
                </div>

                <div class="result-sub">
                    ${formatRupiah(hargaProduk)}
                    ·
                    ${
                        produkHabis
                            ? '<strong>Stok Habis</strong>'
                            : 'Stok tersedia: <strong>' + stokProduk + '</strong>'
                    }
                </div>
            `;

            // Produk hanya bisa diklik jika stok masih tersedia
            if (!produkHabis) {
                item.addEventListener('click', function () {
                    addProductInstant(product);
                });
            }

            productResults.appendChild(item);
        });

        productResults.style.display = 'block';
    });

    function addProductInstant(product) {
        const harga = parseFloat(product.harga_jual ?? product.harga ?? 0);
        const stok = parseInt(product.stok ?? 0, 10);
        if (stok <= 0) {
            return;
        }

        const existingProduct = cart.find(item => item.product_id == product.id);

        if (existingProduct) {
            const newQuantity = existingProduct.quantity + 1;
            existingProduct.quantity = newQuantity;
            existingProduct.subtotal = existingProduct.harga * newQuantity;
        } else {
            cart.push({
                product_id: product.id,
                sku: product.sku ?? '-',
                nama: product.nama,
                harga: harga,
                quantity: 1,
                subtotal: harga,
                stok: stok
            });
        }

        isManualBayar = false;

        productSearch.value = '';
        productResults.style.display = 'none';

        renderCart();
        productSearch.focus();
    }

    // =========================
    // TAMPILKAN KERANJANG
    // =========================
    function renderCart() {
        cartBody.innerHTML = '';

        if (cart.length === 0) {
            cartBody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-data">
                        Belum ada produk dalam keranjang.
                    </td>
                </tr>
            `;
            isManualBayar = false;
            updateSummary();
            return;
        }

        cart.forEach(function (item, index) {
            const isOverStock = item.quantity > item.stok;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>
                    <strong>${item.nama}</strong>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 3px;">
                        SKU: ${item.sku} · Stok: ${item.stok}
                    </div>
                </td>
                <td>${formatRupiah(item.harga)}</td>
                <td style="text-align: center;">
                    <input
                        type="number"
                        min="1"
                        value="${item.quantity}"
                        class="cart-quantity ${isOverStock ? 'over-stock' : ''}"
                        data-index="${index}"
                    >
                </td>
                <td>
                    <strong id="subtotal-cell-${index}">${formatRupiah(item.subtotal)}</strong>
                </td>
                <td style="text-align: center;">
                    <button
                        type="button"
                        class="btn btn-danger btn-sm remove-product"
                        data-index="${index}"
                        style="padding: 6px 12px; font-size: 13px; font-weight: 600; border-radius: 6px;"
                    >
                        Hapus
                    </button>
                </td>
            `;
            cartBody.appendChild(row);
        });

        // AUTO-BLOCK ANGKA SAAT FOKUS & UPDATE REAL-TIME
        document.querySelectorAll('.cart-quantity').forEach(function (input) {
            input.addEventListener('focus', function () {
                this.select();
            });

            input.addEventListener('mouseup', function (e) {
                e.preventDefault();
            });

            input.addEventListener('input', function () {
                const index = parseInt(this.dataset.index, 10);
                let qty = parseInt(this.value, 10);

                if (isNaN(qty) || qty < 1) {
                    qty = 0;
                }

                // Tandai merah jika melebihi stok tanpa mengubah angka ketikan
                if (qty > cart[index].stok) {
                    this.classList.add('over-stock');
                } else {
                    this.classList.remove('over-stock');
                }

                cart[index].quantity = qty;
                cart[index].subtotal = cart[index].harga * qty;

                const subtotalCell = document.getElementById(`subtotal-cell-${index}`);
                if (subtotalCell) {
                    subtotalCell.textContent = formatRupiah(cart[index].subtotal);
                }

                isManualBayar = false;
                updateSummary();
            });

            input.addEventListener('blur', function () {
                const index = parseInt(this.dataset.index, 10);
                let qty = parseInt(this.value, 10);

                if (isNaN(qty) || qty < 1) {
                    qty = 1;
                    this.value = 1;
                }

                if (qty > cart[index].stok) {
                    this.classList.add('over-stock');
                } else {
                    this.classList.remove('over-stock');
                }

                cart[index].quantity = qty;
                cart[index].subtotal = cart[index].harga * qty;

                const subtotalCell = document.getElementById(`subtotal-cell-${index}`);
                if (subtotalCell) {
                    subtotalCell.textContent = formatRupiah(cart[index].subtotal);
                }

                isManualBayar = false;
                updateSummary();
            });
        });

        // HAPUS PRODUK
        document.querySelectorAll('.remove-product').forEach(function (button) {
            button.addEventListener('click', function () {
                const index = parseInt(this.dataset.index, 10);
                cart.splice(index, 1);
                isManualBayar = false;
                renderCart();
                productSearch.focus();
            });
        });

        updateSummary();
    }

    // =========================
    // TOTAL & RINGKASAN
    // =========================
    function getTotal() {
        return cart.reduce(function (total, item) {
            return total + item.subtotal;
        }, 0);
    }

    function updateSummary() {
        const total = getTotal();
        let diskon = parseFloat(diskonInput.value) || 0;

        if (diskon < 0) diskon = 0;
        if (diskon > total) {
            diskon = total;
            diskonInput.value = total;
        }

        const totalAkhir = Math.max(total - diskon, 0);

        document.getElementById('display-total').textContent = formatRupiah(total);
        document.getElementById('display-total-akhir').textContent = formatRupiah(totalAkhir);
        document.getElementById('payment-total').textContent = formatRupiah(totalAkhir);

        if (statusPembayaran.value === 'lunas') {
            jumlahBayarInput.value = totalAkhir;
            isManualBayar = false;
        } else if (!isManualBayar && !jumlahBayarInput.value) {
            jumlahBayarInput.value = 0;
        }

        if (!isManualUangDiterima) {
            uangDiterimaInput.value = jumlahBayarInput.value || 0;
        }

        updatePayment();
    }

    function updatePayment() {
        const total = getTotal();
        const diskon = parseFloat(diskonInput.value) || 0;
        const totalAkhir = Math.max(total - diskon, 0);
        const jumlahBayar = Math.max(parseFloat(jumlahBayarInput.value) || 0, 0);
        const metode = metodePembayaran.value;

        jumlahBayarInput.readOnly = statusPembayaran.value === 'lunas';

        jumlahBayarInput.classList.toggle(
            'payment-readonly',
            statusPembayaran.value === 'lunas'
        );

        let kembalian = 0;
        const sisa = Math.max(totalAkhir - jumlahBayar, 0);

        if (metode === 'tunai') {
            uangDiterimaWrapper.style.display = 'block';
            const uangDiterima = Math.max(parseFloat(uangDiterimaInput.value) || 0, 0);
            kembalian = Math.max(uangDiterima - jumlahBayar, 0);
        } else {
            uangDiterimaWrapper.style.display = 'none';
        }

        document.getElementById('display-kembalian').textContent = formatRupiah(kembalian);
        document.getElementById('display-sisa').textContent = formatRupiah(sisa);
    }

    diskonInput.addEventListener('input', function () {
        updateSummary();
    });

    jumlahBayarInput.addEventListener('input', function () {
        isManualBayar = true;
        if (!isManualUangDiterima) {
            uangDiterimaInput.value = this.value || 0;
        }
        updatePayment();
    });

    uangDiterimaInput.addEventListener('input', function () {
        isManualUangDiterima = true;
        updatePayment();
    });

    metodePembayaran.addEventListener('change', function () {
        updatePayment();
    });

    statusPembayaran.addEventListener('change', function () {
        isManualBayar = false;
        isManualUangDiterima = false;

        if (this.value === 'lunas') {
            jumlahBayarInput.value = Math.max(getTotal() - (parseFloat(diskonInput.value) || 0), 0);
        } else {
            jumlahBayarInput.value = 0;
        }

        uangDiterimaInput.value = jumlahBayarInput.value;
        updateSummary();
    });

    // =========================
    // KLIK DI LUAR DROPDOWN
    // =========================
    document.addEventListener('click', function (event) {
        if (!event.target.closest('#customer-search-wrapper')) {
            customerResults.style.display = 'none';
        }
        if (!event.target.closest('#product-search-wrapper')) {
            productResults.style.display = 'none';
        }
    });

    // =========================
    // SUBMIT TRANSAKSI
    // =========================
    transactionForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const paymentError = document.getElementById('payment-error');
        paymentError.style.display = 'none';
        paymentError.textContent = '';

        if (cart.length === 0) {
            paymentError.textContent = 'Keranjang masih kosong. Tambahkan produk terlebih dahulu.';
            paymentError.style.display = 'block';
            return;
        }

        const invalidQty = cart.find(item => item.quantity < 1);
        if (invalidQty) {
            paymentError.textContent = `Jumlah untuk produk "${invalidQty.nama}" minimal 1.`;
            paymentError.style.display = 'block';
            return;
        }

        const overStockItem = cart.find(item => item.quantity > item.stok);
        if (overStockItem) {
            paymentError.textContent = `Jumlah "${overStockItem.nama}" melebihi stok tersedia (${overStockItem.stok}).`;
            paymentError.style.display = 'block';
            return;
        }

        const total = getTotal();
        const diskon = parseFloat(diskonInput.value) || 0;
        const totalAkhir = Math.max(total - diskon, 0);
        const jumlahBayar = parseFloat(jumlahBayarInput.value) || 0;
        const uangDiterima = parseFloat(uangDiterimaInput.value) || 0;
        const status = statusPembayaran.value;
        const metode = metodePembayaran.value;

        if (diskon < 0 || diskon > total) {
            paymentError.textContent = 'Diskon tidak boleh kurang dari 0 atau melebihi total belanja.';
            paymentError.style.display = 'block';
            return;
        }

        if (status === 'lunas' && Math.round(jumlahBayar * 100) !== Math.round(totalAkhir * 100)) {
            paymentError.textContent = 'Untuk pembayaran lunas, jumlah pembayaran harus sama persis dengan total akhir.';
            paymentError.style.display = 'block';
            return;
        }

        if (status === 'dp' && (jumlahBayar <= 0 || jumlahBayar >= totalAkhir)) {
            paymentError.textContent = 'Untuk pembayaran DP, jumlah harus lebih dari 0 dan lebih kecil dari total akhir.';
            paymentError.style.display = 'block';
            return;
        }

        if (metode === 'tunai' && uangDiterima < jumlahBayar) {
            paymentError.textContent = 'Uang diterima tidak boleh kurang dari jumlah pembayaran.';
            paymentError.style.display = 'block';
            return;
        }

        cartData.value = JSON.stringify(
            cart.map(function (item) {
                return {
                    product_id: item.product_id,
                    quantity: item.quantity
                };
            })
        );

        transactionForm.submit();
    });

    renderCart();
});
</script>
@endpush
@endsection