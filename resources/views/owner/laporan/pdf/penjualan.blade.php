
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>

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
        }

        .summary td {
            width: 20%;
            padding: 9px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .summary-title {
            font-size: 9px;
            color: #666;
        }

        .summary-value {
            font-size: 12px;
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
        <h1>LAPORAN PENJUALAN</h1>
        <p>TOKO BANGUNAN</p>
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
                <div class="summary-title">Total Retur</div>
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

    {{-- DETAIL PENJUALAN --}}
    <div class="section-title">Detail Penjualan</div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Tanggal Transaksi</th>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @forelse($details as $detail)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>

                    <td>
                        {{ $detail->transaction->nomor_invoice ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ $detail->transaction && $detail->transaction->tanggal_transaksi
                            ? \Carbon\Carbon::parse($detail->transaction->tanggal_transaksi)->format('d/m/Y H:i')
                            : '-' }}
                    </td>

                    <td>{{ $detail->product->nama ?? '-' }}</td>

                    <td>{{ $detail->product->category->nama ?? '-' }}</td>

                    <td class="text-right">
                        Rp {{ number_format($detail->harga, 0, ',', '.') }}
                    </td>

                    <td class="text-center">{{ $detail->quantity }}</td>

                    <td class="text-right">
                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        Tidak ada data penjualan pada periode tersebut.
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
                <th>Tanggal Retur</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
                <th>Alasan</th>
                <th>Subtotal Retur</th>
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
