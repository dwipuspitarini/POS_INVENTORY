@extends('layouts.app')

@section('title', 'Riwayat Penyesuaian Stok')
@section('page-title', 'Riwayat Penyesuaian Stok')

@section('content')
<div class="container stock-opname-history">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">
                 
            </h1>
        </div>

        <a href="{{ route('owner.stock-opnames.create') }}" class="btn btn-primary">
            + Penyesuaian Stok
        </a>
    </div>

    {{-- =========================
        TABEL RIWAYAT
    ========================== --}}
    <div class="table-card mb-20">

        <div class="table-card-header">
            <h2>Daftar Riwayat</h2>
        </div>

        <div class="card-body-wrapper" style="padding: 24px;">
            <div class="table-wrapper" style="overflow-x: hidden;">
                <table class="data-table" style="width: 100%; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No.</th>
                            <th style="width: 25%;">Kode Opname</th>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 15%;">Petugas</th>
                            <th style="width: 15%; text-align: center;">Jumlah Produk</th>
                            <th style="width: 18%;">Keterangan</th>
                            <th style="width: 12%; text-align: center;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($stockOpnames as $opname)
                            <tr>
                                <td>
                                    {{ $stockOpnames->firstItem() + $loop->index }}
                                </td>

                                <td style="word-break: break-all;">
                                    <strong>
                                        {{ $opname->kode ?? ('#' . $opname->id) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $opname->tanggal_opname->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $opname->user->name ?? '-' }}
                                </td>

                                <td style="text-align: center;">
                                    <span class="stock-badge stock-available">
                                        {{ $opname->details_count }} produk
                                    </span>
                                </td>

                                <td style="word-wrap: break-word;">
                                    {{ $opname->keterangan ?: '-' }}
                                </td>

                                <td style="text-align: center;">
                                    <a href="{{ route('owner.stock-opnames.show', $opname) }}"
                                       class="btn btn-secondary btn-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-cart">
                                    <div style="font-weight: 600; margin-bottom: 4px;">
                                        Belum ada riwayat stok opname
                                    </div>
                                    <div style="font-size: 13px;">
                                        Klik tombol Stok Opname Baru untuk mulai mencatat penghitungan stok.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($stockOpnames->hasPages())
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-4" style="gap:12px;">
                    <small class="form-help" style="margin-top: 0;">
                        Menampilkan {{ $stockOpnames->firstItem() }}
                        sampai {{ $stockOpnames->lastItem() }}
                        dari {{ $stockOpnames->total() }} data
                    </small>

                    <div>
                        {{ $stockOpnames->links() }}
                    </div>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
/* =========================================================
   BASIC SPACING & CONTAINERS
========================================================= */

.stock-opname-history .mb-20 {
    margin-bottom: 20px;
}

.form-help {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
}

/* =========================================================
   TABLE CARD & HEADERS (TANPA SCROLL)
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

.empty-cart {
    text-align: center;
    padding: 35px !important;
    color: #6b7280;
}

/* =========================================================
   TABLE OVERRIDES (Mencegah overflow/geser)
========================================================= */

.table-wrapper {
    width: 100%;
    overflow-x: hidden !important; /* Menghilangkan scrollbar horizontal */
}

.data-table {
    width: 100% !important;
    min-width: 0 !important; /* Hapus min-width paksaan */
    table-layout: fixed; /* Membagi kolom secara proporsional sesuai lebar persentase */
}

.data-table th, 
.data-table td {
    padding: 12px 10px;
    font-size: 14px;
    vertical-align: middle;
    white-space: normal; /* Biarkan teks turun ke bawah jika terlalu panjang */
    overflow: hidden;
    text-overflow: ellipsis;
}

/* =========================================================
   BADGES
========================================================= */

.stock-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.stock-available { 
    background: #d1fae5; 
    color: #065f46; 
}
</style>
@endpush