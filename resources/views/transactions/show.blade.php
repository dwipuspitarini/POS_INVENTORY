@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')

<div class="container-fluid">

    {{-- HEADER HALAMAN (TIDAK IKUT DICETAK) --}}
    <div class="page-header no-print d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Detail Transaksi
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Informasi lengkap transaksi penjualan
            </p>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                Kembali
            </a>

            @if($transaction->status_pembayaran === 'dp')
                <a href="{{ route('transactions.pelunasan.form', $transaction) }}" class="btn btn-warning">
                    Pelunasan
                </a>
            @endif

            <button
                type="button"
                onclick="window.print()"
                class="btn btn-primary"
                style="background: #1f2937; border-color: #1f2937;"
            >
                Cetak Nota
            </button>
        </div>
    </div>

    {{-- STRUK THERMAL KASIR --}}
    <div class="thermal-receipt-wrapper">
        <div class="thermal-receipt">

            {{-- HEADER TOKO --}}
            <div class="receipt-store-header">
                <div class="store-name">TB BANGUN REJEKI MRANGGEN</div>
                <div class="store-address">
                    Jl. Raya Waru No. 117, Area Sawah/Kebun, Tamansari,<br>
                    Kec. Mranggen, Kab. Demak, Jawa Tengah<br>
                    Telp / HP : 0898900200
                </div>
            </div>

            <div class="receipt-divider">-------------------------------------------------------------------------</div>

            {{-- INFO TRANSAKSI & KASIR --}}
            <div class="receipt-meta">
                <div class="meta-row">
                    <span>{{ $transaction->tanggal_transaksi->format('d.m.y - H:i') }}</span>
                    <span>{{ strtoupper($transaction->user->name ?? 'KASIR') }}</span>
                </div>
                <div class="meta-row">
                    <span>INV : {{ $transaction->nomor_invoice }}</span>
                    <span>{{ strtoupper($transaction->customer->nama ?? 'UMUM') }}</span>
                </div>
                @if($transaction->customer && $transaction->customer->telepon)
                    <div class="meta-row">
                        <span>HP  : {{ $transaction->customer->telepon }}</span>
                        <span>{{ strtoupper($transaction->status_pembayaran) }}</span>
                    </div>
                @endif
            </div>

            <div class="receipt-divider">-------------------------------------------------------------------------</div>

            {{-- DAFTAR ITEM (TANPA NOMOR URUT) --}}
            <div class="receipt-items">
                @foreach($transaction->details as $detail)
                    <div class="item-row">
                        <div class="item-name">
                            {{ strtoupper($detail->product->nama ?? '-') }}
                        </div>
                        <div class="item-qty">
                            {{ $detail->quantity }}
                        </div>
                        <div class="item-price">
                            {{ number_format($detail->harga, 0, ',', ',') }}
                        </div>
                        <div class="item-subtotal">
                            {{ number_format($detail->subtotal, 0, ',', ',') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="receipt-divider">-------------------------------------------------------------------------</div>

            

{{-- RINGKASAN PEMBAYARAN --}}
<div class="receipt-totals">

    <div class="total-line">
        <span class="total-label">HARGA JUAL</span>
        <span class="total-colon">:</span>
        <span class="total-value">{{ number_format($transaction->total, 0, ',', '.') }}</span>
    </div>

    @if($transaction->diskon > 0)
        <div class="total-line">
            <span class="total-label">DISKON</span>
            <span class="total-colon">:</span>
            <span class="total-value">({{ number_format($transaction->diskon, 0, ',', '.') }})</span>
        </div>
    @endif

    <div class="receipt-divider-short">
        -----------------------------------------
    </div>

    <div class="total-line bold-line">
        <span class="total-label">TOTAL AKHIR</span>
        <span class="total-colon">:</span>
        <span class="total-value">{{ number_format($transaction->total_akhir, 0, ',', '.') }}</span>
    </div>

    @if((float) $transaction->jumlah_dp > 0)
        <div class="total-line">
            <span class="total-label">DP AWAL</span>
            <span class="total-colon">:</span>
            <span class="total-value">{{ number_format($transaction->jumlah_dp, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="total-line">
        <span class="total-label">METODE BAYAR</span>
        <span class="total-colon">:</span>
        <span class="total-value">{{ strtoupper($transaction->metode_pembayaran) }}</span>
    </div>

    <div class="total-line">
        <span class="total-label">JUMLAH DIBAYAR</span>
        <span class="total-colon">:</span>
        <span class="total-value">{{ number_format($transaction->jumlah_bayar, 0, ',', '.') }}</span>
    </div>

    
{{-- UANG DITERIMA DAN KEMBALIAN PEMBAYARAN AWAL --}}
@if($transaction->metode_pembayaran === 'tunai' && $transaction->uang_diterima !== null)

    @php
        // Jika transaksi DP, gunakan DP awal sebagai dasar kembalian.
        // Jika bukan DP, gunakan jumlah pembayaran transaksi.
        $dasarPembayaranAwal = (float) $transaction->jumlah_dp > 0
            ? (float) $transaction->jumlah_dp
            : (float) $transaction->jumlah_bayar;

        $kembalianAwal = max(
            (float) $transaction->uang_diterima - $dasarPembayaranAwal,
            0
        );
    @endphp

    <div class="total-line">
        <span class="total-label">UANG DITERIMA</span>
        <span class="total-colon">:</span>
        <span class="total-value">
            {{ number_format($transaction->uang_diterima, 0, ',', '.') }}
        </span>
    </div>

    <div class="total-line">
        <span class="total-label">KEMBALIAN</span>
        <span class="total-colon">:</span>
        <span class="total-value">
            {{ number_format($kembalianAwal, 0, ',', '.') }}
        </span>
    </div>

@endif


    {{-- INFORMASI PELUNASAN DP --}}
    @if($transaction->status_pembayaran === 'lunas' && (float) $transaction->jumlah_dp > 0)

        <div class="receipt-divider-short">
            -----------------------------------------
        </div>

        <div class="total-line">
            <span class="total-label">PELUNASAN</span>
            <span class="total-colon">:</span>
            <span class="total-value">
                {{ number_format(max((float) $transaction->total_akhir - (float) $transaction->jumlah_dp, 0), 0, ',', '.') }}
            </span>
        </div>

        @if($transaction->metode_pembayaran === 'tunai' && $transaction->uang_diterima_pelunasan !== null)
            <div class="total-line">
                <span class="total-label">UANG DITERIMA</span>
                <span class="total-colon">:</span>
                <span class="total-value">{{ number_format($transaction->uang_diterima_pelunasan, 0, ',', '.') }}</span>
            </div>

            <div class="total-line">
                <span class="total-label">KEMBALIAN</span>
                <span class="total-colon">:</span>
                <span class="total-value">
                    {{ number_format(max((float) $transaction->uang_diterima_pelunasan - ((float) $transaction->total_akhir - (float) $transaction->jumlah_dp), 0), 0, ',', '.') }}
                </span>
            </div>
        @endif

    @endif

    {{-- SISA PEMBAYARAN --}}
    @if($transaction->status_pembayaran === 'dp')
        @php
            $sisa = max(
                (float) $transaction->total_akhir -
                (float) $transaction->jumlah_bayar,
                0
            );
        @endphp

        <div class="total-line">
            <span class="total-label">SISA BAYAR</span>
            <span class="total-colon">:</span>
            <span class="total-value">{{ number_format($sisa, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="total-line">
        <span class="total-label">STATUS</span>
        <span class="total-colon">:</span>
        <span class="total-value">{{ strtoupper($transaction->status_pembayaran) }}</span>
    </div>

    @if($transaction->diskon > 0)
        <div class="total-line">
            <span class="total-label">ANDA HEMAT</span>
            <span class="total-colon">:</span>
            <span class="total-value">{{ number_format($transaction->diskon, 0, ',', '.') }}</span>
        </div>
    @endif

</div>

<br>

            <br>

            {{-- FOOTER STRUK --}}
            <div class="receipt-footer">
                <div>TERIMA KASIH. SELAMAT BELANJA KEMBALI</div>
                <div>===========================================================</div>
            </div>

        </div>
    </div>

</div>

@push('styles')
<style>
.thermal-receipt-wrapper {
    display: flex;
    justify-content: center;
    padding: 10px 0 30px 0;
}

.thermal-receipt {
    width: 400px;
    max-width: 100%;
    background: #ffffff;
    color: #1f2937;
    font-family: inherit;
    font-size: 13px;
    line-height: 1.45;
    padding: 24px 20px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    box-sizing: border-box;
}

.receipt-store-header {
    text-align: center;
    margin-bottom: 6px;
}

.store-name {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
    color: #1f2937;
}

.store-address {
    font-size: 12px;
    line-height: 1.45;
    color: #374151;
}

.receipt-divider {
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
    margin: 6px 0;
    color: #6b7280;
    letter-spacing: 0.5px;
}

.receipt-divider-short {
    text-align: right;
    overflow: hidden;
    white-space: nowrap;
    margin: 4px 0;
    color: #6b7280;
    letter-spacing: 0.5px;
}

.receipt-meta {
    font-size: 12px;
    color: #374151;
}

.meta-row {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 2px;
}

/* BARIS ITEM TANPA KOLOM NO */
.receipt-items {
    margin: 4px 0;
}

.item-row {
    display: grid;
    grid-template-columns: 1fr 34px 66px 76px;
    gap: 4px;
    align-items: start;
    margin-bottom: 5px;
    font-size: 12.5px;
}

.item-name {
    word-break: break-word;
    padding-right: 4px;
    font-weight: 500;
}

.item-qty {
    text-align: right;
}

.item-price {
    text-align: right;
}

.item-subtotal {
    text-align: right;
    font-weight: 600;
}

/* TOTAL & PEMBAYARAN */
.receipt-totals {
    margin-top: 4px;
    font-size: 12.5px;
}

.total-line {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 3px;
}

.total-label {
    width: 135px;
    text-align: right;
    padding-right: 8px;
}

.total-colon {
    width: 12px;
    text-align: center;
}

.total-value {
    width: 92px;
    text-align: right;
}

.bold-line {
    font-weight: 700;
    font-size: 13.5px;
}

/* FOOTER */
.receipt-footer {
    text-align: center;
    margin-top: 16px;
    font-size: 11.5px;
    line-height: 1.5;
    color: #374151;
    overflow: hidden;
}

/* =========================================================
   MODE CETAK (PRINT THERMAL)
========================================================= */
@media print {
    @page {
        margin: 4mm;
    }

    body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .sidebar,
    .navbar,
    .no-print,
    .alert {
        display: none !important;
    }

    .main,
    .content,
    .container-fluid {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .thermal-receipt-wrapper {
        padding: 0 !important;
        display: block !important;
    }

    .thermal-receipt {
        width: 100% !important;
        max-width: 80mm !important;
        margin: 0 auto !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        color: #000000 !important;
    }

    .store-name,
    .store-address,
    .receipt-meta,
    .receipt-divider,
    .receipt-divider-short,
    .receipt-footer {
        color: #000000 !important;
    }
}
</style>
@endpush

@endsection