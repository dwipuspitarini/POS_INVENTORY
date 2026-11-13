@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')

@section('content')
<div class="container-fluid edit-supplier-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Edit Supplier
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Perbarui data kontak dan alamat supplier <strong>{{ $supplier->nama }}</strong>.
            </p>
        </div>

        <a href="{{ route('owner.suppliers.index') }}" class="btn btn-secondary">
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
         FORM EDIT SUPPLIER
    ========================================================== --}}
    <form action="{{ route('owner.suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Informasi Supplier
            </div>

            <div class="form-grid-2 mb-20">
                <div class="form-group mb-0">
                    <label for="nama" class="form-label">
                        Nama Supplier <span class="required">*</span>
                    </label>
                    <input
                        id="nama"
                        type="text"
                        name="nama"
                        class="form-control"
                        value="{{ old('nama', $supplier->nama) }}"
                        placeholder="Contoh: PT Sumber Makmur Bangunan"
                        required
                    >
                </div>

                <div class="form-group mb-0">
                    <label for="telepon" class="form-label">
                        Telepon <span class="required">*</span>
                    </label>
                    <input
                        id="telepon"
                        type="text"
                        name="telepon"
                        class="form-control"
                        value="{{ old('telepon', $supplier->telepon) }}"
                        placeholder="Contoh: 081234567890"
                        required
                    >
                </div>
            </div>

            <div class="form-group mb-0">
                <label for="alamat" class="form-label">
                    Alamat
                </label>
                <textarea
                    id="alamat"
                    name="alamat"
                    class="form-control"
                    rows="4"
                    placeholder="Masukkan alamat lengkap supplier (opsional)..."
                >{{ old('alamat', $supplier->alamat) }}</textarea>
            </div>
        </div>

        {{-- =========================================================
             AKSI FORM
        ========================================================== --}}
        <div class="form-card">
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                <a href="{{ route('owner.suppliers.index') }}" class="btn btn-secondary">
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
.edit-supplier-page .mb-20 {
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

@media (max-width: 768px) {
    .form-grid-2 {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}
</style>
@endpush
@endsection