@extends('layouts.app')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('content')

<div class="container-fluid">

    <div class="page-header">

        <div>
            <h1>Dashboard Kasir</h1>
            <p>Ringkasan transaksi penjualan hari ini</p>
        </div>

        <a href="{{ route('transactions.create') }}"
           class="btn btn-primary">
            + Transaksi Baru
        </a>

    </div>


    <div class="dashboard-cards">

        <div class="dashboard-card">

            <div class="dashboard-card-title">
                Transaksi Hari Ini
            </div>

            <div class="dashboard-card-value">
                {{ $transaksiHariIni }}
            </div>

            <div class="dashboard-card-link">
                Transaksi
            </div>

        </div>


        <div class="dashboard-card">

            <div class="dashboard-card-title">
                Penjualan Hari Ini
            </div>

            <div class="dashboard-card-value dashboard-money">
                Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}
            </div>

            <div class="dashboard-card-link">
                Total transaksi hari ini
            </div>

        </div>

    </div>


    <div class="table-card dashboard-table-card">

        <div class="table-card-header">
            Transaksi Terbaru
        </div>

        <div class="table-wrapper">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($transaksiTerbaru as $transaction)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
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
                                {{ strtoupper($transaction->status_pembayaran) }}
                            </td>

                            <td>
                                <a href="{{ route('transactions.show', $transaction) }}"
                                   class="btn btn-primary btn-sm">
                                    Detail
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty-data">
                                Belum ada transaksi.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@push('styles')
<style>

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.dashboard-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 20px;
}

.dashboard-card-title {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 10px;
}

.dashboard-card-value {
    font-size: 26px;
    font-weight: bold;
    color: #1f2937;
}

.dashboard-money {
    font-size: 20px;
}

.dashboard-card-link {
    margin-top: 8px;
    color: #6b7280;
    font-size: 13px;
}

.dashboard-table-card {
    margin-top: 10px;
}

@media (max-width: 600px) {
    .dashboard-cards {
        grid-template-columns: 1fr;
    }
}

</style>
@endpush