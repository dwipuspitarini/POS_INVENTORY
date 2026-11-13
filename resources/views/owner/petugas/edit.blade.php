@extends('layouts.app')

@section('title', 'Edit Petugas')
@section('page-title', 'Edit Petugas')

@section('content')
<div class="container-fluid edit-petugas-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Edit Petugas
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Perbarui data profil, hak akses, dan keamanan akun <strong>{{ $user->name }}</strong>.
            </p>
        </div>

        <a href="{{ route('owner.petugas.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================================================
         ERROR VALIDASI UMUM
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
         FORM EDIT PETUGAS
    ========================================================== --}}
    <form method="POST" action="{{ route('owner.petugas.update', $user) }}">
        @csrf
        @method('PUT')

        {{-- INFORMASI AKUN & AKSES --}}
        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Informasi Akun & Hak Akses
            </div>

            <div class="form-grid-2 mb-20">
                {{-- Nama --}}
                <div class="form-group mb-0">
                    <label for="name" class="form-label">
                        Nama Lengkap <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Masukkan nama petugas"
                        required
                    >
                    @error('name')
                        <small class="form-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="form-group mb-0">
                    <label for="username" class="form-label">
                        Username <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $user->username) }}"
                        placeholder="Masukkan username untuk login"
                        required
                    >
                    @error('username')
                        <small class="form-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-grid-2">
                {{-- Role --}}
                <div class="form-group mb-0">
                    <label for="role" class="form-label">
                        Role <span class="required">*</span>
                    </label>
                    <select
                        name="role"
                        id="role"
                        class="form-control @error('role') is-invalid @enderror"
                        required
                    >
                        <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>
                            Owner
                        </option>
                        <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>
                            Kasir
                        </option>
                    </select>
                    @error('role')
                        <small class="form-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-group mb-0">
                    <label for="status" class="form-label">
                        Status Akun <span class="required">*</span>
                    </label>
                    <select
                        name="status"
                        id="status"
                        class="form-control @error('status') is-invalid @enderror"
                        required
                    >
                        <option value="aktif" {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="nonaktif" {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>
                    @error('status')
                        <small class="form-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        {{-- PENGATURAN PASSWORD --}}
        <div class="form-card mb-20">
            <div class="section-title mb-4">
                Ubah Password (Opsional)
            </div>

            <div class="form-grid-2">
                {{-- Password Baru --}}
                <div class="form-group mb-0">
                    <label for="password" class="form-label">
                        Password Baru
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Kosongkan jika tidak ingin mengubah password"
                    >
                    @error('password')
                        <small class="form-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="form-group mb-0">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Password Baru
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        placeholder="Ulangi password baru"
                    >
                </div>
            </div>
        </div>

        {{-- =========================================================
             AKSI FORM
        ========================================================== --}}
        <div class="form-card">
            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                <a href="{{ route('owner.petugas.index') }}" class="btn btn-secondary">
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
.edit-petugas-page .mb-20 {
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

.form-control.is-invalid {
    border-color: #dc3545;
}

.form-error {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #dc3545;
    font-weight: 500;
}

.form-help {
    display: block;
    font-size: 13px;
    color: #6b7280;
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