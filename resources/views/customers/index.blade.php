@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('page-title', 'Data Pelanggan')

@section('content')

<div class="container-fluid">

    <div class="page-header">
        <div>
            <h1>Data Pelanggan</h1>
            <p>Kelola Data Pelanggan Toko Bangunan</p>
        </div>

        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            + Tambah Pelanggan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('customers.index') }}"
        style="display: flex; gap: 10px; margin-bottom: 20px;">

        <input type="text" name="search" class="form-control"
            placeholder="Cari nama atau nomor telepon pelanggan..." value="{{ request('search') }}" style="flex: 1;">

        <button type="submit" class="btn btn-primary">
            Cari
        </button>
    </form>

    <div class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th width="180">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                {{ $customers->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>
                                    {{ $customer->nama }}
                                </strong>
                            </td>

                            <td>
                                {{ $customer->telepon }}
                            </td>

                            <td>
                                {{ $customer->alamat ?? '-' }}
                            </td>

                            <td>
                                <div style="display: flex; gap: 6px;">
                                    <a href="{{ route('customers.edit', $customer) }}"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('customers.destroy', $customer) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-data">
                                Belum ada data pelanggan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($customers->count())
            <div class="customer-pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $customers->firstItem() }}</strong> - <strong>{{ $customers->lastItem() }}</strong> dari <strong>{{ $customers->total() }}</strong> pelanggan
                </div>
                <div class="customer-pagination">
                    {{ $customers->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

</div>

@push('styles')
<style>
/* =========================
   PAGINATION PELANGGAN
========================= */

.customer-pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    flex-wrap: wrap;
    gap: 12px;
}

.pagination-info {
    font-size: 14px;
    color: #6b7280;
}

.customer-pagination .pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #d1d5db;
}

.customer-pagination .page-item {
    margin: 0;
}

.customer-pagination .page-link {
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

.customer-pagination .page-item:last-child .page-link {
    border-right: none;
}

/* Hover State */
.customer-pagination .page-link:hover {
    background-color: #f3f4f6;
    color: #1f2937;
}

/* Active State */
.customer-pagination .page-item.active .page-link {
    background-color: #1f2937;
    color: #ffffff;
    border-color: #1f2937;
}

/* Disabled State */
.customer-pagination .page-item.disabled .page-link {
    color: #9ca3af;
    background-color: #f9fafb;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .customer-pagination-wrapper {
        flex-direction: column;
        align-items: center;
    }
}
</style>
@endpush

@endsection