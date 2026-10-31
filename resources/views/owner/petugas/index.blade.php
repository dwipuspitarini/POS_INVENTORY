@extends('layouts.app')

@section('content')

<div class="container">

    <div class="page-header">

        <div>
            <h1>Petugas</h1>
            <p>Kelola akun pengguna sistem</p>
        </div>

        <a href="{{ route('owner.petugas.create') }}"
           class="btn btn-primary">
            + Tambah Petugas
        </a>

    </div>


    {{-- SEARCH --}}

    <div class="form-card mb-20">

        <form method="GET"
              action="{{ route('owner.petugas.index') }}"
              style="display: flex; gap: 10px;">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Cari nama, username, atau role..."
                value="{{ request('search') }}"
                style="flex: 1;"
            >

            <button type="submit"
                    class="btn btn-primary">
                Cari
            </button>

        </form>

    </div>


    {{-- PESAN BERHASIL --}}

    @if(session('success'))

        <div class="alert alert-success mb-20">
            {{ session('success') }}
        </div>

    @endif


    {{-- DAFTAR PETUGAS --}}

    <div class="table-card">

        <div class="table-card-header">
            Daftar Petugas
        </div>

        <div class="table-wrapper">

            <table class="data-table">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($users as $user)

                        <tr>

                            <td>
                                {{ $users->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <strong>
                                    {{ $user->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $user->username }}
                            </td>

                            <td>

                                @if($user->role === 'owner')

                                    <span class="stock-status stock-available">
                                        Owner
                                    </span>

                                @else

                                    <span class="stock-status stock-low">
                                        Kasir
                                    </span>

                                @endif

                            </td>

                            <td>
                                @if($user->status === 'aktif')
                                <span class="stock-status stock-available">
                                    Aktif</span>
                                    @else
                                    <span class="stock-status stock-low">
                                        Nonaktif</span>  
                                        @endif
                                    </td>
                            <td>
                                <a href="{{ route('owner.petugas.edit',$user) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="empty-data">

                                @if(request('search'))

                                    Tidak ada petugas yang sesuai dengan pencarian.

                                @else

                                    Belum ada data petugas.

                                @endif

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        @if($users->hasPages())

            <div style="padding: 20px;">

                {{ $users->links() }}

            </div>

        @endif

    </div>

</div>

@endsection