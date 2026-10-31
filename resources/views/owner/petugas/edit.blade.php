@extends('layouts.app')

@section('content')

<div class="container">

```
<div class="page-header">
    <div>
        <h1>Edit Petugas</h1>
        <p>Perbarui data akun petugas</p>
    </div>
</div>

<div class="form-card">

    <form method="POST"
          action="{{ route('owner.petugas.update', $user) }}">

        @csrf
        @method('PUT')

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
                value="{{ old('name', $user->name) }}"
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
                value="{{ old('username', $user->username) }}"
                placeholder="Masukkan username"
                required
            >

            @error('username')
                <small class="text-danger">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- Password Baru --}}
        <div class="form-group">

            <label for="password" class="form-label">
                Password Baru
            </label>

            <input
                type="password"
                name="password"
                id="password"
                class="form-control"
                placeholder="Kosongkan jika tidak ingin mengubah password"
            >

            <small style="color: #6b7280;">
                Kosongkan jika password tidak ingin diubah.
            </small>

            @error('password')
                <small class="text-danger">
                    {{ $message }}
                </small>
            @enderror

        </div>


        {{-- Konfirmasi Password --}}
        <div class="form-group">

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

                <option value="owner"
                    {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>
                    Owner
                </option>

                <option value="kasir"
                    {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>
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

        <option value="aktif"
            {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>
            Aktif
        </option>

        <option value="nonaktif"
            {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>
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
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>
```

</div>

@endsection
