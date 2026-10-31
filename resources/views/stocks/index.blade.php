@extends('layouts.app')

@section('title', 'Stok Produk')
@section('page-title', 'Stok Produk')

@section('content')

<div class="container-fluid">

    <div class="page-header">

        <div>
            <h1>Stok</h1>

            <p>
                Melihat dan mengelola stok produk.
            </p>
        </div>

        @auth
            @if(auth()->user()->role === 'owner')
                <a href="{{ route('owner.stocks.create') }}" class="btn btn-primary">
                    + Tambah Stok
                </a>
            @endif
        @endauth

    </div>

    <form method="GET"
          action="{{ route('stocks.index') }}"
          style="display: flex; gap: 10px; margin-bottom: 20px;">

        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Cari nama produk..."
            value="{{ request('search') }}"
            style="flex: 1;"
        >

        <button type="submit" class="btn btn-primary">
            Cari
        </button>

    </form>

    <div class="table-card">

        <div class="table-wrapper">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($products as $product)

                        <tr>

                            <td>
                                {{ $products->firstItem() + $loop->index }}
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
                                Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                            </td>

                            <td>

                                @if($product->stok <= 0)

                                    <span class="stock-badge stock-empty">
                                        Habis
                                    </span>

                                @elseif($product->stok <= 5)

                                    <span class="stock-badge stock-low">
                                        {{ $product->stok }}
                                    </span>

                                @else

                                    <span class="stock-badge stock-available">
                                        {{ $product->stok }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="empty-data">
                                Belum ada data produk.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($products->count())
            <div class="stock-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $products->firstItem() }}</strong> - <strong>{{ $products->lastItem() }}</strong> dari <strong>{{ $products->total() }}</strong> produk
                </div>
                <div class="stock-pagination">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif

    </div>

</div>

@push('styles')
<style>

.stock-badge {
    display: inline-block;
    min-width: 60px;
    text-align: center;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}

.stock-available {
    background: #d1fae5;
    color: #065f46;
}

.stock-low {
    background: #fef3c7;
    color: #92400e;
}

.stock-empty {
    background: #fee2e2;
    color: #991b1b;
}

/* =========================
   PAGINATION STOK MODERN
========================= */

.stock-pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 10px 10px 10px;
    flex-wrap: wrap;
    gap: 12px;
}

.pagination-info {
    font-size: 14px;
    color: #6b7280;
}

.stock-pagination .pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #d1d5db;
}

.stock-pagination .page-item {
    margin: 0;
}

.stock-pagination .page-link {
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

.stock-pagination .page-item:last-child .page-link {
    border-right: none;
}

/* Hover State */
.stock-pagination .page-link:hover {
    background-color: #f3f4f6;
    color: #1f2937;
}

/* Active State */
.stock-pagination .page-item.active .page-link {
    background-color: #1f2937;
    color: #ffffff;
    border-color: #1f2937;
}

/* Disabled State */
.stock-pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background-color: #f9fafb;
    cursor: not-allowed;
}

/* Penyesuaian Responsif untuk Layar Kecil */
@media (max-width: 640px) {
    .stock-pagination-wrapper {
        flex-direction: column;
        align-items: center;
    }
}
</style>
@endpush

@endsection