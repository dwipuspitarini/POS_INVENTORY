@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('page-title', 'Tambah Pelanggan')

@section('content')
<div class="container-fluid customer-create-page">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; color: #1f2937; margin-bottom: 6px;">
                Tambah Pelanggan
            </h1>
            <p style="font-size: 15px; color: #6b7280; margin-bottom: 0;">
                Tambahkan data pelanggan baru
            </p>
        </div>

        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    {{-- =========================
        FORM TAMBAH PELANGGAN
    ========================== --}}
    <div class="form-card">
        <div class="section-title mb-4">
            Informasi Pelanggan
        </div>

        <form action="{{ route('customers.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama" class="form-label">
                    Nama Pelanggan <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    class="form-control"
                    value="{{ old('nama') }}"
                    required
                >

                @error('nama')
                    <small class="form-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <div class="form-group">
                <label for="telepon" class="form-label">
                    Nomor Telepon <span class="required">*</span>
                </label>

                <input
                    type="tel"
                    id="telepon"
                    name="telepon"
                    class="form-control"
                    value="{{ old('telepon') }}"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="15"
                    required
                >

                @error('telepon')
                    <small class="form-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat" class="form-label">
                    Alamat
                </label>

                <textarea
                    id="alamat"
                    name="alamat"
                    class="form-control"
                    rows="4"
                >{{ old('alamat') }}</textarea>

                @error('alamat')
                    <small class="form-error">
                        {{ $message }}
                    </small>
                @enderror
            </div>

            <div class="form-actions" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap; padding-top: 8px;">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                    style="background: #1f2937; border-color: #1f2937; padding: 10px 20px; font-size: 15px; font-weight: 600;"
                >
                    Simpan
                </button>
            </div>
        </form>
    </div>

</div>

@push('styles')
<style>
.required {
    color: #dc3545;
}

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

.form-error {
    display: block;
    margin-top: 6px;
    font-size: 14px;
    color: #dc3545;
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const teleponInput = document.getElementById('telepon');
    if (teleponInput) {
        teleponInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>
@endpush
@endsection