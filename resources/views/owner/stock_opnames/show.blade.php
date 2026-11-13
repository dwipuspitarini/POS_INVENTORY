@extends('layouts.app')

@section('title', 'Detail Penyesuaian Stok')
@section('page-title', 'Detail Penyesuaian Stok')

@section('content')
<div class="container-fluid stock-opname-detail">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Detail Penyesuaian Stok
            </h1>
        </div>

        <a href="{{ route('owner.stock-opnames.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================================================
         INFORMASI OPNAME
    ========================================================== --}}
    <div class="form-card mb-20">
        <div class="section-title mb-4">
            Informasi Penyesuaian
        </div>

        <div class="stock-info-grid mb-20">
            {{-- NOMOR OPNAME --}}
            <div class="form-group mb-0">
                <label class="form-label">Nomor Opname</label>
                <div class="detail-value-box">
                    <strong>{{ $stockOpname->kode ?? ('#' . $stockOpname->id) }}</strong>
                </div>
            </div>

            {{-- PETUGAS --}}
            <div class="form-group mb-0">
                <label class="form-label">Petugas</label>
                <div class="detail-value-box">
                    {{ $stockOpname->user->name ?? '-' }}
                </div>
            </div>

            {{-- TANGGAL & WAKTU OPNAME --}}
            <div class="form-group mb-0">
                <label class="form-label">Tanggal & Waktu</label>
                <div class="detail-value-box">
                    {{ $stockOpname->tanggal_opname->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        {{-- KETERANGAN UMUM --}}
        <div class="form-group mb-0">
            <label class="form-label">Keterangan Umum</label>
            <div class="detail-value-box">
                {{ $stockOpname->keterangan ?: '-' }}
            </div>
        </div>
    </div>

    {{-- =========================================================
         TABEL HASIL PENGHITUNGAN
    ========================================================== --}}
    <div class="table-card mb-20">
        <div class="card-body-wrapper" style="padding: 20px;">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 55px;">No</th>
                            <th style="width: 120px;">SKU</th>
                            <th>Nama Produk</th>
                            <th style="width: 120px; text-align: center;">Stok Sistem</th>
                            <th style="width: 120px; text-align: center;">Stok Fisik</th>
                            <th style="width: 110px; text-align: center;">Selisih</th>
                            <th style="width: 240px;">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($stockOpname->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td style="white-space: nowrap;">
                                    {{ $detail->product->sku ?? '-' }}
                                </td>

                                <td>
                                    <strong>{{ $detail->product->nama ?? 'Produk tidak tersedia' }}</strong>
                                </td>

                                <td style="text-align: center;">
                                    {{ $detail->stok_sistem }}
                                </td>

                                <td style="text-align: center; font-weight: 600;">
                                    {{ $detail->stok_fisik }}
                                </td>

                                <td style="text-align: center;">
                                    @if ($detail->selisih > 0)
                                        <span class="stock-badge stock-available">
                                            +{{ $detail->selisih }}
                                        </span>
                                    @elseif ($detail->selisih < 0)
                                        <span class="stock-badge stock-empty">
                                            {{ $detail->selisih }}
                                        </span>
                                    @else
                                        <span class="stock-badge stock-neutral">
                                            0
                                        </span>
                                    @endif
                                </td>

                                <td style="color: #4b5563;">
                                    {{ $detail->keterangan ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-cart">
                                    Tidak ada detail produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.stock-opname-detail .mb-20 {
    margin-bottom: 20px;
}

/* =========================================================
   FORM CARD & INFORMATION GRID (FONT SERAGAM 15px)
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
    grid-template-columns: repeat(3, 1fr);
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

.detail-value-box {
    width: 100%;
    padding: 11px 14px;
    font-size: 15px;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #1f2937;
    box-sizing: border-box;
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
    padding: 40px 20px !important;
    font-size: 15px;
    color: #6b7280;
}

/* =========================================================
   BADGES
========================================================= */
.stock-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

.stock-available { background: #d1fae5; color: #065f46; }
.stock-empty { background: #fee2e2; color: #991b1b; }
.stock-neutral { background: #f3f4f6; color: #374151; }

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