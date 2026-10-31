@extends('layouts.app')

@section('content')

<div class="container">

    {{-- HEADER --}}
    <div class="page-header">

        <div>

            <h1>
                Riwayat Stok
            </h1>

            <p>
                Melihat seluruh pergerakan stok produk.
            </p>

        </div>

    </div>


    {{-- FILTER --}}
    <div class="form-card mb-20">

        <div class="section-title">
            Filter Riwayat
        </div>


        <form
            method="GET"
            action="{{ route('owner.stocks.history') }}"
        >

            {{-- SEARCH --}}
            <div class="form-group">

                <label
                    for="search"
                    class="form-label"
                >
                    Cari Riwayat
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    class="form-control"
                    value="{{ request('search') }}"
                    placeholder="Cari Riwayat Produk"
                >

            </div>


            {{-- FILTER TANGGAL DAN JENIS --}}
            <div class="history-filter-grid">

                {{-- TANGGAL MULAI --}}
                <div class="form-group">

                    <label
                        for="tanggal_mulai"
                        class="form-label"
                    >
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        class="form-control"
                        value="{{ request('tanggal_mulai') }}"
                    >

                </div>


                {{-- TANGGAL AKHIR --}}
                <div class="form-group">

                    <label
                        for="tanggal_akhir"
                        class="form-label"
                    >
                        Tanggal Akhir
                    </label>

                    <input
                        type="date"
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        class="form-control"
                        value="{{ request('tanggal_akhir') }}"
                    >

                </div>


                {{-- JENIS --}}
                <div class="form-group">

                    <label
                        for="jenis"
                        class="form-label"
                    >
                        Jenis
                    </label>

                    <select
                        id="jenis"
                        name="jenis"
                        class="form-control"
                    >

                        <option
                            value=""
                            {{ request('jenis') == '' ? 'selected' : '' }}
                        >
                            Semua
                        </option>

                        <option
                            value="masuk"
                            {{ request('jenis') == 'masuk' ? 'selected' : '' }}
                        >
                            Stok Masuk
                        </option>

                        <option
                            value="keluar"
                            {{ request('jenis') == 'keluar' ? 'selected' : '' }}
                        >
                            Stok Keluar
                        </option>

                    </select>

                </div>


                {{-- TOMBOL --}}
                <div class="history-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Tampilkan
                    </button>

                    <a
                        href="{{ route('owner.stocks.history') }}"
                        class="btn btn-secondary"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>


    {{-- RINGKASAN --}}
    <div class="cards mb-20">

        <div class="card">

            <div class="card-title">
                Total Riwayat
            </div>

            <div class="card-value">
                {{ method_exists($movements, 'total') ? $movements->total() : $movements->count() }}
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Stok Masuk
            </div>

            <div class="card-value">
                {{ isset($totalMasuk) ? $totalMasuk : $movements->where('jenis', 'masuk')->sum('jumlah') }}
            </div>

        </div>


        <div class="card">

            <div class="card-title">
                Stok Keluar
            </div>

            <div class="card-value">
                {{ isset($totalKeluar) ? $totalKeluar : $movements->where('jenis', 'keluar')->sum('jumlah') }}
            </div>

        </div>

    </div>


    {{-- TABEL --}}
    <div class="table-card">

        <div class="table-card-header">

            <h2>
                Riwayat Pergerakan Stok
            </h2>

        </div>


        <div class="table-wrapper">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Tanggal</th>

                        <th>SKU</th>

                        <th>Produk</th>

                        <th>Kategori</th>

                        <th>Supplier</th>

                        <th>Jenis</th>

                        <th>Jumlah</th>

                        <th>Petugas</th>

                        <th>Keterangan</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($movements as $index => $movement)

                        <tr>

                            {{-- NO --}}
                            <td>
                                {{ method_exists($movements, 'firstItem') ? $movements->firstItem() + $index : $index + 1 }}
                            </td>


                            {{-- TANGGAL --}}
                            <td>
                                {{ $movement->tanggal_stok
                                    ? \Carbon\Carbon::parse($movement->tanggal_stok)->format('d-m-Y H:i')
                                    : '-' }}
                            </td>


                            {{-- SKU --}}
                            <td>
                                {{ $movement->product->sku ?? '-' }}
                            </td>


                            {{-- PRODUK --}}
                            <td>

                                <strong>
                                    {{ $movement->product->nama ?? '-' }}
                                </strong>

                            </td>


                            {{-- KATEGORI --}}
                            <td>
                                {{ $movement->product->category->nama ?? '-' }}
                            </td>


                            {{-- SUPPLIER --}}
                            <td>
                                {{ $movement->supplier->nama ?? '-' }}
                            </td>


                            {{-- JENIS --}}
                            <td>

                                @if($movement->jenis === 'masuk')

                                    <span class="stock-badge stock-in">
                                        Masuk
                                    </span>

                                @else

                                    <span class="stock-badge stock-out">
                                        Keluar
                                    </span>

                                @endif

                            </td>


                            {{-- JUMLAH --}}
                            <td>

                                @if($movement->jenis === 'masuk')

                                    <strong>
                                        +{{ $movement->jumlah }}
                                    </strong>

                                @else

                                    <strong>
                                        -{{ $movement->jumlah }}
                                    </strong>

                                @endif

                            </td>


                            {{-- PETUGAS --}}
                            <td>
                                {{ $movement->user->name ?? '-' }}
                            </td>


                            {{-- KETERANGAN --}}
                            <td>
                                {{ $movement->keterangan ?? '-' }}
                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="10"
                                class="empty-cart"
                            >
                                Belum ada riwayat stok.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- PAGINATION --}}
        @if(method_exists($movements, 'hasPages') && $movements->hasPages())
            <div class="history-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $movements->firstItem() }}</strong> - <strong>{{ $movements->lastItem() }}</strong> dari <strong>{{ $movements->total() }}</strong> riwayat
                </div>
                <div class="history-pagination">
                    {{ $movements->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif

    </div>

</div>


@push('styles')

<style>

.history-filter-grid {

    display: grid;

    grid-template-columns:
        1fr
        1fr
        1fr
        auto;

    gap: 18px;

    align-items: end;

}


.history-filter-actions {

    display: flex;

    gap: 10px;

    margin-bottom: 18px;

    white-space: nowrap;

}


.stock-badge {

    display: inline-block;

    padding: 5px 12px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: 600;

}


.stock-in {

    background: #d1fae5;

    color: #047857;

}


.stock-out {

    background: #fee2e2;

    color: #b91c1c;

}


.page-header-actions {

    display: flex;

    align-items: center;

    gap: 10px;

}

/* =========================
   PAGINATION RIWAYAT STOK
========================= */

.history-pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 12px;
}

.pagination-info {
    font-size: 14px;
    color: #6b7280;
}

.history-pagination .pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #d1d5db;
}

.history-pagination .page-item {
    margin: 0;
}

.history-pagination .page-link {
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

.history-pagination .page-item:last-child .page-link {
    border-right: none;
}

/* Hover State */
.history-pagination .page-link:hover {
    background-color: #f3f4f6;
    color: #1f2937;
}

/* Active State */
.history-pagination .page-item.active .page-link {
    background-color: #1f2937;
    color: #ffffff;
    border-color: #1f2937;
}

/* Disabled State */
.history-pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background-color: #f9fafb;
    cursor: not-allowed;
}


@media (max-width: 1100px) {

    .history-filter-grid {

        grid-template-columns:
            1fr
            1fr;

    }


    .history-filter-actions {

        grid-column: 1 / -1;

        margin-bottom: 0;

    }

}


@media (max-width: 700px) {

    .history-filter-grid {

        grid-template-columns: 1fr;

    }


    .history-filter-actions {

        grid-column: auto;

        flex-wrap: wrap;

    }


    .table-wrapper {

        overflow-x: auto;

    }


    .data-table {

        min-width: 1100px;

    }

    .history-pagination-wrapper {
        flex-direction: column;
        align-items: center;
    }

}

</style>

@endpush

@endsection