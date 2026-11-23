@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="container-fluid detail-laporan-page">

    {{-- =========================
        HEADER HALAMAN
    ========================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Detail Transaksi
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Rincian penjualan dan retur berdasarkan invoice.
            </p>
        </div>

        <a href="{{ route('owner.laporan.penjualan', request()->query()) }}"
           class="btn btn-secondary">
            Kembali ke Laporan
        </a>
    </div>

    {{-- =========================
        INFORMASI INVOICE
    ========================== --}}
    <div class="form-card mb-20">
        <div class="section-title mb-4">
            Informasi Transaksi
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nomor Invoice</span>
                <span class="info-value">{{ $transaction->nomor_invoice }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Tanggal Transaksi</span>
                <span class="info-value">
                    {{ $transaction->tanggal_transaksi
                        ? \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d/m/Y H:i')
                        : '-' }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $transaction->user->name ?? '-' }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">Pelanggan</span>
                <span class="info-value">{{ $transaction->customer->nama ?? 'Pelanggan Umum' }}</span>
            </div>
        </div>
    </div>

    {{-- =========================
        DETAIL PRODUK TERJUAL
    ========================== --}}
    <div class="table-card mb-20">
        <div class="table-card-header">
            <h2>Detail Produk Terjual</h2>
        </div>

        <div class="card-body-wrapper">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th style="min-width: 220px;">Produk</th>
                            <th style="min-width: 150px;">Kategori</th>
                            <th style="text-align: right; min-width: 140px;">Harga</th>
                            <th style="text-align: center; width: 100px;">Jumlah</th>
                            <th style="text-align: right; min-width: 150px;">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transaction->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $detail->product->nama ?? '-' }}</strong>
                                    @if(!empty($detail->product->sku))
                                        <div style="font-size: 13px; color: #6b7280; margin-top: 2px;">
                                            SKU: {{ $detail->product->sku }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $detail->product->category->nama ?? '-' }}</td>
                                <td style="text-align: right;">
                                    Rp {{ number_format((float) $detail->harga, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="qty-badge">{{ $detail->quantity }}</span>
                                </td>
                                <td style="text-align: right; font-weight: 600;">
                                    Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-table">
                                    Tidak ada detail produk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================
        RINGKASAN PEMBAYARAN
    ========================== --}}
    <div class="form-card mb-20">
        <div class="section-title mb-4">
            Ringkasan Pembayaran
        </div>

        <div class="payment-summary-box">
            <div class="summary-line">
                <span class="summary-label">Total sebelum diskon</span>
                <span class="summary-value">
                    Rp {{ number_format((float) $transaction->total, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-line">
                <span class="summary-label">Diskon</span>
                <span class="summary-value" style="color: #dc2626;">
                    - Rp {{ number_format((float) $transaction->diskon, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-line summary-total">
                <span class="summary-label">Total Akhir</span>
                <span class="summary-value">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-line">
                <span class="summary-label">Jumlah Dibayar</span>
                <span class="summary-value">
                    Rp {{ number_format($dibayar, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-line">
                <span class="summary-label">Sisa Pembayaran</span>
                <span class="summary-value" style="font-weight: 600; color: {{ $sisa > 0 ? '#b45309' : '#1f2937' }};">
                    Rp {{ number_format($sisa, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-line" style="margin-top: 8px;">
                <span class="summary-label">Status Pembayaran</span>
                <span class="summary-value">
                    @if(strtolower($transaction->status_pembayaran ?? '') === 'lunas')
                        <span class="status-badge status-lunas">LUNAS</span>
                    @else
                        <span class="status-badge status-dp">
                            {{ strtoupper($transaction->status_pembayaran ?? '-') }}
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- =========================
        INFORMASI RETUR PENJUALAN
    ========================== --}}
    <div class="table-card mb-20">
        <div class="table-card-header">
            <h2>Informasi Retur Penjualan</h2>
        </div>

        <div class="card-body-wrapper">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th style="min-width: 180px;">Nomor Retur</th>
                            <th style="min-width: 160px;">Tanggal Retur</th>
                            <th style="min-width: 220px;">Produk</th>
                            <th style="text-align: center; width: 100px;">Jumlah</th>
                            <th style="text-align: right; min-width: 150px;">Subtotal Retur</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($returns as $return)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $return->salesReturn->nomor_retur ?? '-' }}</strong>
                                </td>
                                <td>
                                    {{ $return->salesReturn && $return->salesReturn->tanggal_retur
                                        ? \Carbon\Carbon::parse($return->salesReturn->tanggal_retur)->format('d/m/Y H:i')
                                        : '-' }}
                                </td>
                                <td>{{ $return->product->nama ?? '-' }}</td>
                                <td style="text-align: center;">
                                    <span class="qty-badge qty-retur">{{ $return->quantity }}</span>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: #dc2626;">
                                    Rp {{ number_format((float) $return->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-table">
                                    Belum ada retur untuk transaksi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
.detail-laporan-page .mb-20 {
    margin-bottom: 20px;
}

/* =========================================================
   CARD & SECTION TITLE
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

/* =========================================================
   INFORMASI TRANSAKSI GRID
========================================================= */
.info-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 14px 16px;
    background-color: #f9fafb;
    border: 1px solid #f3f4f6;
    border-radius: 6px;
}

.info-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
}

.info-value {
    font-size: 15px;
    font-weight: 700;
    color: #1f2937;
    word-break: break-word;
}

/* =========================================================
   TABLE CARD & DATA TABLE
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

.card-body-wrapper {
    padding: 24px;
}

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
    padding: 14px 16px;
    font-size: 15px;
    color: #1f2937;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.data-table tbody tr:hover {
    background-color: #f9fafb;
}

.empty-table {
    text-align: center;
    padding: 36px 20px !important;
    font-size: 15px;
    color: #6b7280;
}

/* =========================================================
   RINGKASAN PEMBAYARAN
========================================================= */
.payment-summary-box {
    max-width: 460px;
    margin-left: auto;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px 24px;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 15px;
}

.summary-label {
    color: #4b5563;
}

.summary-value {
    color: #1f2937;
    font-weight: 500;
}

.summary-divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 10px 0;
}

.summary-total .summary-label,
.summary-total .summary-value {
    font-size: 17px;
    font-weight: 700;
    color: #1f2937;
}

/* =========================================================
   BADGES
========================================================= */
.qty-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    background: #f3f4f6;
    color: #1f2937;
}

.qty-retur {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

.status-lunas {
    background: #dcfce7;
    color: #166534;
}

.status-dp {
    background: #fef3c7;
    color: #92400e;
}

/* =========================================================
   RESPONSIVE
========================================================= */
@media (max-width: 992px) {
    .info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    .payment-summary-box {
        max-width: 100%;
    }
}
</style>
@endpush
@endsection