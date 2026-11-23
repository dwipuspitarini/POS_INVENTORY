
@extends('layouts.app')

@section('title', 'Pelunasan Transaksi')
@section('page-title', 'Pelunasan Transaksi')

@section('content')

<div class="container-fluid">

    <div class="page-header">
        <div>
            <h1>Pelunasan Transaksi</h1>
            <p>Proses pelunasan pembayaran pelanggan</p>
        </div>

        <a href="{{ route('transactions.show', $transaction) }}"
           class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <div class="transaction-bottom-grid">

        {{-- INFORMASI TRANSAKSI --}}
        <div class="form-card">

            <div class="section-title">
                Informasi Transaksi
            </div>

            <div class="summary-row">
                <span>No. Invoice</span>
                <strong>{{ $transaction->nomor_invoice }}</strong>
            </div>

            <div class="summary-row">
                <span>Pelanggan</span>
                <strong>
                    {{ $transaction->customer->nama ?? 'Pelanggan Umum' }}
                </strong>
            </div>

            <div class="summary-row">
                <span>Total Transaksi</span>
                <strong>
                    Rp {{ number_format($transaction->total_akhir, 0, ',', '.') }}
                </strong>
            </div>

            <div class="summary-row">
                <span>DP Awal</span>
                <strong>
                    Rp {{ number_format($transaction->jumlah_dp, 0, ',', '.') }}
                </strong>
            </div>

            <div class="summary-row">
                <span>Sudah Dibayar</span>
                <strong>
                    Rp {{ number_format($transaction->jumlah_bayar, 0, ',', '.') }}
                </strong>
            </div>

            <div class="summary-row">
                <span>Metode Pembayaran</span>
                <strong>
                    @if($transaction->metode_pembayaran === 'tunai')
                        Tunai
                    @elseif($transaction->metode_pembayaran === 'qris')
                        QRIS
                    @elseif($transaction->metode_pembayaran === 'edc')
                        EDC
                    @else
                        Tidak diketahui
                    @endif
                </strong>
            </div>

            <div class="summary-row total-row">
                <span>Sisa Pembayaran</span>
                <strong>
                    Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}
                </strong>
            </div>

        </div>


        {{-- FORM PELUNASAN --}}
        <div class="form-card">

            <div class="section-title">
                Pembayaran Pelunasan
            </div>

            @if($errors->any())
                <div class="form-error">
                    <strong>Terdapat kesalahan:</strong>

                    <ul style="margin:8px 0 0 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('transactions.pelunasan', $transaction) }}"
                method="POST"
                id="pelunasan-form"
            >

                @csrf

                {{-- JUMLAH PELUNASAN --}}
                <div class="form-group">

                    <label for="jumlah_pelunasan" class="form-label">
                        Jumlah Pelunasan
                    </label>

                    <input
                        type="number"
                        name="jumlah_pelunasan"
                        id="jumlah_pelunasan"
                        class="form-control"
                        value="{{ old('jumlah_pelunasan', $sisaPembayaran) }}"
                        min="{{ $sisaPembayaran }}"
                        max="{{ $sisaPembayaran }}"
                        step="0.01"
                        readonly
                        required
                    >

                    <small class="text-muted">
                        Nominal pelunasan sesuai dengan sisa tagihan.
                    </small>

                </div>


                {{-- KHUSUS PEMBAYARAN TUNAI --}}
                @if($transaction->metode_pembayaran === 'tunai')

                    <div class="form-group">

                        <label for="uang_diterima" class="form-label">
                            Uang Tunai Diterima (Rp)
                        </label>

                        <input
                            type="number"
                            name="uang_diterima"
                            id="uang_diterima"
                            class="form-control auto-select-number"
                            value="{{ old('uang_diterima', $sisaPembayaran) }}"
                            min="{{ $sisaPembayaran }}"
                            step="1"
                            required
                        >

                        <small class="text-muted">
                            Masukkan uang yang diberikan pelanggan.
                        </small>

                    </div>

                    <div class="payment-info-box">

                        <div class="summary-row">
                            <span>Sisa Tagihan</span>
                            <strong>
                                Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="summary-row total-row">
                            <span>Kembalian</span>
                            <strong id="display-kembalian">
                                Rp 0
                            </strong>
                        </div>

                    </div>

                @else

                    <div class="payment-info-box">

                        <p style="margin:0;">
                            Pembayaran menggunakan
                            <strong>
                                {{ strtoupper($transaction->metode_pembayaran) }}
                            </strong>.
                        </p>

                        <p class="text-muted" style="margin:8px 0 0;">
                            Pastikan pelanggan telah membayar sesuai
                            dengan sisa tagihan.
                        </p>

                    </div>

                @endif


                <div class="form-actions" style="margin-top:20px;">

                    <a
                        href="{{ route('transactions.show', $transaction) }}"
                        class="btn btn-secondary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Proses Pelunasan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection


@if($transaction->metode_pembayaran === 'tunai')

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const jumlahPelunasan = document.getElementById('jumlah_pelunasan');
        const uangDiterima = document.getElementById('uang_diterima');
        const displayKembalian = document.getElementById('display-kembalian');
        const form = document.getElementById('pelunasan-form');

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number || 0);
        }

        function updateKembalian() {

            const jumlah = parseFloat(jumlahPelunasan.value) || 0;
            const diterima = parseFloat(uangDiterima.value) || 0;

            const kembalian = Math.max(diterima - jumlah, 0);

            displayKembalian.textContent = formatRupiah(kembalian);
        }

        uangDiterima.addEventListener('input', updateKembalian);

        form.addEventListener('submit', function (event) {

            const jumlah = parseFloat(jumlahPelunasan.value) || 0;
            const diterima = parseFloat(uangDiterima.value) || 0;

            if (diterima < jumlah) {
                event.preventDefault();

                alert('Uang diterima tidak boleh kurang dari sisa pembayaran.');

                uangDiterima.focus();
            }
        });

        updateKembalian();

    });
    </script>
    @endpush

@endif
