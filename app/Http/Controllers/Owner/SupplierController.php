<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->get();

        return view('owner.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('owner.suppliers.index')
            ->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('owner.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('owner.suppliers.index')
            ->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('owner.suppliers.index')
            ->with('success', 'Supplier berhasil dihapus.');
    }
}