@extends('layouts.app')

@section('title', 'Edit Pelanggan')
@section('page-title', 'Edit Pelanggan')

@section('content')

<div class="container">

    <div class="page-header">

        <div>
            <h1>Edit Pelanggan</h1>
            <p>Perbarui data pelanggan</p>
        </div>

    </div>

    <div class="form-card">

        <form action="{{ route('customers.update', $customer) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="form-group">

                <label class="form-label">
                    Nama Pelanggan
                </label>

                <input type="text"
                       name="nama"
                       class="form-control"
                       value="{{ old('nama', $customer->nama) }}"
                       required>

                @error('nama')
                    <small style="color: #dc3545;">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <div class="form-group">

                <label class="form-label">
                    Nomor Telepon
                </label>

                <input type="text"
                       name="telepon"
                       class="form-control"
                       value="{{ old('telepon', $customer->telepon) }}"
                       required>

                @error('telepon')
                    <small style="color: #dc3545;">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <div class="form-group">

                <label class="form-label">
                    Alamat
                </label>

                <textarea name="alamat"
                          class="form-control"
                          rows="4">{{ old('alamat', $customer->alamat) }}</textarea>

                @error('alamat')
                    <small style="color: #dc3545;">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <div class="form-actions">

                <a href="{{ route('customers.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection