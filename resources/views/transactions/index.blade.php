@extends('layouts.app')
@section('title', 'Transaksi Penjualan')
@section('page-title', 'Transaksi Penjualan')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1>Transaksi Penjualan</h1>
            <p>Daftar Transaksi Penjualan Toko Bangunan</p>
        </div>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            + Transaksi Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('transactions.index') }}" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" name="search" class="form-control"
            placeholder="Cari nomor invoice atau nama pelanggan..."
            value="{{ request('search') }}" style="flex: 1;">

        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <div class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                {{ $transactions->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <strong>
                                    {{ $transaction->nomor_invoice }}
                                </strong>
                            </td>
                            <td>
                                {{ $transaction->customer->nama ?? 'Pelanggan Umum' }}
                            </td>
                            <td>
                                {{ $transaction->tanggal_transaksi->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                Rp {{ number_format($transaction->total_akhir, 0, ',', '.') }}
                            </td>
                            <td>
                                {{ strtoupper($transaction->metode_pembayaran) }}
                            </td>
                            <td>
                                {{ strtoupper($transaction->status_pembayaran) }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <a href="{{ route('transactions.show', $transaction) }}"
                                    class="btn btn-primary btn-sm">
                                    Detail
                                </a>
                                <a href="{{ route('sales-returns.create', [
                                'transaksi_id' => $transaction->id
                                ]) }}" class="btn btn-danger btn-sm">
                                Retur
                            </a>
                            @if($transaction->status_pembayaran === 'dp')
                            <a href="{{ route('transactions.pelunasan.form', $transaction) }}"
                            class="btn btn-warning btn-sm">
                            Pelunasan
                        </a>
                        @endif
                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-data">
                                Belum ada transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($transactions->count())
            <div class="transaction-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $transactions->firstItem() }}</strong> - <strong>{{ $transactions->lastItem() }}</strong> dari <strong>{{ $transactions->total() }}</strong> transaksi
                </div>
                <div class="transaction-pagination">
                    {{ $transactions->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* =========================
   PAGINATION TRANSAKSI
========================= */

.transaction-pagination-wrapper {
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

.transaction-pagination .pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #d1d5db;
}

.transaction-pagination .page-item {
    margin: 0;
}

.transaction-pagination .page-link {
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
    transition: background-color 0.15s ease, color 0.15s ease;
}

.transaction-pagination .page-item:last-child .page-link {
    border-right: none;
}

/* Hover State */
.transaction-pagination .page-link:hover {
    background-color: #f3f4f6;
    color: #1f2937;
}

/* Active State */
.transaction-pagination .page-item.active .page-link {
    background-color: #1f2937;
    color: #ffffff;
    border-color: #1f2937;
}

/* Disabled State */
.transaction-pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background-color: #f9fafb;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .transaction-pagination-wrapper {
        flex-direction: column;
        align-items: center;
    }
}
</style>
@endpush
@endsection