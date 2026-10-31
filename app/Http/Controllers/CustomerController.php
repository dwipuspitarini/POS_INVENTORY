<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::latest();
        if ($request->filled('search')) { $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', '%' . $search . '%')
              ->orWhere('telepon', 'like', '%' . $search . '%');
        });
    }
    $customers = $query
        ->paginate(10)
        ->withQueryString();
        return view('customers.index',compact('customers')
        );
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        Customer::create($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }
}