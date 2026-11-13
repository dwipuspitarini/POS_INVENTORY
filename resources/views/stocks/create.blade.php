@extends('layouts.app')

@section('title', 'Tambah Stok')
@section('page-title', 'Tambah Stok')

@section('content')
<div class="container-fluid add-stock-page">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Tambah Stok
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Tambahkan beberapa produk dari satu supplier sekaligus.
            </p>
        </div>

        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================
        INFORMASI STOK
    ========================== --}}
    <div class="form-card mb-20">
        <div class="section-title mb-4">
            Informasi Stok
        </div>

        <div class="stock-info-grid">
            {{-- SUPPLIER --}}
            <div class="form-group mb-0">
                <label for="supplier-search" class="form-label">
                    Supplier <span class="required">*</span>
                </label>

                <div class="supplier-search-wrapper">
                    <input
                        type="text"
                        id="supplier-search"
                        class="form-control"
                        placeholder="Ketik atau pilih supplier..."
                        autocomplete="off"
                    >

                    <input type="hidden" id="supplier-id">

                    <div id="supplier-results" class="supplier-results">
                        @foreach($suppliers as $supplier)
                            <div
                                class="supplier-result-item"
                                data-id="{{ $supplier->id }}"
                                data-name="{{ $supplier->nama }}"
                            >
                                {{ $supplier->nama }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <small id="supplier-error" class="form-error"></small>
            </div>

            {{-- TANGGAL STOK --}}
            <div class="form-group mb-0">
                <label for="tanggal_stok" class="form-label">
                    Tanggal Stok <span class="required">*</span>
                </label>

                <input
                    type="datetime-local"
                    id="tanggal_stok"
                    class="form-control"
                >

                <small class="form-help">
                    Otomatis menyesuaikan tanggal dan waktu lokal saat ini.
                </small>
            </div>
        </div>
    </div>

    {{-- =========================
        PILIH PRODUK
    ========================== --}}
    <div class="form-card mb-20">
        <div class="section-title mb-4">
            Tambahkan Produk
        </div>

        <div class="form-group mb-0">
            <label for="product-search" class="form-label">
                Cari Produk
            </label>

            <div class="product-search-wrapper">
                <div class="product-search-box">
                    <span class="search-icon">🔍</span>
                    <input
                        type="text"
                        id="product-search"
                        class="form-control"
                        placeholder="Ketik nama produk atau kategori..."
                        autocomplete="off"
                    >
                </div>

                <div id="product-results" class="product-results"></div>
            </div>
        </div>
    </div>

    {{-- =========================
        TABEL PRODUK
    ========================== --}}
    <div class="table-card mb-20">
        <div class="table-card-header">
            <h2>Produk yang Akan Ditambahkan</h2>
        </div>

        <div class="card-body-wrapper" style="padding: 24px;">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 55px;">No</th>
                            <th style="min-width: 200px;">Produk</th>
                            <th style="min-width: 140px;">Kategori</th>
                            <th style="width: 130px; text-align: center;">Stok Saat Ini</th>
                            <th style="width: 150px; text-align: center;">Jumlah Masuk</th>
                            <th style="width: 130px; text-align: center;">Stok Setelah</th>
                            <th style="width: 110px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="stock-cart-body">
                        <tr>
                            <td colspan="7" class="empty-cart">
                                Belum ada produk yang dipilih.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================
        KETERANGAN & AKSI
    ========================== --}}
    <div class="form-card">
        <div class="section-title mb-4">
            Keterangan
        </div>

        <div class="form-group">
            <label for="keterangan" class="form-label">
                Keterangan
            </label>

            <textarea
                id="keterangan"
                class="form-control"
                rows="3"
                placeholder="Contoh: Stok barang datang dari supplier."
            ></textarea>
        </div>

        <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                Batal
            </a>

            <button
                type="button"
                id="save-stock"
                class="btn btn-primary"
                style="background: #1f2937; border-color: #1f2937;"
            >
                Simpan Stok
            </button>
        </div>
    </div>

</div>

{{-- =========================
    FORM TERSEMBUNYI
========================== --}}
<form
    action="{{ route('owner.stocks.store') }}"
    method="POST"
    id="stock-form"
    style="display: none;"
>
    @csrf
    <input type="hidden" name="supplier_id" id="hidden-supplier-id">
    <input type="hidden" name="tanggal_stok" id="hidden-tanggal-stok">
    <input type="hidden" name="keterangan" id="hidden-keterangan">
    <div id="hidden-items"></div>
</form>

@push('styles')
<style>
.add-stock-page .mb-20 {
    margin-bottom: 20px;
}

.required {
    color: #dc3545;
}

/* =========================================================
   FORM CARD & GRID
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

.stock-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
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

.form-help {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
}

.form-error {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #dc3545;
    font-weight: 500;
}

/* =========================================================
   SUPPLIER & PRODUCT SEARCH DROPDOWNS
========================================================= */
.supplier-search-wrapper,
.product-search-wrapper {
    position: relative;
}

