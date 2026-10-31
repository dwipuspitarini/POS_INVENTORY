<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Daftar petugas
     */
    public function index(Request $request)
    {
        $query = User::query()
            ->orderBy('name', 'asc');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'name',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'username',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'role',
                    'like',
                    '%' . $search . '%'
                );

            });
        }

        $users = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'owner.petugas.index',
            compact('users')
        );
    }


    /**
     * Form tambah petugas
     */
    public function create()
    {
        return view('owner.petugas.create');
    }


    /**
     * Simpan petugas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'role' => [
                'required',
                'in:owner,kasir',
            ],
            'status' => [
                'required',
                'in:aktif,nonaktif'
            ],
        ], [
            'name.required' =>
                'Nama harus diisi.',

            'username.required' =>
                'Username harus diisi.',

            'username.unique' =>
                'Username sudah digunakan.',

            'password.required' =>
                'Password harus diisi.',

            'password.min' =>
                'Password minimal 6 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'role.required' =>
                'Role harus dipilih.',

            'role.in' =>
                'Role tidak valid.',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('owner.petugas.index')
            ->with(
                'success',
                'Petugas berhasil ditambahkan.'
            );
    }


    /**
     * Form edit petugas
     */
    public function edit(User $user)
    {
        return view(
            'owner.petugas.edit',
            compact('user')
        );
    }


    /**
     * Update petugas
     */
    public function update(
        Request $request,
        User $user
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')
                    ->ignore($user->id),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],

            'role' => [
                'required',
                'in:owner,kasir',
            ],
            'status' => [
                'required',
                'in:aktif,nonaktif'
            ],
        ], [
            'name.required' =>
                'Nama harus diisi.',

            'username.required' =>
                'Username harus diisi.',

            'username.unique' =>
                'Username sudah digunakan.',

            'password.min' =>
                'Password minimal 6 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'role.required' =>
                'Role harus dipilih.',

            'role.in' =>
                'Role tidak valid.',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make(
                $validated['password']
            );
        }

        $user->save();

        return redirect()
            ->route('owner.petugas.index')
            ->with(
                'success',
                'Data petugas berhasil diperbarui.'
            );
    }
}