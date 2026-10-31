@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('page-title', 'Kategori Produk')

@section('content')

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">

        <div>
            <h1 class="page-title">Kategori Produk</h1>
            <p>Kelola kategori produk bangunan.</p>
        </div>

    </div>

    @if(session('success'))
        <div style="
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        ">
            {{ session('success') }}
        </div>
    @endif

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

    <div style="
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 20px;
    ">

        {{-- FORM TAMBAH --}}
        <div class="card">

            <h3 style="margin-top: 0;">
                Tambah Kategori
            </h3>

            <form action="{{ route('owner.categories.store') }}" method="POST">

                @csrf

                <div style="margin-bottom: 15px;">

                    <label>
                        Nama Kategori
                    </label>

                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
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
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="4"
                        style="
                            width: 100%;
                            padding: 10px;
                            margin-top: 6px;
                            border: 1px solid #ccc;
                            border-radius: 6px;
                        "
                    >{{ old('deskripsi') }}</textarea>

                </div>

                <button
                    type="submit"
                    style="
                        background: #1f2937;
                        color: white;
                        border: none;
                        padding: 10px 16px;
                        border-radius: 6px;
                        cursor: pointer;
                    ">
                    Tambah Kategori
                </button>

            </form>

        </div>


        {{-- DAFTAR KATEGORI --}}
        <div class="card">

            <h3 style="margin-top: 0;">
                Daftar Kategori
            </h3>

            @if($categories->count())

                <table style="
                    width: 100%;
                    border-collapse: collapse;
                ">

                    <thead>                        
                        <tr style="border-bottom: 1px solid #ddd;">
                            <th style="text-align: left; padding: 10px;">No</th>
                            <th style="text-align: left; padding: 10px;">Nama</th>
                            <th style="text-align: left; padding: 10px;">Deskripsi</th>
                            <th style="text-align: left; padding: 10px;">Aksi</th>

                            
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($categories as $category)

                            <tr style="border-bottom: 1px solid #eee;">

                                <td style="padding: 10px;">
                                    {{ $loop->iteration }}
                                </td>

                                <td style="padding: 10px;">
                                    {{ $category->nama }}
                                </td>

                                <td style="padding: 10px;">
                                    {{ $category->deskripsi ?? '-' }}
                                </td>

                                <td style="padding: 10px;">
                                    <a href="{{ route('owner.categories.edit', $category) }}"
                                    style="
                                    background: #ffc107;
                                    color: #212529;
                                    text-decoration: none;
                                    padding: 6px 10px;
                                    border-radius: 5px;
                                    display: inline-block;">
                                    Edit</a>
                                    <form action="{{ route('owner.categories.destroy', $category) }}"
                                    method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                    style="
                                    background: #dc3545;
                                    color: white;
                                    border: none;
                                    padding: 6px 10px;
                                    border-radius: 5px;
                                    cursor: pointer;">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <p style="color: #777;">
                    Belum ada kategori produk.
                </p>

            @endif

        </div>

    </div>

@endsection