@extends('layouts.app')

@section('title', 'Riwayat Penyesuaian Stok')
@section('page-title', 'Riwayat Penyesuaian Stok')

@section('content')
<div class="container-fluid stock-opname-history">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Riwayat Penyesuaian Stok
            </h1>
        </div>

        <a
            href="{{ route('owner.stock-opnames.create') }}"
            class="btn btn-primary"
            style="background: #1f2937; border-color: #1f2937; font-size: 15px; font-weight: 600; padding: 10px 18px;"
        >
            + Penyesuaian Stok
        </a>
    </div>

    {{-- =========================================================
         NOTIFIKASI
    ========================================================== --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; font-size: 15px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- =========================================================
         FILTER & SEARCH BAR
    ========================================================== --}}
    <div class="filter-card mb-20">
        <form method="GET" action="{{ route('owner.stock-opnames.index') }}" class="filter-form">
            <div class="search-input-group">
                <span class="search-icon">🔍</span>
                <input
                    type="text"
                    name="search"
                    id="search-riwayat"
                    class="form-control"
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
            </div>

            <div class="filter-actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                    style="background: #1f2937; border-color: #1f2937; font-size: 15px; font-weight: 600; padding: 10px 18px;"
                >
                    Cari
                </button>

                @if(request('search'))
                    <a
                        href="{{ route('owner.stock-opnames.index') }}"
                        class="btn btn-secondary"
                        style="font-size: 15px; padding: 10px 16px;"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- =========================================================
         TABEL RIWAYAT
    ========================================================== --}}
    <div class="table-card mb-20">
        <div class="card-body-wrapper" style="padding: 20px;">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 55px;">No</th>
                            <th style="width: 170px;">Kode Opname</th>
                            <th style="width: 165px;">Tanggal & Waktu</th>
                            <th style="width: 160px;">Petugas</th>
                            <th style="width: 145px; text-align: center;">Jumlah Produk</th>
                            <th>Keterangan</th>
                            <th style="width: 100px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="riwayat-table-body">
                        @forelse ($stockOpnames as $opname)
                            <tr class="riwayat-row">
                                <td>
                                    {{ $stockOpnames->firstItem() + $loop->index }}
                                </td>

                                <td style="white-space: nowrap;">
                                    <strong>{{ $opname->kode ?? ('#' . $opname->id) }}</strong>
                                </td>

                                <td style="white-space: nowrap;">
                                    {{ $opname->tanggal_opname->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{ $opname->user->name ?? '-' }}
                                </td>

                                <td style="text-align: center;">
                                    <span class="stock-badge stock-available">
                                        {{ $opname->details_count }} produk
                                    </span>
                                </td>

                                <td style="color: #4b5563;">
                                    {{ $opname->keterangan ?: '-' }}
                                </td>

                                <td style="text-align: center; white-space: nowrap;">
                                    <a
                                        href="{{ route('owner.stock-opnames.show', $opname) }}"
                                        class="btn-action-detail"
                                    >
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-server-row">
                                <td colspan="7" class="empty-cart">
                                    <div style="font-weight: 600; font-size: 15px; color: #374151; margin-bottom: 4px;">
                                        {{ request('search') ? 'Data riwayat tidak ditemukan' : 'Belum ada riwayat penyesuaian stok' }}
                                    </div>
                                    <div style="font-size: 14px; color: #6b7280;">
                                        @if(request('search'))
                                            Tidak ada riwayat yang cocok dengan kata kunci "<strong>{{ request('search') }}</strong>".
                                        @else
                                            Klik tombol <strong>+ Penyesuaian Stok</strong> untuk mulai mencatat penghitungan stok fisik.
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris kosong untuk pencarian instan client-side --}}
                        <tr id="empty-search-row" style="display: none;">
                            <td colspan="7" class="empty-cart">
                                <div style="font-weight: 600; font-size: 15px; color: #374151; margin-bottom: 4px;">
                                    Data riwayat tidak ditemukan
                                </div>
                                <div style="font-size: 14px; color: #6b7280;">
                                    Tidak ada riwayat yang cocok pada halaman ini. Tekan <strong>Enter</strong> atau klik <strong>Cari</strong> untuk mencari ke seluruh halaman.
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($stockOpnames->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap: 12px; padding-top: 16px; border-top: 1px solid #f3f4f6;">
                    <small class="form-help" style="margin-top: 0;">
                        Menampilkan {{ $stockOpnames->firstItem() }}
                        sampai {{ $stockOpnames->lastItem() }}
                        dari {{ $stockOpnames->total() }} data
                    </small>

                    <div>
                        {{ $stockOpnames->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.stock-opname-history .mb-20 {
    margin-bottom: 20px;
}

.form-help {
    display: block;
    margin-top: 6px;
    font-size: 14px;
    color: #6b7280;
}

/* =========================================================
   FILTER & SEARCH BAR
========================================================= */
.filter-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 18px 20px;
}

.filter-form {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.search-input-group {
    position: relative;
    flex: 1;
    min-width: 260px;
}

.search-input-group .form-control {
    width: 100%;
    height: 44px;
    padding: 10px 14px 10px 42px;
    font-size: 15px;
    color: #1f2937;
    background-color: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.search-input-group .form-control:focus {
    border-color: #1f2937;
    outline: none;
    box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #6b7280;
    pointer-events: none;
}

.filter-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

/* =========================================================
   TABLE CARD & HEADER (SERAGAM 18px)
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

/* =========================================================
   TABLE STYLING (FONT SERAGAM 15px & FIT TO SCREEN)
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
    padding: 13px 14px;
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    white-space: nowrap;
}

.data-table td {
    padding: 13px 14px;
    font-size: 15px;
    color: #1f2937;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.empty-cart {
    text-align: center;
    padding: 45px 20px !important;
    color: #6b7280;
}

/* =========================================================
   BADGES & ACTION BUTTONS
========================================================= */
.stock-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

.stock-available {
    background: #d1fae5;
    color: #065f46;
}

.btn-action-detail {
    display: inline-block;
    background-color: #f3f4f6;
    color: #1f2937;
    border: 1px solid #d1d5db;
    text-decoration: none;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.15s ease;
}

.btn-action-detail:hover {
    background-color: #1f2937;
    color: #ffffff;
    border-color: #1f2937;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-riwayat');
    const rows = document.querySelectorAll('.riwayat-row');
    const emptySearchRow = document.getElementById('empty-search-row');

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(keyword)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (emptySearchRow) {
                emptySearchRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    }
});
</script>
@endpush