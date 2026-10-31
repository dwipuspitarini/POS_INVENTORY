<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <title>Laporan Stok</title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 15px;
        }

        .filter-info {
            margin-bottom: 15px;
            padding: 8px 10px;
            background: #f3f4f6;
            border: 1px solid #ddd;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #ddd;
            padding: 10px;
        }

        .summary-title {
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 17px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 7px;
        }

        .text-center {
            text-align: center;
        }

        .status-empty {
            color: #991b1b;
            font-weight: bold;
        }

        .status-low {
            color: #92400e;
            font-weight: bold;
        }

        .status-available {
            color: #065f46;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            color: #666;
            font-size: 10px;
        }

    </style>

</head>

<body>

    <h1>Laporan Stok</h1>

    <div class="subtitle">
        Laporan kondisi stok produk saat ini
    </div>


    {{-- Filter --}}
    <div class="filter-info">

        <strong>Filter:</strong>

        @if($status === 'tersedia')
            Stok Tersedia

        @elseif($status === 'rendah')
            Stok Rendah

        @elseif($status === 'habis')
            Stok Habis

        @else
            Semua Status
        @endif

    </div>


    {{-- Summary --}}
    <table class="summary">

        <tr>

            <td>
                <div class="summary-title">
                    Total Produk
                </div>

                <div class="summary-value">
                    {{ $totalProduk }}
                </div>
            </td>


            <td>
                <div class="summary-title">
                    Total Stok
                </div>

                <div class="summary-value">
                    {{ $totalStok }}
                </div>
            </td>


            <td>
                <div class="summary-title">
                    Produk Stok Rendah
                </div>

                <div class="summary-value">
                    {{ $produkStokRendah }}
                </div>
            </td>


            <td>
                <div class="summary-title">
                    Produk Habis
                </div>

                <div class="summary-value">
                    {{ $produkHabis }}
                </div>
            </td>

        </tr>

    </table>


    {{-- Tabel Produk --}}
    <table>

        <thead>

            <tr>

                <th width="5%">
                    No
                </th>

                <th width="25%">
                    Produk
                </th>

                <th width="15%">
                    Kategori
                </th>

                <th width="15%">
                    Supplier
                </th>

                <th width="15%">
                    Harga Jual
                </th>

                <th width="10%">
                    Stok
                </th>

                <th width="15%">
                    Status
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($products as $product)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $product->nama }}
                    </td>

                    <td>
                        {{ $product->category->nama ?? '-' }}
                    </td>

                    <td>
                        {{ optional($product->stockMovements->first()?->supplier)->nama ?? '-' }}
                    </td>

                    <td>
                        Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                    </td>

                    <td class="text-center">
                        {{ $product->stok }}
                    </td>

                    <td>

                        @if($product->stok <= 0)

                            <span class="status-empty">
                                Stok Habis
                            </span>

                        @elseif($product->stok <= 5)

                            <span class="status-low">
                                Stok Rendah
                            </span>

                        @else

                            <span class="status-available">
                                Tersedia
                            </span>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center">
                        Tidak ada produk dengan status stok tersebut.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">

        Dicetak pada:
        {{ now()->format('d-m-Y H:i') }}

    </div>

</body>
</html>