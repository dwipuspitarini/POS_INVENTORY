<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Laporan Pendapatan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .periode {
            text-align: center;
            margin-bottom: 20px;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary td {
            width: 25%;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .summary-title {
            font-size: 10px;
            color: #666;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #eeeeee;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 7px;
        }

        th {
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>LAPORAN PENDAPATAN</h1>
        <p>TOKO OPTIK</p>
    </div>

    <div class="periode">
        <strong>Periode:</strong>

        @if(request('tanggal_mulai') && request('tanggal_akhir'))

            {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d/m/Y') }}
            -
            {{ \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d/m/Y') }}

        @elseif(request('tanggal_mulai'))

            Mulai
            {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d/m/Y') }}

        @elseif(request('tanggal_akhir'))

            Sampai
            {{ \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d/m/Y') }}

        @else

            Semua Periode

        @endif
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="summary-title">
                    Total Transaksi
                </div>

                <div class="summary-value">
                    {{ $totalTransaksi }}
                </div>
            </td>

            <td>
                <div class="summary-title">
                    Total Nilai Penjualan
                </div>

                <div class="summary-value">
                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-title">
                    Pembayaran Diterima
                </div>

                <div class="summary-value">
                    Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-title">
                    Sisa Pembayaran
                </div>

                <div class="summary-value">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Dibayar</th>
                <th>Sisa</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>

            @forelse($transactions as $transaction)

                @php
                    $sisa = max(
                        $transaction->total_akhir -
                        $transaction->jumlah_bayar,
                        0
                    );
                @endphp

                <tr>
                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $transaction->nomor_invoice }}
                    </td>

                    <td>
                        {{ $transaction->customer->nama ?? 'Pelanggan Umum' }}
                    </td>

                    <td class="text-center">
                        {{ $transaction->tanggal_transaksi->format('d/m/Y H:i') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($transaction->total_akhir, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($transaction->jumlah_bayar, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($sisa, 0, ',', '.') }}
                    </td>

                    <td class="text-center">
                        {{ strtoupper($transaction->status_pembayaran) }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="8" class="text-center">
                        Tidak ada data transaksi pada periode tersebut.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

    <div class="footer">
        Dicetak pada:
        {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>