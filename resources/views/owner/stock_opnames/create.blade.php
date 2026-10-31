@extends('layouts.app')

@section('title', 'Penyesuaian Produk')
@section('page-title', 'Penyesuaian Produk')

@section('content')
<div class="container stock-opname-page">

    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 8px;">
            <strong>Periksa kembali data berikut:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header">
        <div>
            <h1> </h1>
        </div>

        <a href="{{ route('owner.stock-opnames.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <form action="{{ route('owner.stock-opnames.store') }}" method="POST" id="form-stock-opname">
        @csrf

        {{-- =========================
            INFORMASI OPNAME
        ========================== --}}
        <div class="form-card mb-20">
            <div class="section-title">
                Informasi Penyesuaian Produk
            </div>

            <div class="stock-info-grid">
                {{-- TANGGAL OPNAME --}}
                <div class="form-group">
                    <label for="tanggal_opname" class="form-label">
                        Tanggal Penyesuaian Produk <span class="required">*</span>
                    </label>
                    <input type="date"
                           name="tanggal_opname"
                           id="tanggal_opname"
                           class="form-control"
                           value="{{ old('tanggal_opname', date('Y-m-d')) }}"
                           required>
                </div>

                {{-- PETUGAS --}}
                <div class="form-group">
                    <label class="form-label">Petugas</label>
                    <input type="text"
                           class="form-control"
                           value="{{ auth()->user()->name ?? 'Petugas login' }}"
                           readonly
                           style="background-color: #f9fafb;">
                </div>
            </div>

            {{-- KETERANGAN UMUM --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label for="keterangan" class="form-label">
                    Keterangan Umum
                </label>
                <textarea name="keterangan"
                          id="keterangan"
                          class="form-control"
                          rows="3"
                          maxlength="1000">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        {{-- =========================
            PILIH PRODUK
        ========================== --}}
        <div class="form-card mb-20">
            <div class="section-title">
                Cari Produk
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="cari_produk" class="form-label">
                    Produk
                </label>

                <div class="product-search-wrapper">
                    <div class="product-search-box">
                        <span class="search-icon">🔍</span>
                        <input type="text"
                               id="cari_produk"
                               class="form-control"
                               autocomplete="off"
                               placeholder="Ketik nama produk atau SKU...">
                    </div>

                    <div id="hasil-pencarian" class="product-results"></div>
                </div>
            </div>
        </div>

        {{-- =========================
            TABEL PRODUK TERPILIH
        ========================== --}}
        <div class="table-card mb-20">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Satuan</th>
                            <th style="text-align: center;">Stok Sistem</th>
                            <th style="width: 150px;">Stok Fisik</th>
                            <th style="text-align: center; width: 110px;">Selisih</th>
                            <th style="width: 200px;">Keterangan</th>
                            <th style="text-align: center; width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="produk-terpilih">
                        <tr id="baris-kosong">
                            <td colspan="8" class="empty-cart">
                                Belum ada produk. Cari dan tambahkan produk terlebih dahulu.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- =========================
            TOMBOL AKSI
        ========================== --}}
        <div class="form-card">
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('owner.stock-opnames.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" id="tombol-simpan">
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
/* =========================================================
   REQUIRED & BASIC SPACING
========================================================= */

.stock-opname-page .mb-20 {
    margin-bottom: 20px;
}

.required {
    color: #dc3545;
}

/* =========================================================
   STOCK INFORMATION & GRID
========================================================= */

.stock-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

/* =========================================================
   PRODUCT SEARCH & DROPDOWN
========================================================= */

.product-search-wrapper {
    position: relative;
}

.product-search-box {
    position: relative;
}

.product-search-box .form-control {
    padding-left: 45px;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 17px;
    z-index: 2;
}

.product-results {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
    display: none;
}

.search-results-item {
    padding: 13px 15px;
    cursor: pointer;
    border-bottom: 1px solid #eeeeee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.search-results-item:last-child {
    border-bottom: none;
}

.search-results-item:hover {
    background: #f3f6fb;
}

/* =========================================================
   TABLE & BADGES
========================================================= */

.empty-cart {
    text-align: center;
    padding: 35px !important;
    color: #6b7280;
}

.stock-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.stock-available { background: #d1fae5; color: #065f46; }
.stock-low { background: #fef3c7; color: #92400e; }
.stock-empty { background: #fee2e2; color: #991b1b; }
.stock-neutral { background: #f3f4f6; color: #374151; }

.data-table input.form-control {
    padding: 6px 10px;
    font-size: 13px;
    height: auto;
}

/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 800px) {
    .stock-info-grid {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        min-width: 900px;
    }
}
</style>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function () {
    const products = {{ Illuminate\Support\Js::from(
        $products->map(fn ($product) => [
            'id' => $product->id,
            'sku' => $product->sku ?? '-',
            'nama' => $product->nama,
            'satuan' => $product->satuan ?? '-',
            'stok' => (int) $product->stok,
        ])->values()
    ) }};

    const searchInput = document.getElementById('cari_produk');
    const results = document.getElementById('hasil-pencarian');
    const selectedBody = document.getElementById('produk-terpilih');
    const countLabel = document.getElementById('jumlah-produk');
    const emptyRow = document.getElementById('baris-kosong');
    const form = document.getElementById('form-stock-opname');
    const saveButton = document.getElementById('tombol-simpan');

    const selected = new Set();

    function updateCount() {
    const emptyRow = document.getElementById('baris-kosong');

    if (countLabel) {
        countLabel.textContent = selected.size + ' produk terpilih';
    }

    if (emptyRow) {
        emptyRow.hidden = selected.size > 0;
    }
}

    function addProduct(product) {
        if (selected.has(String(product.id))) return;

        selected.add(String(product.id));

        const row = document.createElement('tr');
        row.dataset.productId = product.id;

        function cell(text) {
            const td = document.createElement('td');
            td.style.padding = '10px 12px';
            td.textContent = text ?? '-';
            return td;
        }

        // SKU, Nama, Satuan
        row.appendChild(cell(product.sku));

        const nameTd = cell('');
        const nameStrong = document.createElement('strong');
        nameStrong.textContent = product.nama;
        nameTd.appendChild(nameStrong);
        row.appendChild(nameTd);

        row.appendChild(cell(product.satuan));

        // Stok Sistem
        const systemStock = cell(String(product.stok));
        systemStock.style.textAlign = 'center';
        row.appendChild(systemStock);

        // Input Stok Fisik
        const physicalCell = cell('');
        const physicalInput = document.createElement('input');
        physicalInput.type = 'number';
        physicalInput.name = `details[${product.id}][stok_fisik]`;
        physicalInput.className = 'form-control';
        physicalInput.min = '0';
        physicalInput.step = '1';
        physicalInput.required = true;
        physicalInput.value = String(product.stok);
        physicalInput.setAttribute('aria-label', 'Stok fisik ' + product.nama);

        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = `details[${product.id}][product_id]`;
        productIdInput.value = product.id;

        physicalCell.appendChild(physicalInput);
        physicalCell.appendChild(productIdInput);
        row.appendChild(physicalCell);

        // Badge Selisih
        const diffCell = cell('');
        diffCell.style.textAlign = 'center';
        const diffBadge = document.createElement('span');
        diffBadge.className = 'stock-badge stock-neutral';
        diffBadge.textContent = '0';
        diffCell.appendChild(diffBadge);
        row.appendChild(diffCell);

        // Handler Hitung Selisih Otomatis
        physicalInput.addEventListener('input', function () {
            const physicalVal = parseInt(this.value, 10);
            if (isNaN(physicalVal)) {
                diffBadge.textContent = '-';
                diffBadge.className = 'stock-badge stock-neutral';
                return;
            }

            const diff = physicalVal - product.stok;
            if (diff > 0) {
                diffBadge.textContent = '+' + diff;
                diffBadge.className = 'stock-badge stock-available';
            } else if (diff < 0) {
                diffBadge.textContent = String(diff);
                diffBadge.className = 'stock-badge stock-empty';
            } else {
                diffBadge.textContent = '0';
                diffBadge.className = 'stock-badge stock-neutral';
            }
        });

        // Keterangan Item
        const noteCell = cell('');
        const noteInput = document.createElement('input');
        noteInput.type = 'text';
        noteInput.name = `details[${product.id}][keterangan]`;
        noteInput.className = 'form-control';
        noteInput.maxLength = 1000;
        noteInput.placeholder = 'Opsional';
        noteInput.setAttribute('aria-label', 'Keterangan ' + product.nama);
        noteCell.appendChild(noteInput);
        row.appendChild(noteCell);

        // Tombol Hapus Baris
        const actionCell = cell('');
        actionCell.style.textAlign = 'center';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger btn-sm';
        removeButton.style.padding = '4px 8px';
        removeButton.style.fontSize = '12px';
        removeButton.textContent = 'Hapus';
        removeButton.addEventListener('click', function () {
            selected.delete(String(product.id));
            row.remove();
            updateCount();
        });

        actionCell.appendChild(removeButton);
        row.appendChild(actionCell);

        selectedBody.appendChild(row);
        updateCount();
    }

    function renderResults(keyword) {
        results.replaceChildren();

        if (!keyword) {
            results.style.display = 'none';
            return;
        }

        const matches = products.filter(function (product) {
            const searchable = (product.sku + ' ' + product.nama).toLowerCase();
            return searchable.includes(keyword);
        }).slice(0, 10);

        if (matches.length === 0) {
            const item = document.createElement('div');
            item.className = 'search-results-item text-muted';
            item.style.fontSize = '14px';
            item.textContent = 'Produk tidak ditemukan.';
            results.appendChild(item);
            results.style.display = 'block';
            return;
        }

        matches.forEach(function (product) {
            const item = document.createElement('div');
            item.className = 'search-results-item';

            const info = document.createElement('div');
            const name = document.createElement('div');
            name.style.fontWeight = '600';
            name.style.fontSize = '14px';
            name.style.color = '#1f2937';
            name.textContent = product.nama;

            const detail = document.createElement('small');
            detail.className = 'text-muted';
            detail.textContent = 'SKU: ' + product.sku + ' · Stok sistem: ' + product.stok + ' ' + product.satuan;

            info.appendChild(name);
            info.appendChild(detail);

            const addButton = document.createElement('button');
            addButton.type = 'button';
            addButton.className = selected.has(String(product.id)) ? 'btn btn-sm btn-secondary' : 'btn btn-sm btn-primary';
            addButton.textContent = selected.has(String(product.id)) ? 'Sudah Ada' : 'Tambahkan';
            addButton.disabled = selected.has(String(product.id));

            addButton.addEventListener('click', function () {
                addProduct(product);
                renderResults(searchInput.value.trim().toLowerCase());
            });

            item.appendChild(info);
            item.appendChild(addButton);
            results.appendChild(item);
        });

        results.style.display = 'block';
    }

    searchInput.addEventListener('input', function () {
        renderResults(this.value.trim().toLowerCase());
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !results.contains(e.target)) {
            results.style.display = 'none';
        }
    });

    form.addEventListener('submit', function (event) {
        if (selected.size === 0) {
            event.preventDefault();
            alert('Tambahkan minimal satu produk sebelum menyimpan stok opname.');
            searchInput.focus();
            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = 'Menyimpan...';
    });

    updateCount();
});
</script>
@endsection