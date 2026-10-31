
@extends('layouts.app')

@section('title', 'Produk')
@section('page-title', 'Produk')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <div>
            <h1 class="page-title">Produk</h1>
            <p>Kelola data produk toko bangunan.</p>
        </div>

        <a href="{{ route('owner.products.create') }}"
            style="background: #1f2937; color: white; text-decoration: none; padding: 10px 16px; border-radius: 6px;">
            + Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d1e7dd; color: #0f5132; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #842029; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('owner.products.index') }}"
        style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Cari nama atau SKU produk..."
            value="{{ request('search') }}"
            style="flex: 1;"
        >

        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <div class="card">
        @if($products->count())
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <th style="text-align: left; padding: 10px;">No</th>
                            <th style="text-align: left; padding: 10px;">SKU</th>
                            <th style="text-align: left; padding: 10px;">Gambar</th>
                            <th style="text-align: left; padding: 10px;">Nama Produk</th>
                            <th style="text-align: left; padding: 10px;">Kategori</th>
                            <th style="text-align: center; padding: 10px;">Satuan</th>
                            <th style="text-align: right; padding: 10px;">Harga Beli</th>
                            <th style="text-align: right; padding: 10px;">Harga Jual</th>
                            <th style="text-align: center; padding: 10px;">Stok</th>
                            <th style="text-align: left; padding: 10px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($products as $product)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;">
                                    {{ $products->firstItem() + $loop->index }}
                                </td>

                                <td style="padding: 10px;">
                                    <strong>{{ $product->sku }}</strong>
                                </td>

                                <td style="padding: 10px;">
                                    @if($product->gambar)
                                        <img
                                            src="{{ asset('storage/' . $product->gambar) }}"
                                            alt="{{ $product->nama }}"
                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;"
                                        >
                                    @else
                                        <span style="color: #999;">Tidak ada</span>
                                    @endif
                                </td>

                                <td style="padding: 10px;">
                                    {{ $product->nama }}
                                </td>

                                <td style="padding: 10px;">
                                    {{ $product->category->nama ?? '-' }}
                                </td>

                                <td style="padding: 10px; text-align: center;">
                                    {{ $product->satuan }}
                                </td>

                                <td style="padding: 10px; text-align: right; white-space: nowrap;">
                                    Rp {{ number_format($product->harga_beli, 0, ',', '.') }}
                                </td>

                                <td style="padding: 10px; text-align: right; white-space: nowrap;">
                                    Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                </td>

                                <td style="padding: 10px; text-align: center;">
                                    {{ number_format($product->stok, 0, ',', '.') }}
                                    <small style="color: #777;">{{ $product->satuan }}</small>
                                </td>

                                <td style="padding: 10px;">
                                    <div style="display: flex; gap: 6px; align-items: center;">
                                        <a
                                            href="{{ route('owner.products.edit', $product) }}"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('owner.products.destroy', $product) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="product-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan
                    <strong>{{ $products->firstItem() }}</strong> -
                    <strong>{{ $products->lastItem() }}</strong>
                    dari <strong>{{ $products->total() }}</strong> produk
                </div>

                <div class="product-pagination">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <p style="color: #777; padding: 20px; text-align: center;">
                Belum ada produk.
            </p>
        @endif
    </div>

    @push('styles')
        <style>
            .product-pagination-wrapper {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 10px 5px;
                flex-wrap: wrap;
                gap: 12px;
            }

            .pagination-info {
                font-size: 14px;
                color: #6b7280;
            }

            .product-pagination .pagination {
                display: flex;
                list-style: none;
                padding: 0;
                margin: 0;
                border-radius: 6px;
                overflow: hidden;
                border: 1px solid #d1d5db;
            }

            .product-pagination .page-item {
                margin: 0;
            }

            .product-pagination .page-link {
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 38px;
                height: 38px;
                padding: 0 12px;
                font-size: 14px;
                font-weight: 500;
                color: #374151;
                background-color: #fff;
                border: none;
                border-right: 1px solid #d1d5db;
                text-decoration: none;
            }

            .product-pagination .page-item:last-child .page-link {
                border-right: none;
            }

            .product-pagination .page-link:hover {
                background-color: #f3f4f6;
                color: #1f2937;
            }

            .product-pagination .page-item.active .page-link {
                background-color: #1f2937;
                color: #fff;
                border-color: #1f2937;
            }

            .product-pagination .page-item.disabled .page-link {
                color: #9ca3af;
                background-color: #f9fafb;
                cursor: not-allowed;
            }

            @media (max-width: 640px) {
                .product-pagination-wrapper {
                    flex-direction: column;
                    align-items: center;
                }
            }
        </style>
    @endpush
@endsection
