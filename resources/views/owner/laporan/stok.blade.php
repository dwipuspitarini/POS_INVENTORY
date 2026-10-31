@extends('layouts.app')

@section('content')

<div class="container">

    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1>Laporan Stok</h1>
            <p>Laporan kondisi stok produk saat ini</p>
        </div>
    </div>


    {{-- Filter --}}
    <div class="form-card mb-20">

        <form method="GET" action="{{ route('owner.laporan.stok') }}">

            <div class="stock-filter-grid">

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="status" class="form-label">
                        Filter Status Stok
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-control"
                    >
                        <option value="semua"
                            {{ $status === 'semua' ? 'selected' : '' }}>
                            Semua Status
                        </option>

                        <option value="tersedia"
                            {{ $status === 'tersedia' ? 'selected' : '' }}>
                            Stok Tersedia
                        </option>

                        <option value="rendah"
                            {{ $status === 'rendah' ? 'selected' : '' }}>
                            Stok Rendah
                        </option>

                        <option value="habis"
                            {{ $status === 'habis' ? 'selected' : '' }}>
                            Stok Habis
                        </option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        Terapkan</button>
                    <a href="{{ route('owner.laporan.stok') }}" class="btn btn-secondary">
                        Reset</a>
                    <a href="{{ route('owner.laporan.stok.pdf', ['status' => $status]) }}" class="btn btn-danger">
                        Cetak PDF
                    </a>
                </div>
            </div>
        </form>
    </div>


    {{-- Summary --}}
    <div class="cards mb-20">

        <div class="card">
            <div class="card-title">
                Total Produk
            </div>

            <div class="card-value">
                {{ $totalProduk }}
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                Total Stok
            </div>

            <div class="card-value">
                {{ $totalStok }}
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                Produk Stok Rendah
            </div>

            <div class="card-value">
                {{ $produkStokRendah }}
            </div>
        </div>


        <div class="card">
            <div class="card-title">
                Produk Habis
            </div>

            <div class="card-value">
                {{ $produkHabis }}
            </div>
        </div>

    </div>


    {{-- Judul filter aktif --}}
    <div class="table-card">

        <div class="table-card-header">

            Daftar Stok Produk

            @if($status === 'tersedia')
                <span class="filter-label">
                    — Stok Tersedia
                </span>
            @elseif($status === 'rendah')
                <span class="filter-label">
                    — Stok Rendah
                </span>
            @elseif($status === 'habis')
                <span class="filter-label">
                    — Stok Habis
                </span>
            @endif

        </div>


        <div class="table-wrapper">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($products as $index => $product)

                        <tr>

                            <td>
                                {{ method_exists($products, 'firstItem') ? $products->firstItem() + $index : $index + 1 }}
                            </td>

                            <td>
                                <strong>
                                    {{ $product->nama }}
                                </strong>
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

                            <td>
                                {{ $product->stok }}
                            </td>

                            <td>

                                @if($product->stok <= 0)

                                    <span class="stock-status stock-empty">
                                        Stok Habis
                                    </span>

                                @elseif($product->stok <= 5)

                                    <span class="stock-status stock-low">
                                        Stok Rendah
                                    </span>

                                @else

                                    <span class="stock-status stock-available">
                                        Tersedia
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty-data">
                                Tidak ada produk dengan status stok tersebut.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- BAGIAN PAGINATION DENGAN KETERANGAN --}}
        @if(method_exists($products, 'hasPages') && $products->hasPages())
            <div class="report-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $products->firstItem() }}</strong> - <strong>{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
                </div>
                <div class="report-pagination">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif

    </div>

</div>


@push('styles')

<style>

    .mb-20 {
        margin-bottom: 20px;
    }

    .stock-filter-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 20px;
        align-items: end;
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
    }

    .filter-label {
        font-size: 14px;
        font-weight: normal;
        color: #6b7280;
    }

    .stock-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 13px;
        font-weight: bold;
    }

    .stock-empty {
        background: #fee2e2;
        color: #991b1b;
    }

    .stock-low {
        background: #fef3c7;
        color: #92400e;
    }

    .stock-available {
        background: #d1fae5;
        color: #065f46;
    }

    /* =========================
       STYLE PAGINATION LAPORAN
    ========================= */

    .report-pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        flex-wrap: wrap;
        gap: 12px;
        border-top: 1px solid #e5e7eb;
    }

    .pagination-info {
        font-size: 14px;
        color: #6b7280;
    }

    .report-pagination .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #d1d5db;
    }

    .report-pagination .page-item {
        margin: 0;
    }

    .report-pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        background-color: #ffffff;
        border: none;
        border-right: 1px solid #d1d5db;
        text-decoration: none;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .report-pagination .page-item:last-child .page-link {
        border-right: none;
    }

    .report-pagination .page-link:hover {
        background-color: #f3f4f6;
        color: #1f2937;
    }

    .report-pagination .page-item.active .page-link {
        background-color: #1f2937;
        color: #ffffff;
        border-color: #1f2937;
    }

    .report-pagination .page-item.disabled .page-link {
        color: #9ca3af;
        background-color: #f9fafb;
        cursor: not-allowed;
    }

    @media (max-width: 700px) {

        .stock-filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            margin-bottom: 0;
        }

        .report-pagination-wrapper {
            flex-direction: column;
            align-items: center;
        }

    }

</style>

@endpush

@endsection