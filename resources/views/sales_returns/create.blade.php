@extends('layouts.app')

@section('title', 'Retur Penjualan')
@section('page-title', 'Retur Penjualan')

@section('content')
<div class="container-fluid sales-return-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Retur Penjualan
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Tentukan jumlah dan kondisi barang yang dikembalikan pelanggan.
            </p>
        </div>

        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================================================
         ERROR VALIDASI
    ========================================================== --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 8px; font-size: 15px;">
            <strong>Periksa kembali data retur:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($transaction)
        {{-- =========================================================
             INFORMASI INVOICE (PERMANEN)
        ========================================================== --}}
        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Informasi Transaksi Asal
            </div>

            <div class="invoice-info-grid">
                <div class="info-item">
                    <span class="info-label">Nomor Invoice</span>
                    <div class="info-value-box">
                        <strong>{{ $transaction->nomor_invoice }}</strong>
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Pelanggan</span>
                    <div class="info-value-box">
                        {{ $transaction->customer->nama ?? 'Pelanggan Umum' }}
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Tanggal Transaksi</span>
                    <div class="info-value-box">
                        {{ $transaction->tanggal_transaksi
                            ? \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d/m/Y H:i')
                            : '-' }}
                    </div>
                </div>

                <div class="info-item">
                    <span class="info-label">Total Invoice</span>
                    <div class="info-value-box">
                        <strong>Rp {{ number_format($transaction->total_akhir, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
             FORM RETUR BARANG
        ========================================================== --}}
        <form method="POST" action="{{ route('sales-returns.store') }}">
            @csrf
            <input type="hidden" name="transaksi_id" value="{{ $transaction->id }}">

            <div class="table-card mb-20">
                <div class="table-card-header">
                    <h2>Barang yang Dibeli</h2>
                </div>

                <div class="card-body-wrapper" style="padding: 20px;">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 42px;">No</th>
                                    <th>Produk</th>
                                    <th style="width: 125px;">Harga</th>
                                    <th style="width: 110px; text-align: center;">Beli / Retur</th>
                                    <th style="width: 75px; text-align: center;">Sisa</th>
                                    <th style="width: 95px; text-align: center;">Jml Retur</th>
                                    <th style="width: 145px;">Kondisi</th>
                                    <th style="width: 210px;">Alasan</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($details as $index => $detail)
                                    <tr class="{{ $detail->sisa_retur <= 0 ? 'row-disabled' : '' }}">
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            <strong>{{ $detail->product->nama ?? 'Produk tidak ditemukan' }}</strong>
                                            @if($detail->product && $detail->product->sku)
                                                <div style="font-size: 13px; color: #6b7280; margin-top: 2px;">
                                                    SKU: {{ $detail->product->sku }}
                                                </div>
                                            @endif
                                        </td>

                                        <td style="white-space: nowrap;">
                                            Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                        </td>

                                        <td style="text-align: center; white-space: nowrap;">
                                            {{ $detail->quantity }} / {{ $detail->jumlah_sudah_diretur }}
                                        </td>

                                        <td style="text-align: center;">
                                            <span class="sisa-badge {{ $detail->sisa_retur > 0 ? 'sisa-available' : 'sisa-empty' }}">
                                                {{ $detail->sisa_retur }}
                                            </span>
                                        </td>

                                        <td style="text-align: center;">
                                            <input
                                                type="number"
                                                class="form-control table-input qty-return-input"
                                                name="items[{{ $detail->id }}][quantity]"
                                                min="0"
                                                max="{{ $detail->sisa_retur }}"
                                                step="1"
                                                value="{{ old('items.' . $detail->id . '.quantity', 0) }}"
                                                @disabled($detail->sisa_retur <= 0)
                                                title="Maksimal retur: {{ $detail->sisa_retur }}"
                                                aria-label="Jumlah retur {{ $detail->product->nama ?? 'produk' }}"
                                            >
                                        </td>

                                        <td>
                                            <select
                                                class="form-control table-input"
                                                name="items[{{ $detail->id }}][kondisi]"
                                                @disabled($detail->sisa_retur <= 0)
                                            >
                                                <option value="">Pilih</option>
                                                <option value="layak" @selected(old('items.' . $detail->id . '.kondisi') === 'layak')>
                                                    Layak jual
                                                </option>
                                                <option value="rusak" @selected(old('items.' . $detail->id . '.kondisi') === 'rusak')>
                                                    Rusak
                                                </option>
                                            </select>
                                        </td>

                                        <td>
                                            <input
                                                type="text"
                                                class="form-control table-input"
                                                name="items[{{ $detail->id }}][alasan]"
                                                maxlength="255"
                                                placeholder="{{ $detail->sisa_retur > 0 ? 'Alasan retur...' : 'Sudah diretur' }}"
                                                value="{{ old('items.' . $detail->id . '.alasan') }}"
                                                @disabled($detail->sisa_retur <= 0)
                                            >
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
            </div>

            {{-- =========================================================
                 KETERANGAN & TOMBOL AKSI
            ========================================================== --}}
            <div class="form-card">
                <div class="section-title mb-4">
                    Catatan & Konfirmasi
                </div>

                <div class="form-group">
                    <label for="keterangan" class="form-label">
                        Keterangan Retur (Opsional)
                    </label>

                    <textarea
                        name="keterangan"
                        id="keterangan"
                        class="form-control"
                        rows="3"
                        maxlength="1000"
                        placeholder="Contoh: Barang ditukar atau dikembalikan karena tidak sesuai ukuran."
                    >{{ old('keterangan') }}</textarea>
                </div>

                <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-danger"
                        onclick="return confirm('Simpan retur untuk invoice {{ $transaction->nomor_invoice }}? Pastikan jumlah dan kondisi barang sudah benar.')"
                    >
                        Simpan Retur
                    </button>
                </div>
            </div>
        </form>
    @else
        {{-- FALLBACK JIKA DIAKSES TANPA MEMILIH INVOICE DARI DAFTAR TRANSAKSI --}}
        <div class="form-card" style="text-align: center; padding: 48px 24px;">
            <p style="font-size: 16px; color: #4b5563; margin-bottom: 16px;">
                Data invoice tidak ditemukan. Silakan pilih transaksi terlebih dahulu melalui halaman Daftar Transaksi.
            </p>
            <a href="{{ route('transactions.index') }}" class="btn btn-primary" style="background: #1f2937; border-color: #1f2937;">
                Ke Daftar Transaksi
            </a>
        </div>
    @endif

</div>

@push('styles')
<style>
.sales-return-page .mb-20 {
    margin-bottom: 20px;
}

/* =========================================================
   FORM CARD & INVOICE INFO GRID
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

.invoice-info-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 6px;
}

.info-value-box {
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 11px 14px;
    font-size: 15px;
    color: #1f2937;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 15px;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 11px 14px;
    font-size: 15px;
    color: #1f2937;
    background-color: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.form-control:focus {
    border-color: #1f2937;
    outline: none;
    box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
}

.form-control:disabled {
    background-color: #f3f4f6;
    color: #9ca3af;
    cursor: not-allowed;
}

/* =========================================================
   TABLE & FIT-TO-SCREEN LAYOUT (NO HORIZONTAL SCROLL)
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

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    padding: 13px 10px;
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    white-space: nowrap;
}

.data-table td {
    padding: 13px 10px;
    font-size: 15px;
    color: #1f2937;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle !important;
}

.row-disabled {
    background-color: #fafafa;
    color: #9ca3af;
}

/* Seragamkan font 15px & tinggi semua input dalam sel tabel */
.table-input {
    height: 42px;
    padding: 8px 10px;
    font-size: 15px;
    margin: 0;
}

.qty-return-input {
    width: 80px;
    text-align: center;
    font-weight: 600;
    margin: 0 auto;
    display: block;
}

.sisa-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 700;
}

.sisa-available {
    background-color: #d1fae5;
    color: #065f46;
}

.sisa-empty {
    background-color: #f3f4f6;
    color: #6b7280;
}

.empty-data {
    text-align: center;
    padding: 40px 20px !important;
    font-size: 15px;
    color: #6b7280;
}

@media (max-width: 992px) {
    .invoice-info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .invoice-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-select angka jumlah retur saat box diklik/fokus agar kasir bisa langsung ketik
    document.querySelectorAll('.qty-return-input').forEach(function (input) {
        input.addEventListener('focus', function () {
            this.select();
        });
        input.addEventListener('mouseup', function (e) {
            e.preventDefault();
        });
    });
});
</script>
@endpush
@endsection