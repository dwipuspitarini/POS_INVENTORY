@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">Laporan Penjualan</h1>
            <p style="color: #6b7280; margin-bottom: 0;">Laporan penjualan dan retur berdasarkan periode transaksi</p>
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
                                'tanggal_akhir' => request('tanggal_akhir')
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
            <div class="card-title">Total Retur</div>
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

    {{-- DETAIL PENJUALAN --}}
    <div class="table-card mb-20">

        <div class="table-card-header">
            Detail Penjualan
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($details as $detail)
                        <tr>
                            <td>
                                {{ $details->firstItem() + $loop->index }}
                            </td>

                            <td>
                                {{ $detail->transaction->nomor_invoice ?? '-' }}
                            </td>

                            <td>
                                {{ $detail->transaction && $detail->transaction->tanggal_transaksi
                                    ? \Carbon\Carbon::parse($detail->transaction->tanggal_transaksi)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            <td>
                                {{ $detail->product->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $detail->product->category->nama ?? '-' }}
                            </td>

                            <td>
                                Rp {{ number_format($detail->harga, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $detail->quantity }}
                            </td>

                            <td>
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-data">
                                Tidak ada data penjualan yang sesuai dengan pencarian atau periode.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION PENJUALAN --}}
        @if($details->hasPages())
            <div class="report-pagination-wrapper">

                <div class="pagination-info">
                    Menampilkan
                    <strong>{{ $details->firstItem() }}</strong> -
                    <strong>{{ $details->lastItem() }}</strong>
                    dari <strong>{{ $details->total() }}</strong>
                    detail penjualan
                </div>

                <div class="report-pagination">
                    {{ $details->links('pagination::bootstrap-4') }}
                </div>

            </div>
        @endif

    </div>

    {{-- DETAIL RETUR PENJUALAN --}}
    <div class="table-card">

        <div class="table-card-header">
            Detail Retur Penjualan
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Retur</th>
                        <th>Invoice Asal</th>
                        <th>Tanggal Retur</th>
                        <th>Produk</th>
                        <th>Jumlah Retur</th>
                        <th>Kondisi</th>
                        <th>Alasan</th>
                        <th>Subtotal Retur</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($returns as $retur)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $retur->salesReturn->nomor_retur ?? '-' }}
                            </td>

                            <td>
                                {{ $retur->salesReturn->transaction->nomor_invoice ?? '-' }}
                            </td>

                            <td>
                                {{ $retur->salesReturn && $retur->salesReturn->tanggal_retur
                                    ? \Carbon\Carbon::parse($retur->salesReturn->tanggal_retur)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            <td>
                                {{ $retur->product->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $retur->quantity }}
                            </td>

                            <td>
                                {{ ucfirst($retur->kondisi ?? '-') }}
                            </td>

                            <td>
                                {{ $retur->alasan ?? '-' }}
                            </td>

                            <td>
                                Rp {{ number_format($retur->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-data">
                                Tidak ada retur yang sesuai dengan pencarian atau periode.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

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