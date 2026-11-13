@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

@section('content')
<div class="container-fluid product-create-page">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 700; color: #1f2937; margin-bottom: 4px;">
                Tambah Produk
            </h1>
            <p style="color: #6b7280; margin-bottom: 0;">
                Tambahkan data produk baru ke dalam katalog toko.
            </p>
        </div>

        <a href="{{ route('owner.products.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 8px;">
            <strong>Periksa kembali data berikut:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('owner.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- =========================
            INFORMASI PRODUK
        ========================== --}}
        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Informasi Produk
            </div>

            <div class="form-grid-2">
                {{-- KATEGORI --}}
                <div class="form-group">
                    <label for="kategori_id" class="form-label">
                        Kategori <span class="required">*</span>
                    </label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('kategori_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SATUAN --}}
                <div class="form-group">
                    <label for="satuan" class="form-label">
                        Satuan <span class="required">*</span>
                    </label>
                    <select id="satuan" name="satuan" class="form-control" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach(['Sak', 'Batang', 'Meter', 'Dus', 'Kaleng', 'Pcs', 'Unit'] as $satuan)
                            <option value="{{ $satuan }}"
                                {{ old('satuan') == $satuan ? 'selected' : '' }}>
                                {{ $satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- NAMA PRODUK --}}
            <div class="form-group">
                <label for="nama" class="form-label">
                    Nama Produk <span class="required">*</span>
                </label>
                <input id="nama" type="text" name="nama" class="form-control"
                       value="{{ old('nama') }}"
                       placeholder="" required maxlength="200">
            </div>

            <div class="form-grid-2">
                {{-- HARGA BELI --}}
                <div class="form-group">
                    <label for="harga_beli" class="form-label">
                        Harga Beli per Satuan (Rp) <span class="required">*</span>
                    </label>
                    <input id="harga_beli" type="number" name="harga_beli" class="form-control"
                           value="{{ old('harga_beli') }}" min="0" step="0.01" required>
                </div>

                {{-- HARGA JUAL --}}
                <div class="form-group">
                    <label for="harga_jual" class="form-label">
                        Harga Jual per Satuan (Rp) <span class="required">*</span>
                    </label>
                    <input id="harga_jual" type="number" name="harga_jual" class="form-control"
                           value="{{ old('harga_jual') }}" min="0" step="0.01" required>
                </div>
            </div>

            {{-- DESKRIPSI --}}
            <div class="form-group">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"
                          placeholder="Deskripsi produk...">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- GAMBAR PRODUK --}}
            <div class="form-group" style="margin-bottom: 0;">
                <label for="gambar" class="form-label">Gambar Produk</label>
                <input id="gambar" type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"
                       class="form-control file-input-custom">
                <small class="form-help">Format JPG, JPEG, PNG, WEBP. Maksimal 2 MB.</small>
            </div>
        </div>

        {{-- =========================
            TOMBOL AKSI
        ========================== --}}
        <div class="form-card">
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('owner.products.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="background: #1f2937; border-color: #1f2937;">
                    Simpan Produk
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.product-create-page .mb-20 {
    margin-bottom: 20px;
}

.required {
    color: #dc3545;
}

.form-card {
    width: 100%;
    padding: 24px;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    box-sizing: border-box;
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    padding-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
}

.form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    font-size: 14px;
    color: #374151;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    color: #1f2937;
    background-color: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.form-control:focus {
    border-color: #2c3e50;
    outline: none;
    box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.12);
}

.file-input-custom {
    padding: 8px 12px;
    background-color: #f9fafb;
}

.form-help {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
}

@media (max-width: 768px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
        gap: 0;
    }
}
</style>
@endpush
@endsection