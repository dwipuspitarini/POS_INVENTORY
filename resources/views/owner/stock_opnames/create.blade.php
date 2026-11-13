@extends('layouts.app')

@section('title', 'Penyesuaian Produk')
@section('page-title', 'Penyesuaian Produk')

@section('content')
<div class="container-fluid stock-opname-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Penyesuaian Produk
            </h1>
        </div>

        <a href="{{ route('owner.stock-opnames.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================================================
         ERROR VALIDASI
    ========================================================== --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 8px; font-size: 15px;">
            <strong>Periksa kembali data berikut:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('owner.stock-opnames.store') }}" method="POST" id="form-stock-opname">
        @csrf

        {{-- =========================================================
             INFORMASI PENYESUAIAN PRODUK
        ========================================================== --}}
        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Informasi Penyesuaian Produk
            </div>

            <div class="stock-info-grid mb-20">
                {{-- PETUGAS (KIRI) --}}
                <div class="form-group mb-0">
                    <label class="form-label">Petugas</label>
                    <input
                        type="text"
                        class="form-control"
                        value="{{ auth()->user()->name ?? 'Petugas login' }}"
                        readonly
                        style="background-color: #f9fafb; color: #4b5563;"
                    >
                </div>

                {{-- TANGGAL & WAKTU OPNAME (KANAN) --}}
                <div class="form-group mb-0">
                    <label for="tanggal_opname" class="form-label">
                        Tanggal Penyesuaian <span class="required">*</span>
                    </label>
                    <input
                        type="datetime-local"
                        name="tanggal_opname"
                        id="tanggal_opname"
                        class="form-control"
                        value="{{ old('tanggal_opname') }}"
                        required
                    >
                </div>
            </div>

            {{-- KETERANGAN UMUM --}}
            <div class="form-group mb-0">
                <label for="keterangan" class="form-label">
                    Keterangan Umum
                </label>
                <textarea
                    name="keterangan"
                    id="keterangan"
                    class="form-control"
                    rows="3"
                    maxlength="1000"
                >{{ old('keterangan') }}</textarea>
            </div>
        </div>

        {{-- =========================================================
             CARI PRODUK (INSTANT SEARCH)
        ========================================================== --}}
        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Tambah Produk ke Daftar Penyesuaian
            </div>

            <div class="form-group mb-0">
                <label for="cari_produk" class="form-label">
                    Cari Produk
                </label>

                <div class="product-search-wrapper" id="product-search-wrapper">
                    <div class="product-search-box">
                        <span class="search-icon">🔍</span>
                        <input
                            type="text"
                            id="cari_produk"
                            class="form-control"
                            autocomplete="off">
                    </div>

                    <div id="hasil-pencarian" class="product-results"></div>
                </div>
            </div>
        </div>

        {{-- =========================================================
             TABEL PRODUK TERPILIH
        ========================================================== --}}
        <div class="table-card mb-20">
            <div class="card-body-wrapper" style="padding: 20px;">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 110px;">SKU</th>
                                <th>Nama Produk</th>
                                <th style="width: 90px;">Satuan</th>
                                <th style="width: 115px; text-align: center;">Stok Sistem</th>
                                <th style="width: 130px; text-align: center;">Stok Fisik</th>
                                <th style="width: 100px; text-align: center;">Selisih</th>
                                <th style="width: 220px;">Keterangan</th>
                                <th style="width: 90px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="produk-terpilih">
                            <tr id="baris-kosong">
                                <td colspan="8" class="empty-cart">
                                    Belum ada produk. Ketik nama produk atau SKU pada kolom pencarian di atas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- =========================================================
             TOMBOL AKSI
        ========================================================== --}}
        <div class="form-card">
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                <a href="{{ route('owner.stock-opnames.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button
                    type="submit"
                    class="btn btn-primary"
                    id="tombol-simpan"
                    style="background: #1f2937; border-color: #1f2937;"
                >
                    Simpan Penyesuaian
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.stock-opname-page .mb-20 {
    margin-bottom: 20px;
}

.required {
    color: #dc3545;
}

/* =========================================================
   FORM CARD & GRID (FONT SERAGAM 15px)
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
    gap: 20px;
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
    font-family: inherit;
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

/* =========================================================
   PRODUCT SEARCH & DROPDOWN (INSTANT CLICK)
========================================================= */
.product-search-wrapper {
    position: relative;
}

.product-search-box {
    position: relative;
}

.product-search-box .form-control {
    padding-left: 42px;
    height: 44px;
    font-size: 15px;
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

.product-results {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    max-height: 280px;
    overflow-y: auto;
    z-index: 200;
    display: none;
}

.search-results-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.12s ease;
}

.search-results-item:last-child {
    border-bottom: none;
}

.search-results-item:hover {
    background: #f3f4f6;
}

.result-title {
    font-weight: 600;
    font-size: 15px;
    color: #1f2937;
}

.result-sub {
    font-size: 14px;
    color: #6b7280;
    margin-top: 3px;
}

