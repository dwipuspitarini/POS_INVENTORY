@extends('layouts.app')

@section('content')

<div class="container">

```
<div class="page-header">
    <div>
        <h1>Tambah Petugas</h1>
        <p>Tambah akun pengguna baru ke dalam sistem</p>
    </div>
</div>

<div class="form-card">

    <form method="POST"
          action="{{ route('owner.petugas.store') }}">

        @csrf

        {{-- Nama --}}
        <div class="form-group">

            <label for="name" class="form-label">
                Nama
            </label>

            <input
                type="text"
                name="name"
                id="name"
                class="form-control"
                value="{{ old('name') }}"
                placeholder="Masukkan nama"
                required
            >

            @error('name')
                <small class="text-danger">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- Username --}}
        <div class="form-group">

            <label for="username" class="form-label">
                Username
            </label>

            <input
                type="text"
                name="username"
                id="username"
                class="form-control"
                value="{{ old('username') }}"
                placeholder="Masukkan username"
                required
            >

            @error('username')
                <small class="text-danger">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- Password --}}
        <div class="form-group">

            <label for="password" class="form-label">
                Password
            </label>

            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Masukkan password"
                required
            >

            @error('password')
                <small class="text-danger">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- Konfirmasi Password --}}
        <div class="form-group">

            <label for="password_confirmation" class="form-label">
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                class="form-control"
                placeholder="Ulangi password"
                required
            >

        </div>


        {{-- Role --}}
        <div class="form-group">

            <label for="role" class="form-label">
                Role
            </label>

            <select
                name="role"
                id="role"
                class="form-control"
                required
            >

                <option value="">
                    -- Pilih Role --
                </option>

                <option value="owner"
                    {{ old('role') === 'owner' ? 'selected' : '' }}>
                    Owner
                </option>

                <option value="kasir"
                    {{ old('role') === 'kasir' ? 'selected' : '' }}>
                    Kasir
                </option>

            </select>

            @error('role')
                <small class="text-danger">
                    {{ $message }}
                </small>
            @enderror

        </div>

        {{-- Status --}}
<div class="form-group">

    <label for="status" class="form-label">
        Status
    </label>

    <select
        name="status"
        id="status"
        class="form-control"
        required
    >

        <option value="">
            -- Pilih Status --
        </option>

        <option value="aktif"
            {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>
            Aktif
        </option>

        <option value="nonaktif"
            {{ old('status') === 'nonaktif' ? 'selected' : '' }}>
            Nonaktif
        </option>

    </select>

    @error('status')
        <small class="text-danger">
            {{ $message }}
        </small>
    @enderror

</div>


        {{-- Tombol --}}
        <div class="form-actions">

            <a href="{{ route('owner.petugas.index') }}"
               class="btn btn-secondary">
                Batal
            </a>

            <button type="submit"
                    class="btn btn-primary">
                Simpan
            </button>

        </div>

    </form>

</div>
```

</div>

@endsection
