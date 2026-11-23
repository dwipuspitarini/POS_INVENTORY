@extends('layouts.app')

@section('title', 'Laporan Penjualan & Pendapatan')
@section('page-title', 'Laporan Penjualan & Pendapatan')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">Laporan Penjualan & Pendapatan</h1>
            <p style="color: #6b7280; margin-bottom: 0;">Laporan penjualan dan Pendapatan berdasarkan periode transaksi</p>
        </div>
    </div>

    {{-- FILTER DAN PENCARIAN --}}
    <div class="form-card mb-20">
        <div class="section-title mb-3" style="font-weight: 700; font-size: 16px; color: #1f2937;">Filter Penjualan</div>

        <form method="GET" action="{{ route('owner.laporan.penjualan') }}">
            {{-- BARIS 1: INPUT PENCARIAN FULL WIDTH --}}
            <div class="form-group mb-3">
                <label for="search" class="form-label">
                    Cari Penjualan
                </label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    placeholder="Cari Invoice atau Nama Produk"
                    value="{{ request('search') }}"
                >
            </div>

            {{-- FILTER KASIR --}}
            <div class="form-group mb-3">
                <label for="kasir_id" class="form-label">
                    Filter Kasir
                </label>
                <select name="kasir_id" id="kasir_id" class="form-control">
                    <option value="">Semua Kasir</option>
                    @foreach($kasirs as $kasir)
                    <option value="{{ $kasir->id }}"
                        {{ (string) request('kasir_id') === (string) $kasir->id ? 'selected' : '' }}>
                        {{ $kasir->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- BARIS 2: TANGGAL & ACTION BUTTONS SEJAJAR --}}
            <div class="filter-bottom-row">
                <div class="form-group mb-0">
                    <label for="tanggal_mulai" class="form-label">
                        Tanggal Mulai
                    </label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        class="form-control"
                        value="{{ request('tanggal_mulai') }}"
                    >
                </div>

                <div class="form-group mb-0">
                    <label for="tanggal_akhir" class="form-label">
                        Tanggal Akhir
                    </label>
                    <input
                        type="date"
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        class="form-control"
                        value="{{ request('tanggal_akhir') }}"
                    >
                </div>

                <div class="form-group mb-0 d-flex flex-column justify-content-end">
                    <div class="report-filter-actions">
                        <button type="submit" class="btn btn-primary">
                            Tampilkan
                        </button>

                        <a
                            href="{{ route('owner.laporan.penjualan.pdf', [
                                'search' => request('search'),
                                'tanggal_mulai' => request('tanggal_mulai'),
                                'tanggal_akhir' => request('tanggal_akhir'),
                                'kasir_id' => request('kasir_id')
                            ]) }}"
                            class="btn btn-danger"
                        >
                            Cetak PDF
                        </a>

                        <a
                            href="{{ route('owner.laporan.penjualan') }}"
                            class="btn btn-secondary"
                        >
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    
{{-- RINGKASAN --}}
<div class="cards mb-20">

    <div class="card">
        <div class="card-title">Total Transaksi</div>
        <div class="card-value">
            {{ number_format($totalTransaksi, 0, ',', '.') }}
        </div>
    </div>

    <div class="card">
        <div class="card-title">Total Produk Terjual</div>
        <div class="card-value">
            {{ number_format($totalProdukTerjual, 0, ',', '.') }}
        </div>
    </div>

    <div class="card">
        <div class="card-title">Penjualan Kotor</div>
        <div class="card-value">
            Rp {{ number_format($totalPenjualanKotor, 0, ',', '.') }}
        </div>
    </div>

    <div class="card">
        <div class="card-title">Total Diskon</div>
        <div class="card-value">
            Rp {{ number_format($totalDiskon, 0, ',', '.') }}
        </div>
    </div>

    <div class="card">
        <div class="card-title">Total Retur Bersih</div>
        <div class="card-value">
            Rp {{ number_format($totalRetur, 0, ',', '.') }}
        </div>
    </div>

    <div class="card">
        <div class="card-title">Penjualan Bersih</div>
        <div class="card-value">
            Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
        </div>
    </div>