.product-search-box {
    position: relative;
}

.product-search-box .form-control {
    padding-left: 42px;
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

.supplier-results,
.product-results {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    max-height: 260px;
    overflow-y: auto;
    z-index: 200;
    display: none;
}

.supplier-result-item,
.product-result-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    font-size: 15px;
    transition: background 0.12s ease;
}

.supplier-result-item:last-child,
.product-result-item:last-child {
    border-bottom: none;
}

.supplier-result-item:hover,
.product-result-item:hover {
    background: #f3f4f6;
}

.product-result-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.product-result-info {
    font-size: 13px;
    color: #6b7280;
}

.no-product-result {
    padding: 16px;
    text-align: center;
    font-size: 14px;
    color: #6b7280;
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

.empty-cart {
    text-align: center;
    padding: 40px 20px !important;
    font-size: 15px;
    color: #6b7280;
}

.stock-quantity-input {
    width: 100px;
    padding: 8px 10px;
    text-align: center;
    margin: 0 auto;
    font-weight: 600;
}

.stock-after-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    background: #d1fae5;
    color: #065f46;
}

@media (max-width: 800px) {
    .stock-info-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */
    const supplierSearch = document.getElementById('supplier-search');
    const supplierId = document.getElementById('supplier-id');
    const supplierResults = document.getElementById('supplier-results');
    const supplierError = document.getElementById('supplier-error');

    const productSearch = document.getElementById('product-search');
    const productResults = document.getElementById('product-results');
    const stockCartBody = document.getElementById('stock-cart-body');

    const tanggalStok = document.getElementById('tanggal_stok');
    const keterangan = document.getElementById('keterangan');
    const saveButton = document.getElementById('save-stock');
    const stockForm = document.getElementById('stock-form');

    const hiddenItems = document.getElementById('hidden-items');
    const hiddenSupplierId = document.getElementById('hidden-supplier-id');
    const hiddenTanggalStok = document.getElementById('hidden-tanggal-stok');
    const hiddenKeterangan = document.getElementById('hidden-keterangan');

    /*
    |--------------------------------------------------------------------------
    | SINKRONISASI WAKTU LOKAL AKURAT
    |--------------------------------------------------------------------------
    */
    let isManualDate = false;

    function setLocalDateTimeNow() {
        if (isManualDate) return;
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        tanggalStok.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    setLocalDateTimeNow();
    setInterval(setLocalDateTimeNow, 15000); // Update otomatis tiap 15 detik selama tidak diubah manual

    tanggalStok.addEventListener('change', function () {
        isManualDate = true;
    });

    /*
    |--------------------------------------------------------------------------
    | DATA PRODUK & CART
    |--------------------------------------------------------------------------
    */
    const products = @json($productData);
    let cart = [];

    /*
    |--------------------------------------------------------------------------
    | SUPPLIER SEARCH
    |--------------------------------------------------------------------------
    */
    supplierSearch.addEventListener('focus', function () {
        showSupplierResults(this.value);
    });

    supplierSearch.addEventListener('input', function () {
        supplierId.value = '';
        supplierError.textContent = '';
        showSupplierResults(this.value);
    });

    function showSupplierResults(keyword) {
        const search = keyword.toLowerCase().trim();
        const items = supplierResults.querySelectorAll('.supplier-result-item');
        let found = false;

        items.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            if (search === '' || name.includes(search)) {
                item.style.display = 'block';
                found = true;
            } else {
                item.style.display = 'none';
            }
        });

        supplierResults.style.display = found ? 'block' : 'none';
    }

    document.querySelectorAll('.supplier-result-item').forEach(item => {
        item.addEventListener('click', function () {
            supplierId.value = this.dataset.id;
            supplierSearch.value = this.dataset.name;
            supplierError.textContent = '';
            supplierResults.style.display = 'none';
        });
    });

    /*
    |--------------------------------------------------------------------------
    | SEARCH PRODUK
    |--------------------------------------------------------------------------
    */
    productSearch.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        productResults.innerHTML = '';

        if (keyword === '') {
            productResults.style.display = 'none';
            return;
        }

        const results = products.filter(product => {
            const alreadyAdded = cart.some(item => item.product_id == product.id);
            if (alreadyAdded) return false;

            return (
                product.nama.toLowerCase().includes(keyword) ||
                product.kategori.toLowerCase().includes(keyword)
            );
        });

        if (results.length === 0) {
            productResults.innerHTML = `<div class="no-product-result">Produk tidak ditemukan atau sudah ditambahkan.</div>`;
            productResults.style.display = 'block';
            return;
        }

        results.forEach(product => {
            const item = document.createElement('div');
            item.className = 'product-result-item';
            item.innerHTML = `
                <div class="product-result-name">${escapeHtml(product.nama)}</div>
                <div class="product-result-info">${escapeHtml(product.kategori)} · Stok saat ini: ${product.stok}</div>
            `;

            item.addEventListener('click', function () {
                addProduct(product);
            });

            productResults.appendChild(item);
        });

        productResults.style.display = 'block';
    });

    /*
    |--------------------------------------------------------------------------
    | TAMBAH PRODUK (OTOMATIS FOKUS KEMBALI KE SEARCH)
    |--------------------------------------------------------------------------
    */
    function addProduct(product) {
        const existing = cart.find(item => item.product_id == product.id);

        if (existing) {
            existing.jumlah += 1;
        } else {
            cart.push({
                product_id: product.id,
                nama: product.nama,
                kategori: product.kategori,
                stok: Number(product.stok),
                jumlah: 1
            });
        }

        productSearch.value = '';
        productResults.style.display = 'none';

        renderCart();

        // Langsung arahkan kursor kembali ke kolom search produk
        productSearch.focus();
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER CART
    |--------------------------------------------------------------------------
    */
    function renderCart() {
        stockCartBody.innerHTML = '';

        if (cart.length === 0) {
            stockCartBody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-cart">
                        Belum ada produk yang dipilih.
                    </td>
                </tr>
            `;
            return;
        }

        cart.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td><strong>${escapeHtml(item.nama)}</strong></td>
                <td>${escapeHtml(item.kategori)}</td>
                <td style="text-align: center;">${item.stok}</td>
                <td style="text-align: center;">
                    <input
                        type="number"
                        min="1"
                        class="form-control stock-quantity-input"
                        value="${item.jumlah}"
                        data-index="${index}"
                    >
                </td>
                <td style="text-align: center;">
                    <span class="stock-after-badge" id="stock-after-${index}">
                        ${item.stok + item.jumlah}
                    </span>
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
            stockCartBody.appendChild(row);
        });

        bindCartEvents();
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT CART (TANPA MENGHILANGKAN FOKUS INPUT ANGKA)
    |--------------------------------------------------------------------------
    */
    function bindCartEvents() {
        document.querySelectorAll('.stock-quantity-input').forEach(input => {
            // Update angka stok setelah secara langsung tanpa render ulang tabel
            input.addEventListener('input', function () {
                const index = Number(this.dataset.index);
                const rawVal = this.value;
                const parsed = parseInt(rawVal, 10);
                const validQty = (!isNaN(parsed) && parsed >= 1) ? parsed : 0;

                cart[index].jumlah = validQty;

                const stockAfterEl = document.getElementById(`stock-after-${index}`);
                if (stockAfterEl) {
                    stockAfterEl.textContent = cart[index].stok + validQty;
                }
            });

            // Jika dikosongkan lalu ditinggalkan (blur), kembalikan ke minimal 1
            input.addEventListener('blur', function () {
                const index = Number(this.dataset.index);
                let value = parseInt(this.value, 10);
                if (isNaN(value) || value < 1) {
                    value = 1;
                    this.value = 1;
                }
                cart[index].jumlah = value;

                const stockAfterEl = document.getElementById(`stock-after-${index}`);
                if (stockAfterEl) {
                    stockAfterEl.textContent = cart[index].stok + value;
                }
            });
        });

        document.querySelectorAll('.remove-product').forEach(button => {
            button.addEventListener('click', function () {
                const index = Number(this.dataset.index);
                cart.splice(index, 1);
                renderCart();
                productSearch.focus();
            });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN
    |--------------------------------------------------------------------------
    */
    saveButton.addEventListener('click', function () {
        if (!supplierId.value) {
            supplierError.textContent = 'Supplier harus dipilih.';
            supplierSearch.focus();
            return;
        }

        if (!tanggalStok.value) {
            alert('Tanggal stok harus diisi.');
            tanggalStok.focus();
            return;
        }

        if (cart.length === 0) {
            alert('Silakan pilih minimal satu produk.');
            productSearch.focus();
            return;
        }

        // Pastikan tidak ada item dengan jumlah < 1
        const invalidItem = cart.find(item => item.jumlah < 1);
        if (invalidItem) {
            alert(`Jumlah masuk untuk produk "${invalidItem.nama}" minimal 1.`);
            return;
        }

        hiddenItems.innerHTML = '';
        hiddenSupplierId.value = supplierId.value;
        hiddenTanggalStok.value = tanggalStok.value;
        hiddenKeterangan.value = keterangan.value;

        cart.forEach((item, index) => {
            hiddenItems.insertAdjacentHTML(
                'beforeend',
                `
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${index}][jumlah]" value="${item.jumlah}">
                `
            );
        });

        stockForm.submit();
    });

    /*
    |--------------------------------------------------------------------------
    | KLIK DI LUAR SEARCH
    |--------------------------------------------------------------------------
    */
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.supplier-search-wrapper')) {
            supplierResults.style.display = 'none';
        }

        if (!event.target.closest('.product-search-wrapper')) {
            productResults.style.display = 'none';
        }
    });

    /*
    |--------------------------------------------------------------------------
    | ESCAPE HTML
    |--------------------------------------------------------------------------
    */
    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    renderCart();
});
</script>
@endpush
@endsection