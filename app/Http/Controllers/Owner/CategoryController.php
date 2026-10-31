<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();

        return view('owner.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:categories,nama'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Category::create($validated);

        return redirect()
            ->route('owner.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('owner.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                'unique:categories,nama,' . $category->id,
            ],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()
            ->route('owner.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('owner.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}