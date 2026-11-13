@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('page-title', 'Kategori Produk')

@section('content')
<div class="container-fluid category-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Kategori Produk
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Kelola daftar kategori produk bahan bangunan.
            </p>
        </div>
    </div>

    {{-- =========================================================
         NOTIFIKASI & ERROR VALIDASI
    ========================================================== --}}
    @if(session('success'))
        <div class="alert alert-success mb-4" style="border-radius: 8px; font-size: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 8px; font-size: 15px;">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================================================
         GRID UTAMA: FORM TAMBAH & DAFTAR KATEGORI
    ========================================================== --}}
    <div class="category-layout-grid">

        {{-- FORM TAMBAH KATEGORI --}}
        <div class="form-card">
            <div class="section-title mb-4">
                Tambah Kategori
            </div>

            <form action="{{ route('owner.categories.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nama" class="form-label">
                        Nama Kategori <span class="required">*</span>
                    </label>
                    <input
                        id="nama"
                        type="text"
                        name="nama"
                        class="form-control"
                        value="{{ old('nama') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">
                        Deskripsi
                    </label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        class="form-control"
                        rows="4"
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100"
                    style="background: #1f2937; border-color: #1f2937; width: 100%; padding: 11px 16px; font-size: 15px; font-weight: 600; justify-content: center;"
                >
                    + Tambah Kategori
                </button>
            </form>
        </div>

        {{-- DAFTAR KATEGORI --}}
        <div class="table-card">
            <div class="table-card-header d-flex justify-content-between align-items-center">
                <h2>Daftar Kategori</h2>
            </div>

            <div class="card-body-wrapper" style="padding: 20px;">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 55px;">No</th>
                                <th style="min-width: 170px;">Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th style="width: 150px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <strong>{{ $category->nama }}</strong>
                                    </td>

                                    <td style="color: #4b5563;">
                                        {{ $category->deskripsi ?: '-' }}
                                    </td>

                                    <td style="text-align: center; white-space: nowrap;">
                                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                                            <a
                                                href="{{ route('owner.categories.edit', $category) }}"
                                                class="btn-action-edit"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route('owner.categories.destroy', $category) }}"
                                                method="POST"
                                                style="display: inline; margin: 0;"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->nama }}?')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-delete">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty-data">
                                        Belum ada kategori produk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

@push('styles')
<style>
.required {
    color: #dc3545;
}

/* =========================================================
   LAYOUT GRID
========================================================= */
.category-layout-grid {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 24px;
    align-items: start;
}

/* =========================================================
   FORM CARD
========================================================= */
.form-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 24px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    padding-bottom: 14px;
    border-bottom: 1px solid #f3f4f6;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 15px;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 11px 14px;
    font-size: 15px;
    color: #1f2937;
    background-color: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.form-control:focus {
    border-color: #1f2937;
    outline: none;
    box-shadow: 0 0 0 3px rgba(31, 41, 55, 0.12);
}

/* =========================================================
   TABLE CARD & TABLE STYLING
========================================================= */
.table-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.table-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background-color: #ffffff;
}

.table-card-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.count-badge {
    background-color: #f3f4f6;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    padding: 13px 14px;
    font-size: 14px;
    font-weight: 700;
    color: #374151;
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    white-space: nowrap;
}

.data-table td {
    padding: 13px 14px;
    font-size: 15px;
    color: #1f2937;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.empty-data {
    text-align: center;
    padding: 40px 20px !important;
    font-size: 15px;
    color: #6b7280;
}

/* =========================================================
   ACTION BUTTONS
========================================================= */
.btn-action-edit {
    background-color: #fbbf24;
    color: #1f2937;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    transition: background-color 0.15s ease;
}

.btn-action-edit:hover {
    background-color: #f59e0b;
    color: #111827;
}

.btn-action-delete {
    background-color: #dc3545;
    color: #ffffff;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.btn-action-delete:hover {
    background-color: #bb2d3b;
}

@media (max-width: 900px) {
    .category-layout-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
@endsection