/* =========================================================
   TABLE & BADGES (FONT SERAGAM 15px)
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

.count-badge {
    background-color: #f3f4f6;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
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
    padding: 13px 12px;
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    white-space: nowrap;
}

.data-table td {
    padding: 13px 12px;
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

.stock-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 700;
}

.stock-available { background: #d1fae5; color: #065f46; }
.stock-empty { background: #fee2e2; color: #991b1b; }
.stock-neutral { background: #f3f4f6; color: #374151; }

.data-table input.form-control,
.table-input {
    height: 42px;
    padding: 8px 12px;
    font-size: 15px !important;
    margin: 0;
}

.physical-stock-input {
    width: 95px;
    text-align: center;
    font-weight: 600;
    margin: 0 auto;
    display: block;
}

/* =========================================================
   RESPONSIVE
========================================================= */
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
    // Sinkronisasi tanggal & waktu lokal perangkat (YYYY-MM-DDTHH:mm)
    const tanggalInput = document.getElementById('tanggal_opname');
    if (tanggalInput && !tanggalInput.value) {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        tanggalInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
    }

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

    function addProductInstant(product) {
        const productKey = String(product.id);

        // Jika produk sudah ada di tabel, fokuskan ke input stok fisik produk tersebut
        if (selected.has(productKey)) {
            const existingRow = selectedBody.querySelector(`tr[data-product-id="${productKey}"]`);
            if (existingRow) {
                const existingInput = existingRow.querySelector('.physical-stock-input');
                if (existingInput) {
                    existingInput.focus();
                    existingInput.select();
                }
            }
            searchInput.value = '';
            results.style.display = 'none';
            return;
        }

        selected.add(productKey);

        const row = document.createElement('tr');
        row.dataset.productId = productKey;

        function cell(text) {
            const td = document.createElement('td');
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
        systemStock.style.fontWeight = '600';
        row.appendChild(systemStock);

        // Input Stok Fisik
        const physicalCell = cell('');
        physicalCell.style.textAlign = 'center';

        const physicalInput = document.createElement('input');
        physicalInput.type = 'number';
        physicalInput.name = `details[${product.id}][stok_fisik]`;
        physicalInput.className = 'form-control table-input physical-stock-input';
        physicalInput.min = '0';
        physicalInput.step = '1';
        physicalInput.required = true;
        physicalInput.value = String(product.stok);
        physicalInput.setAttribute('aria-label', 'Stok fisik ' + product.nama);

        // Auto-block angka saat diklik/fokus
        physicalInput.addEventListener('focus', function () {
            this.select();
        });
        physicalInput.addEventListener('mouseup', function (e) {
            e.preventDefault();
        });

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
        noteInput.className = 'form-control table-input';
        noteInput.maxLength = 1000;
        noteInput.setAttribute('aria-label', 'Keterangan ' + product.nama);
        noteCell.appendChild(noteInput);
        row.appendChild(noteCell);

        // Tombol Hapus Baris
        const actionCell = cell('');
        actionCell.style.textAlign = 'center';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger btn-sm';
        removeButton.style.padding = '6px 12px';
        removeButton.style.fontSize = '13px';
        removeButton.style.fontWeight = '600';
        removeButton.style.borderRadius = '6px';
        removeButton.textContent = 'Hapus';
        removeButton.addEventListener('click', function () {
            selected.delete(productKey);
            row.remove();
            updateCount();
            searchInput.focus();
        });

        actionCell.appendChild(removeButton);
        row.appendChild(actionCell);

        selectedBody.appendChild(row);
        updateCount();

        // Bersihkan search & kembalikan kursor ke kotak pencarian
        searchInput.value = '';
        results.style.display = 'none';
        searchInput.focus();
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
        }).slice(0, 12);

        if (matches.length === 0) {
            const item = document.createElement('div');
            item.style.padding = '14px 16px';
            item.style.color = '#6b7280';
            item.style.fontSize = '15px';
            item.style.textAlign = 'center';
            item.textContent = 'Produk tidak ditemukan.';
            results.appendChild(item);
            results.style.display = 'block';
            return;
        }

        matches.forEach(function (product) {
            const isAdded = selected.has(String(product.id));
            const item = document.createElement('div');
            item.className = 'search-results-item';

            const title = document.createElement('div');
            title.className = 'result-title';
            title.textContent = (product.sku && product.sku !== '-' ? product.sku + ' — ' : '') + product.nama;

            const sub = document.createElement('div');
            sub.className = 'result-sub';
            sub.innerHTML = `Stok sistem: <strong>${product.stok} ${product.satuan}</strong>` +
                (isAdded ? ' · <span style="color: #065f46; font-weight: 600;">Sudah di daftar</span>' : '');

            item.appendChild(title);
            item.appendChild(sub);

            item.addEventListener('click', function () {
                addProductInstant(product);
            });

            results.appendChild(item);
        });

        results.style.display = 'block';
    }

    searchInput.addEventListener('input', function () {
        renderResults(this.value.trim().toLowerCase());
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#product-search-wrapper')) {
            results.style.display = 'none';
        }
    });

    form.addEventListener('submit', function (event) {
        if (selected.size === 0) {
            event.preventDefault();
            alert('Tambahkan minimal satu produk sebelum menyimpan penyesuaian.');
            searchInput.focus();
            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = 'Menyimpan...';
    });

    updateCount();
});
</script>
@endpush
@endsection