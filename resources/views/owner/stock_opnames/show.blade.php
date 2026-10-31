@extends('layouts.app')

@section('title', 'Detail Stok Opname')
@section('page-title', 'Detail Stok Opname')

@section('content')
<div class="container stock-opname-detail">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header">
        <div>
            <h1>Detail Stok Opname</h1>
        </div>

        <a href="{{ route('owner.stock-opnames.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================
        INFORMASI OPNAME
    ========================== --}}
    <div class="form-card mb-20">
        <div class="section-title">
            Informasi Opname
        </div>

        <div class="stock-info-grid">
            {{-- NOMOR OPNAME --}}
            <div class="form-group">
                <label class="form-label">Nomor Opname</label>
                <div class="detail-value-box">
                    <strong>{{ $stockOpname->kode ?? ('#' . $stockOpname->id) }}</strong>
                </div>
            </div>

            {{-- TANGGAL OPNAME --}}
            <div class="form-group">
                <label class="form-label">Tanggal Opname</label>
                <div class="detail-value-box">
                    {{ $stockOpname->tanggal_opname->format('d-m-Y') }}
                </div>
            </div>

            {{-- PETUGAS --}}
            <div class="form-group">
                <label class="form-label">Petugas</label>
                <div class="detail-value-box">
                    {{ $stockOpname->user->name ?? '-' }}
                </div>
            </div>
        </div>

        {{-- KETERANGAN UMUM --}}
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Keterangan Umum</label>
            <div class="detail-value-box">
                {{ $stockOpname->keterangan ?: '-' }}
            </div>
        </div>
    </div>

    {{-- =========================
        TABEL HASIL PENGHITUNGAN
    ========================== --}}
    <div class="table-card mb-20">
        <div class="table-card-header">
            <h2>Hasil Penghitungan Produk</h2>
        </div>

        <div class="card-body-wrapper" style="padding: 24px;">
            <div class="table-wrapper" style="overflow-x: hidden;">
                <table class="data-table" style="width: 100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th style="width: 15%;">SKU</th>
                            <th style="width: 25%;">Nama Produk</th>
                            <th style="width: 13%; text-align: center;">Stok Sistem</th>
                            <th style="width: 13%; text-align: center;">Stok Fisik</th>
                            <th style="width: 12%; text-align: center;">Selisih</th>
                            <th style="width: 22%;">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($stockOpname->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->product->sku ?? '-' }}</td>
                                <td style="word-wrap: break-word;">
                                    <strong>{{ $detail->product->nama ?? 'Produk tidak tersedia' }}</strong>
                                </td>
                                <td style="text-align: center;">{{ $detail->stok_sistem }}</td>
                                <td style="text-align: center;">{{ $detail->stok_fisik }}</td>
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
                                <td style="word-wrap: break-word;">{{ $detail->keterangan ?: '-' }}</td>
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
/* =========================================================
   BASIC SPACING & CONTAINERS
========================================================= */

.stock-opname-detail .mb-20 {
    margin-bottom: 20px;
}

/* =========================================================
   FORM CARD & INFORMATION GRID
========================================================= */

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
    font-size: 14px;
    color: #374151;
    margin-bottom: 8px;
}

.detail-value-box {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    color: #1f2937;
    box-sizing: border-box;
}

/* =========================================================
   TABLE CARD & OVERRIDES
========================================================= */

.table-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.table-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid #e5e7eb;
    background-color: #ffffff;
}

.table-card-header h2 {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.table-wrapper {
    width: 100%;
    overflow-x: hidden !important;
}

.data-table {
    width: 100% !important;
    min-width: 0 !important;
    table-layout: fixed;
}

.data-table th, 
.data-table td {
    padding: 12px 10px;
    font-size: 14px;
    vertical-align: middle;
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
}

.empty-cart {
    text-align: center;
    padding: 35px !important;
    color: #6b7280;
}

/* =========================================================
   BADGES
========================================================= */

.stock-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
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
        gap: 0;
    }

    .table-wrapper {
        overflow-x: auto !important;
    }

    .data-table {
        min-width: 800px !important;
    }
}
</style>
@endpush