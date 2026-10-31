@extends('layouts.app')

@section('title', 'Laporan Pendapatan')
@section('page-title', 'Laporan Pendapatan')

@section('content')

<div class="container-fluid">

    <div class="page-header">

        <div>
            <h1>Laporan Pendapatan</h1>
            <p>Ringkasan Penjualan Dan Pembayaran Transaksi Toko Bangunan</p>
        </div>

    </div>


    {{-- FILTER --}}
    <div class="form-card mb-20">
    <div class="section-title">
        Filter Periode
    </div>
    <form method="GET" action="{{ route('owner.laporan.pendapatan') }}">

        <div class="report-filter-grid">

            <div class="form-group">
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

            <div class="form-group">
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

            <div class="report-filter-actions">

                <button type="submit" class="btn btn-primary">
                    Tampilkan
                </button>

                <a
                    href="{{ route('owner.laporan.pendapatan.pdf', [
                        'tanggal_mulai' => request('tanggal_mulai'),
                        'tanggal_akhir' => request('tanggal_akhir')
                    ]) }}"
                    class="btn btn-danger">
                    Cetak PDF
                </a>

                <a
                    href="{{ route('owner.laporan.pendapatan') }}"
                    class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </div>
    </form>
    </div>


    {{-- RINGKASAN --}}
    <div class="cards"
         style="grid-template-columns:repeat(4, 1fr); margin-bottom:20px;">

        <div class="card">

            <div class="card-title">
                Total Transaksi
            </div>

            <div class="card-value">
                {{ $totalTransaksi }}
            </div>

            <div style="color:#6b7280; margin-top:5px;">
                Transaksi
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Total Nilai Penjualan
            </div>

            <div class="card-value">
                Rp {{ number_format(
                    $totalPenjualan,
                    0,
                    ',',
                    '.'
                ) }}
            </div>

            <div style="color:#6b7280; margin-top:5px;">
                Nilai seluruh transaksi
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Pembayaran Diterima
            </div>

            <div class="card-value">
                Rp {{ number_format(
                    $totalDibayar,
                    0,
                    ',',
                    '.'
                ) }}
            </div>

            <div style="color:#6b7280; margin-top:5px;">
                Uang yang sudah dibayar
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Sisa Pembayaran
            </div>

            <div class="card-value">
                Rp {{ number_format(
                    $totalPiutang,
                    0,
                    ',',
                    '.'
                ) }}
            </div>

            <div style="color:#6b7280; margin-top:5px;">
                Piutang pelanggan
            </div>

        </div>

    </div>


    {{-- TABEL --}}
    <div class="form-card">

        <div class="section-title">
            Rincian Pendapatan
        </div>

        <div style="overflow-x:auto;">

            <table style="width:100%; border-collapse:collapse;">

                <thead>

                    <tr style="border-bottom:1px solid #e5e7eb;">

                        <th style="padding:12px; text-align:left;">
                            No
                        </th>

                        <th style="padding:12px; text-align:left;">
                            Invoice
                        </th>

                        <th style="padding:12px; text-align:left;">
                            Pelanggan
                        </th>

                        <th style="padding:12px; text-align:left;">
                            Tanggal
                        </th>

                        <th style="padding:12px; text-align:right;">
                            Total
                        </th>

                        <th style="padding:12px; text-align:right;">
                            Dibayar
                        </th>

                        <th style="padding:12px; text-align:right;">
                            Sisa
                        </th>

                        <th style="padding:12px; text-align:center;">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($transactions as $index => $transaction)

                        @php

                            $sisa = max(
                                $transaction->total_akhir
                                - $transaction->jumlah_bayar,
                                0
                            );

                        @endphp

                        <tr style="border-bottom:1px solid #e5e7eb;">

                            <td style="padding:12px;">
                                {{ $transactions->firstItem() + $index }}
                            </td>

                            <td style="padding:12px;">

                                <strong>
                                    {{ $transaction->nomor_invoice }}
                                </strong>

                            </td>

                            <td style="padding:12px;">

                                {{ $transaction->customer->nama
                                    ?? 'Pelanggan Umum' }}

                            </td>

                            <td style="padding:12px;">

                                {{ \Carbon\Carbon::parse(
                                    $transaction->tanggal_transaksi
                                )->format('d/m/Y H:i') }}

                            </td>

                            <td style="padding:12px; text-align:right;">

                                Rp {{ number_format(
                                    $transaction->total_akhir,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td style="padding:12px; text-align:right;">

                                Rp {{ number_format(
                                    $transaction->jumlah_bayar,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td style="padding:12px; text-align:right;">

                                Rp {{ number_format(
                                    $sisa,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td style="padding:12px; text-align:center;">

                                @if($transaction->status_pembayaran === 'lunas')

                                    <span style="
                                        background:#dcfce7;
                                        color:#166534;
                                        padding:5px 10px;
                                        border-radius:20px;
                                        font-size:13px;
                                    ">
                                        LUNAS
                                    </span>

                                @else

                                    <span style="
                                        background:#fef3c7;
                                        color:#92400e;
                                        padding:5px 10px;
                                        border-radius:20px;
                                        font-size:13px;
                                    ">
                                        DP
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                style="padding:30px; text-align:center;"
                            >
                                Belum ada transaksi pada periode tersebut.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- BAGIAN PAGINATION --}}
        @if($transactions->hasPages())
            <div class="report-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $transactions->firstItem() }}</strong> - <strong>{{ $transactions->lastItem() }}</strong> dari <strong>{{ $transactions->total() }}</strong> transaksi
                </div>
                <div class="report-pagination">
                    {{ $transactions->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif

    </div>

</div>

@push('styles')

<style>

    .report-filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 20px;
        align-items: end;
    }

    .report-filter-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
        white-space: nowrap;
    }

    /* =========================
       PAGINATION LAPORAN
    ========================= */

    .report-pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 12px 4px 12px;
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
        transition: background-color 0.15s ease, color 0.15s ease;
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
        border-color: #1f2937;
    }

    .report-pagination .page-item.disabled .page-link {
        color: #9ca3af;
        background-color: #f9fafb;
        cursor: not-allowed;
    }

    @media (max-width: 900px) {

        .report-filter-grid {
            grid-template-columns: 1fr 1fr;
        }

        .report-filter-actions {
            grid-column: 1 / -1;
            margin-bottom: 0;
        }

    }

    @media (max-width: 600px) {

        .report-filter-grid {
            grid-template-columns: 1fr;
        }

        .report-filter-actions {
            grid-column: auto;
            flex-wrap: wrap;
        }

        .report-pagination-wrapper {
            flex-direction: column;
            align-items: center;
        }

    }

</style>

@endpush

@endsection