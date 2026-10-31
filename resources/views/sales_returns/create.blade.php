
@extends('layouts.app')

@section('title', 'Retur Penjualan')
@section('page-title', 'Retur Penjualan')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1>Retur Penjualan</h1>
            <p>Pilih invoice dan tentukan barang yang dikembalikan pelanggan.</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali data retur:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PILIH INVOICE --}}
    <div class="form-card mb-20">
        <div class="section-title">Pilih Invoice Asal</div>

        <form method="GET" action="{{ route('sales-returns.create') }}">
            <div class="form-group">
                <label for="transaksi_id" class="form-label">
                    Nomor Invoice
                </label>

                <select name="transaksi_id" id="transaksi_id"
                    class="form-control" required>
                    <option value="">-- Pilih Invoice --</option>

                    @foreach ($transactions as $item)
                        <option value="{{ $item->id }}"
                            @selected(
                                (string) request('transaksi_id') === (string) $item->id
                                || ($transaction && $transaction->id === $item->id)
                            )>
                            {{ $item->nomor_invoice }}
                            — {{ $item->customer->nama ?? 'Pelanggan Umum' }}
                            — Rp {{ number_format($item->total_akhir, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Tampilkan Barang
            </button>
        </form>
    </div>

    @if ($transaction)
        {{-- INFORMASI INVOICE --}}
        <div class="form-card mb-20">
            <div class="section-title">Informasi Transaksi</div>

            <div class="table-wrapper">
                <table class="data-table">
                    <tbody>
                        <tr>
                            <th style="width: 180px;">Invoice</th>
                            <td>{{ $transaction->nomor_invoice }}</td>
                        </tr>
                        <tr>
                            <th>Pelanggan</th>
                            <td>{{ $transaction->customer->nama ?? 'Pelanggan Umum' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Transaksi</th>
                            <td>
                                {{ $transaction->tanggal_transaksi
                                    ? \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Total Invoice</th>
                            <td>
                                Rp {{ number_format($transaction->total_akhir, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FORM RETUR --}}
        <form method="POST" action="{{ route('sales-returns.store') }}">
            @csrf

            <input type="hidden" name="transaksi_id"
                value="{{ $transaction->id }}">

            <div class="table-card mb-20">
                <div class="table-card-header">Barang yang Dibeli</div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah Beli</th>
                                <th>Sudah Diretur</th>
                                <th>Sisa Retur</th>
                                <th>Jumlah Retur</th>
                                <th>Kondisi</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($details as $detail)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $detail->product->nama ?? 'Produk tidak ditemukan' }}
                                        </strong>
                                    </td>

                                    <td>
                                        Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                    </td>

                                    <td>{{ $detail->quantity }}</td>

                                    <td>{{ $detail->jumlah_sudah_diretur }}</td>

                                    <td>
                                        <strong>{{ $detail->sisa_retur }}</strong>
                                    </td>

                                    <td style="min-width: 110px;">
                                        <input type="number"
                                            class="form-control"
                                            name="items[{{ $detail->id }}][quantity]"
                                            min="0"
                                            max="{{ $detail->sisa_retur }}"
                                            step="1"
                                            value="{{ old('items.' . $detail->id . '.quantity', 0) }}"
                                            @disabled($detail->sisa_retur <= 0)
                                            aria-label="Jumlah retur {{ $detail->product->nama ?? 'produk' }}">

                                        <small class="text-muted">
                                            Maks. {{ $detail->sisa_retur }}
                                        </small>
                                    </td>

                                    <td style="min-width: 135px;">
                                        <select class="form-control"
                                            name="items[{{ $detail->id }}][kondisi]"
                                            @disabled($detail->sisa_retur <= 0)>
                                            <option value="">Pilih</option>
                                            <option value="layak"
                                                @selected(old('items.' . $detail->id . '.kondisi') === 'layak')>
                                                Layak jual
                                            </option>
                                            <option value="rusak"
                                                @selected(old('items.' . $detail->id . '.kondisi') === 'rusak')>
                                                Rusak
                                            </option>
                                        </select>
                                    </td>

                                    <td style="min-width: 170px;">
                                        <input type="text"
                                            class="form-control"
                                            name="items[{{ $detail->id }}][alasan]"
                                            maxlength="255"
                                            placeholder="Alasan retur"
                                            value="{{ old('items.' . $detail->id . '.alasan') }}"
                                            @disabled($detail->sisa_retur <= 0)>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-data">
                                        Tidak ada detail barang pada invoice ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-card mb-20">
                <div class="form-group">
                    <label for="keterangan" class="form-label">
                        Keterangan Retur (opsional)
                    </label>

                    <textarea name="keterangan" id="keterangan"
                        class="form-control" rows="3"
                        maxlength="1000"
                        placeholder="Keterangan tambahan retur">{{ old('keterangan') }}</textarea>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Simpan retur untuk invoice ini? Pastikan jumlah dan kondisi barang sudah benar.')">
                        Simpan Retur
                    </button>

                    <a href="{{ route('transactions.index') }}"
                        class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
