
@extends('layouts.app')

@section('title', 'Detail Retur Penjualan')
@section('page-title', 'Detail Retur Penjualan')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1>Detail Retur Penjualan</h1>
            <p>Informasi barang yang dikembalikan dari transaksi asli.</p>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('transactions.index') }}"
                class="btn btn-primary">
                Daftar Transaksi
            </a>

            <a href="{{ route('sales-returns.index') }}"
                class="btn btn-secondary">
                Daftar Retur
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="form-card mb-20">
        <div class="section-title">Informasi Retur</div>

        <div class="table-wrapper">
            <table class="data-table">
                <tbody>
                    <tr>
                        <th style="width: 200px;">Nomor Retur</th>
                        <td><strong>{{ $salesReturn->nomor_retur }}</strong></td>
                    </tr>

                    <tr>
                        <th>Invoice Asal</th>
                        <td>
                            {{ $salesReturn->transaction->nomor_invoice ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Pelanggan</th>
                        <td>
                            {{ $salesReturn->transaction->customer->nama ?? 'Pelanggan Umum' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Tanggal Retur</th>
                        <td>
                            {{ $salesReturn->tanggal_retur
                                ? \Carbon\Carbon::parse($salesReturn->tanggal_retur)->format('d/m/Y H:i')
                                : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Petugas</th>
                        <td>{{ $salesReturn->user->name ?? '-' }}</td>
                    </tr>

                    <tr>
                        <th>Keterangan</th>
                        <td>{{ $salesReturn->keterangan ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">Detail Barang Retur</div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah Retur</th>
                        <th>Kondisi</th>
                        <th>Alasan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($salesReturn->details as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->product->nama ?? '-' }}</td>
                            <td>
                                Rp {{ number_format($detail->harga, 0, ',', '.') }}
                            </td>
                            <td>{{ $detail->quantity }}</td>
                            <td>
                                {{ $detail->kondisi === 'layak' ? 'Layak jual' : 'Rusak' }}
                            </td>
                            <td>{{ $detail->alasan ?: '-' }}</td>
                            <td>
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-data">
                                Tidak ada detail barang retur.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align: right;">
                            Total Nilai Retur
                        </th>
                        <th>
                            Rp {{ number_format(
                                $salesReturn->details->sum('subtotal'),
                                0,
                                ',',
                                '.'
                            ) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
