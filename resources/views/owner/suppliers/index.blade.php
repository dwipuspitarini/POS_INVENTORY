@extends('layouts.app')

@section('title', 'Supplier')
@section('page-title', 'Supplier')

@section('content')
<div class="container-fluid supplier-page">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header mb-4">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Supplier
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Kelola Data Supplier Produk Toko Bangunan.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4" style="background: #d1e7dd; color: #0f5132; padding: 14px 18px; border-radius: 8px; border: 1px solid #badbcc; font-size: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4" style="background: #f8d7da; color: #842029; padding: 14px 18px; border-radius: 8px; border: 1px solid #f5c2c7; font-size: 15px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================
        GRID UTAMA: FORM & TABEL
    ========================== --}}
    <div class="supplier-layout-grid">

        {{-- FORM TAMBAH SUPPLIER --}}
        <div class="form-card">
            <div class="section-title mb-4">
                Tambah Supplier
            </div>

            <form action="{{ route('owner.suppliers.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nama" class="form-label">
                        Nama Supplier <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        class="form-control"
                        value="{{ old('nama') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="telepon" class="form-label">
                        Telepon <span class="required">*</span>
                    </label>
                    <input
                        type="tel"
                        id="telepon"
                        name="telepon"
                        class="form-control"
                        value="{{ old('telepon') }}"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="15"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="alamat" class="form-label">
                        Alamat
                    </label>
                    <textarea
                        id="alamat"
                        name="alamat"
                        class="form-control"
                        rows="4"
                    >{{ old('alamat') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100" style="background: #1f2937; border-color: #1f2937; width: 100%; padding: 12px 16px; font-size: 15px; font-weight: 600; justify-content: center;">
                    Tambah Supplier
                </button>
            </form>
        </div>

        {{-- DAFTAR SUPPLIER --}}
        <div class="table-card">
            <div class="table-card-header">
                <h2>Daftar Supplier</h2>
            </div>

            <div class="card-body-wrapper" style="padding: 24px;">
                @if($suppliers->count())
                    {{-- FITUR SEARCH FULL WIDTH DENGAN JARAK LEGA --}}
                    <div class="supplier-search-wrapper">
                        <div class="supplier-search-box">
                            <span class="search-icon">🔍</span>
                            <input
                                type="text"
                                id="search-supplier"
                                class="form-control"
                                placeholder="Cari nama supplier, nomor telepon, atau alamat..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="data-table" id="supplier-table">
                            <thead>
                                <tr>
                                    <th style="width: 55px;">No</th>
                                    <th style="min-width: 170px;">Nama</th>
                                    <th style="min-width: 145px;">Telepon</th>
                                    <th style="min-width: 200px;">Alamat</th>
                                    <th style="width: 145px; text-align: center;">Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="supplier-table-body">
                                @foreach($suppliers as $supplier)
                                    <tr class="supplier-row"
                                        data-search="{{ strtolower($supplier->nama . ' ' . $supplier->telepon . ' ' . ($supplier->alamat ?? '')) }}">
                                        <td class="row-number">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $supplier->nama }}</strong>
                                        </td>
                                        <td style="white-space: nowrap;">{{ $supplier->telepon }}</td>
                                        <td style="line-height: 1.5;">{{ $supplier->alamat ?? '-' }}</td>
                                        <td style="text-align: center;">
                                            <div class="action-buttons-group">
                                                <a
                                                    href="{{ route('owner.suppliers.edit', $supplier) }}"
                                                    class="btn-action-edit"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    action="{{ route('owner.suppliers.destroy', $supplier) }}"
                                                    method="POST"
                                                    style="display: inline; margin: 0;"
                                                    onsubmit="return confirm('Yakin ingin menghapus supplier ini?')"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="btn-action-delete"
                                                    >
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- BARIS KOSONG JIKA PENCARIAN TIDAK DITEMUKAN --}}
                                <tr id="empty-search-row" style="display: none;">
                                    <td colspan="5" class="empty-cart">
                                        Supplier tidak ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- KONTROL PAGINATION (MUNCUL OTOMATIS JIKA DATA > 20) --}}
                    <div id="supplier-pagination-container" class="report-pagination-wrapper">
                        <div id="supplier-pagination-info" class="pagination-info"></div>

                        <div class="report-pagination">
                            <ul class="pagination" id="supplier-pagination-buttons"></ul>
                        </div>
                    </div>

                @else
                    <div class="empty-cart">
                        <div style="font-weight: 600; font-size: 15px; margin-bottom: 6px;">Belum ada supplier.</div>
                        <div style="font-size: 14px;">Silakan tambahkan data supplier melalui formulir di samping.</div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@push('styles')
<style>
.required {
    color: #dc3545;
}

/* =========================================================
   LAYOUT GRID
========================================================= */
.supplier-layout-grid {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 24px;
    align-items: start;
}

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

