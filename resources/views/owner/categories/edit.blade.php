@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('content')
<div class="container-fluid edit-category-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Edit Kategori
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Perbarui informasi kategori <strong>{{ $category->nama }}</strong>.
            </p>
        </div>

        <a href="{{ route('owner.categories.index') }}" class="btn btn-secondary">
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
         FORM EDIT KATEGORI
    ========================================================== --}}
    <form action="{{ route('owner.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Informasi Kategori
            </div>

            <div class="form-group">
                <label for="nama" class="form-label">
                    Nama Kategori <span class="required">*</span>
                </label>
                <input
                    id="nama"
                    type="text"
                    name="nama"
                    class="form-control"
                    value="{{ old('nama', $category->nama) }}"
                    placeholder="Contoh: Semen & Perekat"
                    required
                >
            </div>

            <div class="form-group mb-0">
                <label for="deskripsi" class="form-label">
                    Deskripsi
                </label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    class="form-control"
                    rows="4"
                    placeholder="Keterangan singkat kategori (opsional)..."
                >{{ old('deskripsi', $category->deskripsi) }}</textarea>
            </div>
        </div>

        {{-- =========================================================
             AKSI FORM
        ========================================================== --}}
        <div class="form-card">
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                <a href="{{ route('owner.categories.index') }}" class="btn btn-secondary">
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
.edit-category-page .mb-20 {
    margin-bottom: 20px;
}

.required {
    color: #dc3545;
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
</style>
@endpush
@endsection