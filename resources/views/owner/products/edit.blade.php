@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="container-fluid edit-product-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Edit Produk
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Perbarui informasi, harga, atau foto produk <strong>{{ $product->nama }}</strong>.
            </p>
        </div>

        <a href="{{ route('owner.products.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================================================
         ERROR VALIDASI
    ========================================================== --}}
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
         FORM EDIT PRODUK
    ========================================================== --}}
    <form action="{{ route('owner.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Informasi Dasar Produk
            </div>

            <div class="form-group">
                <label for="nama" class="form-label">
                    Nama Produk <span class="required">*</span>
                </label>
                <input
                    id="nama"
                    type="text"
                    name="nama"
                    class="form-control"
                    value="{{ old('nama', $product->nama) }}"
                    required
                    maxlength="200"
                    placeholder="Contoh: Semen Gresik 40kg"
                >
            </div>

            <div class="form-grid-2">
                <div class="form-group mb-0">
                    <label for="kategori_id" class="form-label">
                        Kategori <span class="required">*</span>
                    </label>
                    <select id="kategori_id" name="kategori_id" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('kategori_id', $product->kategori_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label for="satuan" class="form-label">
                        Satuan <span class="required">*</span>
                    </label>
                    <select id="satuan" name="satuan" class="form-control" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach(['Sak', 'Batang', 'Meter', 'Dus', 'Kaleng', 'Pcs', 'Unit'] as $satuan)
                            <option value="{{ $satuan }}"
                                {{ old('satuan', $product->satuan) == $satuan ? 'selected' : '' }}>
                                {{ $satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Harga Produk
            </div>

            <div class="form-grid-2">
                <div class="form-group mb-0">
                    <label for="harga_beli" class="form-label">
                        Harga Beli per Satuan (Rp) <span class="required">*</span>
                    </label>
                    <input
                        id="harga_beli"
                        type="number"
                        name="harga_beli"
                        class="form-control auto-select-number"
                        value="{{ old('harga_beli', $product->harga_beli) }}"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>

                <div class="form-group mb-0">
                    <label for="harga_jual" class="form-label">
                        Harga Jual per Satuan (Rp) <span class="required">*</span>
                    </label>
                    <input
                        id="harga_jual"
                        type="number"
                        name="harga_jual"
                        class="form-control auto-select-number"
                        value="{{ old('harga_jual', $product->harga_jual) }}"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>
            </div>
        </div>

        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Deskripsi & Foto Produk
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
                    placeholder="Tambahkan spesifikasi atau keterangan produk (opsional)..."
                >{{ old('deskripsi', $product->deskripsi) }}</textarea>
            </div>

            <div class="image-upload-section">
                @if($product->gambar)
                    <div class="current-image-box">
                        <span class="form-label" style="font-size: 14px; color: #6b7280;">Gambar Saat Ini</span>
                        <img
                            src="{{ asset('storage/' . $product->gambar) }}"
                            alt="{{ $product->nama }}"
                            class="product-preview-img"
                        >
                    </div>
                @endif

                <div id="new-image-preview-box" class="current-image-box" style="display: none;">
                    <span class="form-label" style="font-size: 14px; color: #065f46;">Pratinjau Gambar Baru</span>
                    <img
                        id="new-image-preview"
                        src="#"
                        alt="Pratinjau Gambar Baru"
                        class="product-preview-img"
                        style="border-color: #10b981;"
                    >
                </div>

                <div class="form-group mb-0" style="flex: 1; min-width: 260px;">
                    <label for="gambar" class="form-label">
                        {{ $product->gambar ? 'Ganti Gambar' : 'Upload Gambar' }}
                    </label>
                    <input
                        id="gambar"
                        type="file"
                        name="gambar"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                        style="padding: 9px 12px;"
                    >
                    <small class="form-help">
                        Kosongkan jika tidak ingin mengganti gambar. Format: JPG, JPEG, PNG, WEBP (Maksimal 2 MB).
                    </small>
                </div>
            </div>
        </div>

        {{-- =========================================================
             AKSI FORM
        ========================================================== --}}
        <div class="form-card">
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                <a href="{{ route('owner.products.index') }}" class="btn btn-secondary">
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                    style="background: #1f2937; border-color: #1f2937;"
                >
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.edit-product-page .mb-20 {
    margin-bottom: 20px;
}

.required {
    color: #dc3545;
}

/* =========================================================
   FORM CARD & GRID
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

.form-help {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #6b7280;
}

/* =========================================================
   IMAGE PREVIEW SECTION
========================================================= */
.image-upload-section {
    display: flex;
    align-items: flex-start;
    gap: 24px;
    flex-wrap: wrap;
}

.current-image-box {
    display: flex;
    flex-direction: column;
}

.product-preview-img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background-color: #f9fafb;
}

@media (max-width: 768px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-select angka saat input harga diklik/fokus
    document.querySelectorAll('.auto-select-number').forEach(function (input) {
        input.addEventListener('focus', function () {
            this.select();
        });
        input.addEventListener('mouseup', function (e) {
            e.preventDefault();
        });
    });

    // Live preview gambar baru saat dipilih
    const gambarInput = document.getElementById('gambar');
    const previewBox = document.getElementById('new-image-preview-box');
    const previewImg = document.getElementById('new-image-preview');

    if (gambarInput) {
        gambarInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewBox.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            } else {
                previewBox.style.display = 'none';
                previewImg.src = '#';
            }
        });
    }
});
</script>
@endpush
@endsection