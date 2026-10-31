@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')

<div class="container-fluid">

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
    @endif

    {{-- HEADER --}}
    <div class="page-header no-print">

        <div>
            <h1>Detail Transaksi</h1>
            <p>Informasi lengkap transaksi penjualan</p>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                Kembali</a>
                @if($transaction->status_pembayaran === 'dp')
                <a href="{{ route( 'transactions.pelunasan.form', $transaction ) }}"
                class="btn btn-warning">Pelunasan</a>
                @endif
                <button type="button" onclick="window.print()" class="btn btn-primary">
                    Cetak Nota</button>
        </div>

    </div>


    {{-- NOTA --}}
    <div class="receipt-card">

        {{-- HEADER NOTA --}}
        <div class="receipt-header">

            <div>
                <h2>TOKO BANGUNAN</h2>
                <p>Nota Penjualan</p>
            </div>

            <div class="receipt-info">

                <div>
                    <span>No. Invoice</span>
                    <strong>
                        {{ $transaction->nomor_invoice }}
                    </strong>
                </div>

                <div>
                    <span>Tanggal</span>
                    <strong>
                        {{ $transaction->tanggal_transaksi->format('d/m/Y H:i') }}
                    </strong>
                </div>

            </div>

        </div>


        {{-- DATA TRANSAKSI --}}
        <div class="receipt-customer">

            <div>
                <span>Pelanggan</span>

                <strong>
                    {{ $transaction->customer->nama ?? 'Pelanggan Umum' }}
                </strong>
            </div>

            @if($transaction->customer)

                <div>
                    <span>Telepon</span>

                    <strong>
                        {{ $transaction->customer->telepon }}
                    </strong>
                </div>

            @endif

            <div>
                <span>Kasir</span>

                <strong>
                    {{ $transaction->user->name ?? '-' }}
                </strong>
            </div>

        </div>


        {{-- DETAIL PRODUK --}}
        <div class="table-wrapper">

            <table class="data-table receipt-table">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($transaction->details as $detail)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $detail->product->nama ?? '-' }}
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

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- RINGKASAN PEMBAYARAN --}}
        <div class="receipt-summary">

            <div class="summary-row">

                <span>Total</span>

                <strong>
                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                </strong>

            </div>


            <div class="summary-row">

                <span>Diskon</span>

                <strong>
                    Rp {{ number_format($transaction->diskon, 0, ',', '.') }}
                </strong>

            </div>


            <div class="summary-row total-row">

                <span>Total Akhir</span>

                <strong>
                    Rp {{ number_format($transaction->total_akhir, 0, ',', '.') }}
                </strong>

            </div>


            <div class="summary-row">

                <span>Metode Pembayaran</span>

                <strong>
                    {{ strtoupper($transaction->metode_pembayaran) }}
                </strong>

            </div>


            <div class="summary-row">

                <span>Status Pembayaran</span>

                <strong>
                    {{ strtoupper($transaction->status_pembayaran) }}
                </strong>

            </div>


            <div class="summary-row">

                <span>Jumlah Bayar</span>

                <strong>
                    Rp {{ number_format($transaction->jumlah_bayar, 0, ',', '.') }}
                </strong>

            </div>


            @if($transaction->status_pembayaran === 'lunas')

                @php
                    $kembalian =
                        max(
                            $transaction->jumlah_bayar
                            - $transaction->total_akhir,
                            0
                        );
                @endphp

                <div class="summary-row">

                    <span>Kembalian</span>

                    <strong>
                        Rp {{ number_format($kembalian, 0, ',', '.') }}
                    </strong>

                </div>

            @else

                @php
                    $sisa =
                        max(
                            $transaction->total_akhir
                            - $transaction->jumlah_bayar,
                            0
                        );
                @endphp

                <div class="summary-row">

                    <span>Sisa Pembayaran</span>

                    <strong>
                        Rp {{ number_format($sisa, 0, ',', '.') }}
                    </strong>
                </div>
            @endif
        </div>
        {{-- FOOTER --}}
        <div class="receipt-footer">
            <p>
                Terima kasih telah melakukan transaksi.
            </p>
        </div>
    </div>
</div>

@push('styles')

<style>
.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.receipt-card {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 30px;
}

.receipt-header {
    display: flex;
    justify-content: space-between;
    gap: 30px;
    padding-bottom: 25px;
    border-bottom: 2px solid #1f2937;
}

.receipt-header h2 {
    margin: 0 0 5px 0;
    font-size: 28px;
}

.receipt-header p {
    margin: 0;
    color: #6b7280;
}

.receipt-info {
    min-width: 240px;
}

.receipt-info > div {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 8px;
}

.receipt-info span,
.receipt-customer span {
    color: #6b7280;
    font-size: 14px;
}

.receipt-info strong,
.receipt-customer strong {
    font-size: 14px;
}

.receipt-customer {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    padding: 20px 0;
}

.receipt-customer > div {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.receipt-table {
    margin-top: 10px;
}

.receipt-summary {
    width: 420px;
    max-width: 100%;
    margin-left: auto;
    margin-top: 25px;
}

.receipt-summary .summary-row {
    padding: 10px 0;
}

.receipt-footer {
    text-align: center;
    margin-top: 35px;
    padding-top: 20px;
    border-top: 1px dashed #9ca3af;
    color: #6b7280;
}

.receipt-footer p {
    margin: 0;
}

@media (max-width: 700px) {

    .receipt-card {
        padding: 20px;
    }

    .receipt-header {
        flex-direction: column;
    }

    .receipt-info {
        min-width: 0;
        width: 100%;
    }

    .receipt-customer {
        grid-template-columns: 1fr;
    }

}

@media print {

    body {
        background: white !important;
    }

    .sidebar,
    .navbar,
    .no-print {
        display: none !important;
    }

    .main {
        width: 100%;
    }

    .content {
        padding: 0 !important;
    }

    .receipt-card {
        max-width: none;
        border: none;
        box-shadow: none;
        border-radius: 0;
        padding: 0;
    }

    .table-wrapper {
        overflow: visible;
    }

    .data-table th,
    .data-table td {
        border: 1px solid #333;
    }

}

</style>

@endpush

@endsection