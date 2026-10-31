@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('page-title', 'Edit Supplier')

@section('content')

    <div class="card" style="max-width: 700px;">

        <h2 style="margin-top: 0;">
            Edit Supplier
        </h2>

        @if($errors->any())

            <div style="
                background: #f8d7da;
                color: #842029;
                padding: 12px 15px;
                border-radius: 6px;
                margin-bottom: 20px;
            ">

                <ul style="margin: 0;">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif

        <form
            action="{{ route('owner.suppliers.update', $supplier) }}"
            method="POST"
        >

            @csrf
            @method('PUT')

            <div style="margin-bottom: 15px;">

                <label>
                    Nama Supplier
                </label>

                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama', $supplier->nama) }}"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 6px;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                    "
                >

            </div>

            <div style="margin-bottom: 15px;">

                <label>
                    Telepon
                </label>

                <input
                    type="text"
                    name="telepon"
                    value="{{ old('telepon', $supplier->telepon) }}"
                    required
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 6px;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                    "
                >

            </div>

            <div style="margin-bottom: 15px;">

                <label>
                    Alamat
                </label>

                <textarea
                    name="alamat"
                    rows="4"
                    style="
                        width: 100%;
                        padding: 10px;
                        margin-top: 6px;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                    "
                >{{ old('alamat', $supplier->alamat) }}</textarea>

            </div>

            <div style="display: flex; gap: 10px;">

                <button
                    type="submit"
                    style="
                        background: #1f2937;
                        color: white;
                        border: none;
                        padding: 10px 16px;
                        border-radius: 6px;
                        cursor: pointer;
                    "
                >
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('owner.suppliers.index') }}"
                    style="
                        background: #6c757d;
                        color: white;
                        text-decoration: none;
                        padding: 10px 16px;
                        border-radius: 6px;
                    "
                >
                    Batal
                </a>

            </div>

        </form>

    </div>

@endsection