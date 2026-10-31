
@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
<div class="card" style="max-width: 800px;">
    <h2 style="margin-top: 0;">Edit Produk</h2>

    @if($errors->any())
        <div style="background: #f8d7da; color: #842029; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('owner.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 15px;">
            <label for="kategori_id">Kategori</label>
            <select id="kategori_id" name="kategori_id" required
                style="width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px;">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('kategori_id', $product->kategori_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="nama">Nama Produk</label>
            <input id="nama" type="text" name="nama"
                value="{{ old('nama', $product->nama) }}" required maxlength="200"
                style="width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="satuan">Satuan</label>
            <select id="satuan" name="satuan" required
                style="width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px;">
                <option value="">-- Pilih Satuan --</option>
                @foreach(['Sak', 'Batang', 'Meter', 'Dus', 'Kaleng', 'Pcs', 'Unit'] as $satuan)
                    <option value="{{ $satuan }}"
                        {{ old('satuan', $product->satuan) == $satuan ? 'selected' : '' }}>
                        {{ $satuan }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="harga_beli">Harga Beli per Satuan (Rp)</label>
            <input id="harga_beli" type="number" name="harga_beli"
                value="{{ old('harga_beli', $product->harga_beli) }}"
                min="0" step="0.01" required
                style="width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="harga_jual">Harga Jual per Satuan (Rp)</label>
            <input id="harga_jual" type="number" name="harga_jual"
                value="{{ old('harga_jual', $product->harga_jual) }}"
                min="0" step="0.01" required
                style="width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"
                style="width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px;">{{ old('deskripsi', $product->deskripsi) }}</textarea>
        </div>

        @if($product->gambar)
            <div style="margin-bottom: 15px;">
                <label>Gambar Saat Ini</label>
                <div style="margin-top: 8px;">
                    <img src="{{ asset('storage/' . $product->gambar) }}"
                        alt="{{ $product->nama }}"
                        style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px;">
                </div>
            </div>
        @endif

        <div style="margin-bottom: 20px;">
            <label for="gambar">Ganti Gambar</label>
            <input id="gambar" type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp"
                style="width: 100%; margin-top: 6px;">
            <small style="color: #777;">Kosongkan jika tidak ingin mengganti gambar. Maksimal 2 MB.</small>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit"
                style="background: #1f2937; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer;">
                Simpan Perubahan
            </button>

            <a href="{{ route('owner.products.index') }}"
                style="background: #6c757d; color: white; text-decoration: none; padding: 10px 16px; border-radius: 6px;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
