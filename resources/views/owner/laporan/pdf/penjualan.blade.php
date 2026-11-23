
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan & Pendapatan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
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
            margin-bottom: 18px;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .summary td {
            width: 16.66%;
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .summary-title {
            font-size: 8px;
            color: #666;
        }

        .summary-value {
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th {
            background: #eee;
            font-weight: bold;
            text-align: center;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 18px 0 8px;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>LAPORAN PENJUALAN & PENDAPATAN</h1>
        <p>TOKO BANGUN REJEKI</p>
    </div>

    <div class="periode">
        <strong>Periode transaksi:</strong>

        @if(request('tanggal_mulai') && request('tanggal_akhir'))
            {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d/m/Y') }}
            -
            {{ \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d/m/Y') }}
        @elseif(request('tanggal_mulai'))
            Mulai {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d/m/Y') }}
        @elseif(request('tanggal_akhir'))
            Sampai {{ \Carbon\Carbon::parse(request('tanggal_akhir'))->format('d/m/Y') }}
        @else
            Semua Periode
        @endif

        @if(request('search'))
            <br>
            <strong>Pencarian:</strong> {{ request('search') }}
        @endif
    </div>

    {{-- RINGKASAN --}}
    <table class="summary">
        <tr>
            <td>
                <div class="summary-title">Total Transaksi</div>
                <div class="summary-value">
                    {{ number_format($totalTransaksi, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-title">Produk Terjual</div>
                <div class="summary-value">
                    {{ number_format($totalProdukTerjual, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-title">Penjualan Kotor</div>
                <div class="summary-value">
                    Rp {{ number_format($totalPenjualanKotor, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-title">Total Diskon</div>
                <div class="summary-value">
                    Rp {{ number_format($totalDiskon, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-title">Total Retur Bersih</div>
                <div class="summary-value">
                    Rp {{ number_format($totalRetur, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="summary-title">Penjualan Bersih</div>
                <div class="summary-value">
                    Rp {{ number_format($totalPenjualanBersih, 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    
{{-- TABEL GABUNGAN PENJUALAN DAN PENDAPATAN --}}
<h3 style="margin-top: 20px; margin-bottom: 10px;">
    DAFTAR TRANSAKSI PENJUALAN
</h3>

<table width="100%" cellspacing="0" cellpadding="6"
       style="border-collapse: collapse; font-size: 9px;">
    <thead>
        <tr style="background-color: #e5e7eb;">
            <th style="border: 1px solid #333;">No</th>
            <th style="border: 1px solid #333;">Invoice</th>
            <th style="border: 1px solid #333;">Kasir</th>
            <th style="border: 1px solid #333;">Pelanggan</th>
            <th style="border: 1px solid #333;">Tanggal Transaksi</th>
            <th style="border: 1px solid #333;">Total</th>
            <th style="border: 1px solid #333;">Dibayar</th>
            <th style="border: 1px solid #333;">Sisa</th>
            <th style="border: 1px solid #333;">Status</th>
        </tr>
    </thead>

    <tbody>
        @forelse($transactions as $transaction)
            @php
                $total = (float) $transaction->total_akhir;
                $dibayar = (float) $transaction->jumlah_bayar;
                $sisa = max($total - $dibayar, 0);
            @endphp

            <tr>
                <td style="border: 1px solid #333; text-align: center;">
                    {{ $loop->iteration }}
                </td>

                <td style="border: 1px solid #333;">
                    {{ $transaction->nomor_invoice }}
                </td>

                <td style="border: 1px solid #333;">
                    {{ $transaction->user->name ?? '-' }}
                </td>

                <td style="border: 1px solid #333;">
                    {{ $transaction->customer->nama ?? 'Pelanggan Umum' }}
                </td>

                <td style="border: 1px solid #333; text-align: center;">
                    {{ $transaction->tanggal_transaksi
                        ? \Carbon\Carbon::parse($transaction->tanggal_transaksi)->format('d/m/Y H:i')
                        : '-' }}
                </td>

                <td style="border: 1px solid #333; text-align: right;">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </td>

                <td style="border: 1px solid #333; text-align: right;">
                    Rp {{ number_format($dibayar, 0, ',', '.') }}
                </td>

                <td style="border: 1px solid #333; text-align: right;">
                    Rp {{ number_format($sisa, 0, ',', '.') }}
                </td>

                <td style="border: 1px solid #333; text-align: center;">
                    {{ ucfirst($transaction->status_pembayaran ?? '-') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9"
                    style="border: 1px solid #333; text-align: center;">
                    Tidak ada transaksi yang sesuai dengan filter.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>


    {{-- DETAIL RETUR --}}
    <div class="section-title">Detail Retur Penjualan</div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>No. Retur</th>
                <th>Invoice Asal</th>
                <th>Kasir</th>
                <th>Tanggal Retur</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
                <th>Alasan</th>
                <th>Subtotal Retur Sebelum Diskon</th>
            </tr>
        </thead>

        <tbody>
            @forelse($returns as $retur)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td>
                        {{ $retur->salesReturn->nomor_retur ?? '-' }}
                    </td>

                    <td>
                        {{ $retur->salesReturn->transaction->nomor_invoice ?? '-' }}
                    </td>

                    <td>
                        {{ $retur->salesReturn->transaction->user->name ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ $retur->salesReturn && $retur->salesReturn->tanggal_retur
                            ? \Carbon\Carbon::parse($retur->salesReturn->tanggal_retur)->format('d/m/Y H:i')
                            : '-' }}
                    </td>

                    <td>{{ $retur->product->nama ?? '-' }}</td>

                    <td class="text-center">{{ $retur->quantity }}</td>

                    <td class="text-center">
                        {{ ucfirst($retur->kondisi ?? '-') }}
                    </td>

                    <td>{{ $retur->alasan ?? '-' }}</td>

                    <td class="text-right">
                        Rp {{ number_format($retur->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">
                        Tidak ada retur dari transaksi pada periode tersebut.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
