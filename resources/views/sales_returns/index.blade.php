
@extends('layouts.app')

@section('title', 'Daftar Retur Penjualan')
@section('page-title', 'Daftar Retur Penjualan')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1>Daftar Retur Penjualan</h1>
            <p>Riwayat pengembalian barang berdasarkan invoice asli.</p>
        </div>

        <a href="{{ route('transactions.index') }}" class="btn btn-primary">
            Pilih Transaksi untuk Retur
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Retur</th>
                        <th>Invoice Asal</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Retur</th>
                        <th>Jumlah Jenis Barang</th>
                        <th>Total Nilai Retur</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($returns as $return)
                        <tr>
                            <td>{{ $returns->firstItem() + $loop->index }}</td>

                            <td>
                                <strong>{{ $return->nomor_retur }}</strong>
                            </td>

                            <td>
                                {{ $return->transaction->nomor_invoice ?? '-' }}
                            </td>

                            <td>
                                {{ $return->transaction->customer->nama ?? 'Pelanggan Umum' }}
                            </td>

                            <td>
                                {{ $return->tanggal_retur
                                    ? \Carbon\Carbon::parse($return->tanggal_retur)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            <td>{{ $return->details->count() }}</td>

                            <td>
                                Rp {{ number_format(
                                    $return->details->sum('subtotal'),
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td>{{ $return->user->name ?? '-' }}</td>

                            <td>
                                <a href="{{ route('sales-returns.show', $return) }}"
                                    class="btn btn-primary btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-data">
                                Belum ada retur penjualan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($returns->hasPages())
            <div class="report-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan {{ $returns->firstItem() }} -
                    {{ $returns->lastItem() }} dari
                    {{ $returns->total() }} retur
                </div>

                <div class="report-pagination">
                    {{ $returns->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
