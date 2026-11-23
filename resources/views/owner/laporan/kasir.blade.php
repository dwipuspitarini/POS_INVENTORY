
@extends('layouts.app')

@section('title', 'Laporan Kasir')
@section('page-title', 'Laporan Kasir')

@section('content')

<div class="container-fluid">

    <div class="page-header mb-4">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">
                Laporan Kasir
            </h1>
            <p style="color: #6b7280; margin-bottom: 0;">
                Rekap transaksi, diskon, retur, dan pembayaran berdasarkan kasir dan periode
            </p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="form-card mb-20">
        <div class="section-title mb-3" style="font-weight: 700; font-size: 16px; color: #1f2937;">
            Filter Laporan
        </div>

        <form method="GET" action="{{ route('laporan.kasir') }}">

            @if(auth()->user()->role === 'owner')
                <div class="form-group mb-3">
                    <label for="kasir_id" class="form-label">Filter Kasir</label>
                    <select name="kasir_id" id="kasir_id" class="form-control">
                        <option value="">Semua Kasir</option>
                        @foreach($kasirs as $kasir)
                            <option value="{{ $kasir->id }}"
                                {{ (string) $kasirId === (string) $kasir->id ? 'selected' : '' }}>
                                {{ $kasir->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="filter-bottom-row">

                <div class="form-group mb-0">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        class="form-control"
                        value="{{ $tanggalMulai }}"
                    >
                </div>

                <div class="form-group mb-0">
                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                    <input
                        type="date"
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        class="form-control"
                        value="{{ $tanggalAkhir }}"
                    >
                </div>

                <div class="form-group mb-0 d-flex flex-column justify-content-end">
                    <div class="report-filter-actions">
                        <button type="submit" class="btn btn-primary">
                            Tampilkan
                        </button>

                        <a href="{{ route('laporan.kasir.pdf', [ 'tanggal_mulai' => $tanggalMulai,
                        'tanggal_akhir' => $tanggalAkhir, 'kasir_id' => $kasirId ]) }}"
                        target="_blank" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i>
                        Cetak PDF
                        </a>
                        <a href="{{ route('laporan.kasir') }}"
                            class="btn btn-secondary">
                            Reset
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- RINGKASAN TRANSAKSI --}}
    <div class="cards mb-20">

        <div class="card">
            <div class="card-title">Jumlah Transaksi</div>
            <div class="card-value">
                {{ number_format($transaksi->count(), 0, ',', '.') }}
            </div>
            <div class="card-note">Jumlah invoice asli</div>
        </div>

        <div class="card">
            <div class="card-title">Penjualan Kotor</div>
            <div class="card-value">
                Rp {{ number_format($totalPenjualanKotor, 0, ',', '.') }}
            </div>
            <div class="card-note">Sebelum diskon dan retur</div>
        </div>

        <div class="card">
            <div class="card-title">Total Diskon</div>
            <div class="card-value">
                Rp {{ number_format($totalDiskon, 0, ',', '.') }}
            </div>
        </div>

        <div class="card">
            <div class="card-title">Total Retur</div>
            <div class="card-value">
                Rp {{ number_format($totalRetur, 0, ',', '.') }}
            </div>
            <div class="card-note">
                {{ number_format($retur->count(), 0, ',', '.') }} transaksi retur
            </div>
        </div>

    </div>

    {{-- PENJUALAN BERSIH --}}
    <div class="cards mb-20">
        <div class="card card-net">
            <div class="card-title">Total Penjualan Bersih</div>
            <div class="card-value">
                Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
            </div>
            <div class="card-note">
                Penjualan setelah dikurangi diskon dan retur
            </div>
        </div>
    </div>

    {{-- RINGKASAN PEMBAYARAN --}}
    <div class="section-heading mb-3">
        Ringkasan Pembayaran
    </div>

    <div class="cards mb-20">

        @foreach($ringkasanMetode as $metode)
            <div class="card">
                <div class="card-title">
                    Pembayaran {{ $metode['nama'] }}
                </div>

                <div class="card-value">
                    Rp {{ number_format($metode['total'], 0, ',', '.') }}
                </div>

                <div class="card-note">
                    {{ number_format($metode['jumlah_transaksi'], 0, ',', '.') }}
                    transaksi
                </div>
            </div>
        @endforeach

    </div>

    {{-- DETAIL TRANSAKSI --}}
    <div class="table-card mb-20">

        <div class="table-card-header">
            Detail Transaksi Kasir
        </div>

        <div class="table-wrapper">
            <table class="data-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Kasir</th>
                        <th>Tanggal</th>
                        <th>Metode Pembayaran</th>
                        <th>Diskon</th>
                        <th>Total Transaksi</th>
                        <th>Jumlah Dibayar</th>
                        <th>Sisa Pembayaran</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transaksi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $item->nomor_invoice }}</td>

                            <td>{{ $item->user->name ?? '-' }}</td>

                            <td>
                                {{ $item->tanggal_transaksi
                                    ? \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            <td>
                                {{ strtoupper($item->metode_pembayaran ?? '-') }}
                            </td>

                            <td>
                                Rp {{ number_format($item->diskon, 0, ',', '.') }}
                            </td>

                            <td>
                                Rp {{ number_format($item->total_akhir, 0, ',', '.') }}
                            </td>

                            <td>
                                Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                            </td>

                            <td>
                                Rp {{ number_format(
                                    max((float) $item->total_akhir - (float) $item->jumlah_bayar, 0),
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td>
                                {{ strtoupper($item->status_pembayaran ?? '-') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-data">
                                Tidak ada transaksi pada periode yang dipilih.
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
.filter-bottom-row {
    display: flex;
    gap: 15px;
    align-items: flex-end;
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
    height: 40px;
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
    height: 40px;
    padding: 9px 12px;
    font-size: 14px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
}

.section-heading {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
}

.card-note {
    font-size: 13px;
    color: #6b7280;
    margin-top: 5px;
}

.card-net {
    border-left: 4px solid #16a34a;
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