</div>


    
{{-- TABEL LAPORAN PENJUALAN & PENDAPATAN --}}
<div class="table-card">

    <div class="table-card-header">
        Daftar Transaksi Penjualan
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Kasir</th>
                    <th>Pelanggan</th>
                    <th>Tanggal Transaksi</th>
                    <th>Total</th>
                    <th>Dibayar</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transactions as $transaction)
                    @php
                        $total = (float) $transaction->total_akhir;
                        $dibayar = (float) $transaction->jumlah_bayar;
                        $sisa = max($total - $dibayar, 0);
                    @endphp

                    <tr>
                        <td>
                            {{ $transactions->firstItem() + $loop->index }}
                        </td>

                        <td>
                            {{ $transaction->nomor_invoice }}
                        </td>

                        <td>
                            {{ $transaction->user->name ?? '-' }}
                        </td>

                        <td>
                            {{ $transaction->customer->nama ?? 'Pelanggan Umum' }}
                        </td>

                        <td>
                            {{ $transaction->tanggal_transaksi
                                ? \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d/m/Y H:i')
                                : '-' }}
                        </td>

                        <td>
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>

                        <td>
                            Rp {{ number_format($dibayar, 0, ',', '.') }}
                        </td>

                        <td>
                            Rp {{ number_format($sisa, 0, ',', '.') }}
                        </td>

                        <td>
                            {{ ucfirst($transaction->status_pembayaran ?? '-') }}
                        </td>

                        <td>
                            <a
                                href="{{ route('owner.laporan.penjualan.detail', $transaction->id) }}?{{ http_build_query(request()->query()) }}"
                                class="btn btn-primary btn-sm"
                            >
                                Detail
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="10" class="empty-data">
                            Tidak ada transaksi yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($transactions->hasPages())
        <div class="report-pagination-wrapper">

            <div class="pagination-info">
                Menampilkan
                <strong>{{ $transactions->firstItem() }}</strong> -
                <strong>{{ $transactions->lastItem() }}</strong>
                dari <strong>{{ $transactions->total() }}</strong>
                transaksi
            </div>

            <div class="report-pagination">
                {{ $transactions->links('pagination::bootstrap-4') }}
            </div>

        </div>
    @endif

</div>


@push('styles')
<style>
/* CSS PERBAIKAN UNTUK MENSEJAJARKAN TOMBOL */
.filter-bottom-row {
    display: flex;
    gap: 15px;
    align-items: flex-end; /* Memaksa semua item rapat ke bawah */
}

.filter-bottom-row .form-group {
    flex: 1;
}

.filter-bottom-row .form-group:last-child {
    flex: 0 0 auto;
}

.report-filter-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    height: 40px; /* Menyamakan tinggi tombol dengan tinggi input field */
}

.report-filter-actions .btn {
    height: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 16px;
    font-size: 14px;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    height: 40px; /* Dikunci 40px agar presisi dengan tombol */
    padding: 9px 12px;
    font-size: 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
}

.report-pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    flex-wrap: wrap;
    gap: 12px;
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
    padding: 0 12px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    background-color: #ffffff;
    border: none;
    border-right: 1px solid #d1d5db;
    text-decoration: none;
}

.report-pagination .page-item:last-child .page-link {
    border-right: none;
}

.report-pagination .page-link:hover {
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

@media (max-width: 768px) {
    .filter-bottom-row {
        flex-direction: column;
        align-items: stretch;
    }

    .report-filter-actions {
        height: auto;
        margin-top: 10px;
    }

    .report-filter-actions .btn {
        height: 40px;
        flex: 1;
    }
}
</style>
@endpush

@endsection