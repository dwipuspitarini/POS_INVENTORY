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

                <strong>
                    {{ $transaction->nomor_invoice }}
                </strong>
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
                    Rp {{ number_format(
                        $transaction->total_akhir,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </div>

            <div class="summary-row">
                <span>Sudah Dibayar</span>

                <strong>
                    Rp {{ number_format(
                        $transaction->jumlah_bayar,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </div>

            <div class="summary-row total-row">
                <span>Sisa Pembayaran</span>

                <strong>
                    Rp {{ number_format(
                        $sisaPembayaran,
                        0,
                        ',',
                        '.'
                    ) }}
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

                    <strong>
                        Terdapat kesalahan:
                    </strong>

                    <ul style="margin:8px 0 0 18px;">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                action="{{ route(
                    'transactions.pelunasan',
                    $transaction
                ) }}"
                method="POST"
            >

                @csrf


                <div class="form-group">

                    <label
                        for="metode_pembayaran"
                        class="form-label"
                    >
                        Metode Pembayaran
                    </label>

                    <select
                        name="metode_pembayaran"
                        id="metode_pembayaran"
                        class="form-control"
                        required
                    >

                        <option value="">
                            -- Pilih Metode Pembayaran --
                        </option>

                        <option value="tunai"
                            {{ old('metode_pembayaran') === 'tunai'
                                ? 'selected'
                                : '' }}>
                            Tunai
                        </option>

                        <option value="qris"
                            {{ old('metode_pembayaran') === 'qris'
                                ? 'selected'
                                : '' }}>
                            QRIS
                        </option>

                        <option value="mesin"
                            {{ old('metode_pembayaran') === 'mesin'
                                ? 'selected'
                                : '' }}>
                            Mesin
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label
                        for="jumlah_pelunasan"
                        class="form-label"
                    >
                        Jumlah Pelunasan
                    </label>

                    <input
                        type="number"
                        name="jumlah_pelunasan"
                        id="jumlah_pelunasan"
                        class="form-control"
                        value="{{ old(
                            'jumlah_pelunasan',
                            $sisaPembayaran
                        ) }}"
                        min="{{ $sisaPembayaran }}"
                        max="{{ $sisaPembayaran }}"
                        required
                    >

                    <small class="text-muted">
                        Jumlah pelunasan harus
                        Rp {{ number_format(
                            $sisaPembayaran,
                            0,
                            ',',
                            '.'
                        ) }}
                    </small>

                </div>


                <div class="form-actions">

                    <a
                        href="{{ route(
                            'transactions.show',
                            $transaction
                        ) }}"
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