/* =========================================================
   TABLE CARD & FULL WIDTH SEARCH
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

.supplier-search-wrapper {
    width: 100%;
    margin-bottom: 24px;
}

.supplier-search-box {
    position: relative;
    width: 100%;
}

.supplier-search-box .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #6b7280;
    pointer-events: none;
}

.supplier-search-box .form-control {
    width: 100%;
    padding-left: 42px;
    height: 44px;
    font-size: 15px;
}

/* =========================================================
   TABLE STYLING (RAPI & TIDAK BERTABRAKAN)
========================================================= */
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
    padding: 16px;
    font-size: 15px;
    color: #1f2937;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.data-table tbody tr:hover {
    background-color: #f9fafb;
}

.action-buttons-group {
    display: inline-flex;
    gap: 8px;
    justify-content: center;
    align-items: center;
}

.btn-action-edit {
    background: #ffc107;
    color: #212529;
    padding: 7px 14px;
    font-size: 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    transition: background 0.15s ease;
}

.btn-action-edit:hover {
    background: #e0a800;
    color: #212529;
}

.btn-action-delete {
    background: #dc3545;
    color: #ffffff;
    border: none;
    padding: 7px 14px;
    font-size: 14px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease;
}

.btn-action-delete:hover {
    background: #bb2d3b;
}

.empty-cart {
    text-align: center;
    padding: 40px 20px !important;
    font-size: 15px;
    color: #6b7280;
}

/* =========================================================
   PAGINATION STYLING
========================================================= */
.report-pagination-wrapper {
    display: none;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    margin-top: 16px;
}

.pagination-info {
    font-size: 14px;
    color: #6b7280;
}

.report-pagination .pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #d1d5db;
}

.report-pagination .page-item {
    margin: 0;
}

.report-pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0 14px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    background-color: #ffffff;
    border: none;
    border-right: 1px solid #d1d5db;
    cursor: pointer;
    user-select: none;
}

.report-pagination .page-item:last-child .page-link {
    border-right: none;
}

.report-pagination .page-link:hover:not(:disabled) {
    background-color: #f3f4f6;
    color: #1f2937;
}

.report-pagination .page-item.active .page-link {
    background-color: #1f2937;
    color: #ffffff;
}

.report-pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background-color: #f9fafb;
    cursor: not-allowed;
}

@media (max-width: 1100px) {
    .supplier-layout-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Validasi input telepon: hanya menerima angka (0-9)
    const teleponInput = document.getElementById('telepon');
    if (teleponInput) {
        teleponInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    const searchInput = document.getElementById('search-supplier');
    const allRows = Array.from(document.querySelectorAll('.supplier-row'));
    const emptySearchRow = document.getElementById('empty-search-row');
    const paginationContainer = document.getElementById('supplier-pagination-container');
    const paginationInfo = document.getElementById('supplier-pagination-info');
    const paginationButtons = document.getElementById('supplier-pagination-buttons');

    if (!searchInput || allRows.length === 0) return;

    const perPage = 20;
    let currentPage = 1;
    let filteredRows = [...allRows];

    function renderTable() {
        const totalItems = filteredRows.length;
        const totalPages = Math.ceil(totalItems / perPage);

        if (currentPage > totalPages) currentPage = Math.max(1, totalPages);

        allRows.forEach(row => row.style.display = 'none');

        if (totalItems === 0) {
            emptySearchRow.style.display = '';
            paginationContainer.style.display = 'none';
            return;
        }

        emptySearchRow.style.display = 'none';

        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalItems);

        for (let i = startIndex; i < endIndex; i++) {
            const row = filteredRows[i];
            row.style.display = '';
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.textContent = i + 1;
        }

        if (totalItems > perPage) {
            paginationContainer.style.display = 'flex';
            paginationInfo.innerHTML = `Menampilkan <strong>${startIndex + 1}</strong> - <strong>${endIndex}</strong> dari <strong>${totalItems}</strong> supplier`;
            renderPaginationButtons(totalPages);
        } else {
            paginationContainer.style.display = 'none';
        }
    }

    function renderPaginationButtons(totalPages) {
        paginationButtons.innerHTML = '';

        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'page-link';
        prevBtn.textContent = '‹ Prev';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
        prevLi.appendChild(prevBtn);
        paginationButtons.appendChild(prevLi);

        for (let page = 1; page <= totalPages; page++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${page === currentPage ? 'active' : ''}`;
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.className = 'page-link';
            pageBtn.textContent = page;
            pageBtn.addEventListener('click', () => {
                currentPage = page;
                renderTable();
            });
            pageLi.appendChild(pageBtn);
            paginationButtons.appendChild(pageLi);
        }

        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'page-link';
        nextBtn.textContent = 'Next ›';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
        nextLi.appendChild(nextBtn);
        paginationButtons.appendChild(nextLi);
    }

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        filteredRows = allRows.filter(row => row.dataset.search.includes(keyword));
        currentPage = 1;
        renderTable();
    });

    renderTable();
});
</script>
@endpush
@endsection