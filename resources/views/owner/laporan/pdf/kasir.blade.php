<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>Laporan Kasir</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin: 0 0 5px;
            font-size: 18px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }

        .info {
            margin-bottom: 15px;
            line-height: 1.7;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .summary td {
            border: 1px solid #ccc;
            padding: 8px;
            width: 25%;
            vertical-align: top;
        }

        .summary .label {
            color: #555;
            font-size: 9px;
        }

        .summary .value {
            font-size: 13px;
            font-weight: bold;
            margin-top: 5px;
        }

        h3 {
            font-size: 12px;
            margin: 16px 0 8px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.data th,
        table.data td {
            border: 1px solid #bbb;
            padding: 6px;
        }

        table.data th {
            background: #eeeeee;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background: #f5f5f5;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>

<body>

    <h2>LAPORAN KASIR</h2>

    <div class="subtitle">
        Laporan Penjualan dan Pembayaran Kasir
    </div>

    <div class="info">
        <strong>Periode:</strong>
        {{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }}
        s/d
        {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d-m-Y') }}

        <br>

        <strong>Kasir:</strong>
        {{ $namaKasir }}

        <br>

        <strong>Dicetak pada:</strong>
        {{ now()->format('d-m-Y H:i:s') }}
    </div>

    {{-- RINGKASAN PENJUALAN --}}
    <table class="summary">
        <tr>
            <td>
                <div class="label">Jumlah Transaksi</div>
                <div class="value">
                    {{ $jumlahTransaksi }}
                </div>
                <div>Invoice asli</div>
            </td>

            <td>
                <div class="label">Penjualan Kotor</div>
                <div class="value">
                    Rp {{ number_format($totalPenjualanKotor, 0, ',', '.') }}
                </div>
                <div>Sebelum diskon dan retur</div>
            </td>

            <td>
                <div class="label">Total Diskon</div>
                <div class="value">
                    Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="label">Total Retur ({{ $jumlahRetur }} retur)</div>
                <div class="value">
                    Rp {{ number_format($totalRetur, 0, ',', '.') }}
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="4">
                <div class="label">Total Penjualan Bersih</div>
                <div class="value">
                    Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
                </div>
                <div>Penjualan setelah dikurangi diskon dan retur</div>
            </td>
        </tr>
    </table>

    {{-- RINGKASAN PEMBAYARAN --}}
    <h3>RINGKASAN PEMBAYARAN</h3>

    <table class="data">
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th>Jumlah Transaksi</th>
                <th>Total Pembayaran</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($ringkasanMetode as $metode)
                <tr>
                    <td>{{ $metode['nama'] }}</td>
                    <td class="text-center">
                        {{ $metode['jumlah_transaksi'] }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($metode['total'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- DETAIL TRANSAKSI --}}
    <h3>DETAIL TRANSAKSI PENJUALAN</h3>

    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="17%">Tanggal</th>
                <th width="20%">Nomor Invoice</th>
                <th width="15%">Kasir</th>
                <th width="12%">Penjualan Kotor</th>
                <th width="10%">Diskon</th>
                <th width="12%">Total Akhir</th>
                <th width="10%">Jumlah Dibayar</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($transaksi as $index => $item)
                <tr>
                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d-m-Y H:i') }}
                    </td>

                    <td>{{ $item->nomor_invoice }}</td>

                    <td>{{ $item->user->name ?? '-' }}</td>

                    <td class="text-right">
                        {{ number_format($item->total, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($item->diskon, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($item->total_akhir, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- DETAIL RETUR --}}
    <h3>DETAIL RETUR PENJUALAN</h3>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Tanggal Retur</th>
                <th width="25%">Nomor Retur</th>
                <th width="30%">Invoice Asli</th>
                <th width="20%">Jumlah Pengembalian</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($retur as $index => $item)
                <tr>
                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($item->tanggal_retur)->format('d-m-Y H:i') }}
                    </td>

                    <td>{{ $item->nomor_retur }}</td>

                    <td>
                        {{ $item->transaction->nomor_invoice ?? '-' }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->jumlah_pengembalian, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        Tidak ada retur pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dicetak secara otomatis dari Sistem Informasi POS Toko Bangunan.
    </div>

</body>
</html>