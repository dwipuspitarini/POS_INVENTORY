<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        $products = $query
            ->paginate(10)
            ->withQueryString();

        return view('owner.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('nama')->get();

        return view('owner.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => ['required', 'exists:categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'satuan' => [
                'required',
                Rule::in([
                    'Sak',
                    'Batang',
                    'Meter',
                    'Dus',
                    'Kaleng',
                    'Pcs',
                    'Unit',
                ]),
            ],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        /*
         * Generate SKU numerik otomatis.
         * Contoh: 001, 002, 003.
         */
        $lastProduct = Product::whereRaw('sku REGEXP "^[0-9]+$"')
            ->orderByRaw('CAST(sku AS UNSIGNED) DESC')
            ->first();

        $nextNumber = $lastProduct
            ? ((int) $lastProduct->sku + 1)
            : 1;

        $validated['sku'] = str_pad(
            (string) $nextNumber,
            3,
            '0',
            STR_PAD_LEFT
        );

        // Stok awal dikelola melalui menu stok masuk.
        $validated['stok'] = 0;

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request
                ->file('gambar')
                ->store('produk', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('nama')->get();

        return view(
            'owner.products.edit',
            compact('product', 'categories')
        );
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'kategori_id' => ['required', 'exists:categories,id'],
            'nama' => ['required', 'string', 'max:200'],
            'satuan' => [
                'required',
                Rule::in([
                    'Sak',
                    'Batang',
                    'Meter',
                    'Dus',
                    'Kaleng',
                    'Pcs',
                    'Unit',
                ]),
            ],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request
                ->file('gambar')
                ->store('produk', 'public');

            if ($product->gambar) {
                Storage::disk('public')->delete($product->gambar);
            }
        }

        // Jangan ubah SKU dan stok ketika mengedit produk.
        $product->update($validated);

        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if (
            $product->transactionDetails()->exists()
            || $product->stockMovements()->exists()
        ) {
            return redirect()
                ->route('owner.products.index')
                ->with(
                    'error',
                    'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi atau pergerakan stok.'
                );
        }

        if ($product->gambar) {
            Storage::disk('public')->delete($product->gambar);
        }

        $product->delete();

        return redirect()
            ->route('owner.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
