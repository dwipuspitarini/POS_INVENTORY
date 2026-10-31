<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'KASIR TOKO BANGUNAN')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #333;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px;
            background: #1f2937;
            color: white;
            padding: 20px;
            flex-shrink: 0;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .menu-title {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            color: #d1d5db;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 4px;
        }

        .sidebar a:hover {
            background: #374151;
            color: white;
        }

        /* CONTENT */
        .main {
            flex: 1;
            min-width: 0;
        }

        .navbar {
            height: 65px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
        }

        .navbar-title {
            font-size: 18px;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .role {
            color: #6b7280;
            font-size: 14px;
        }

        .logout-btn {
            border: none;
            background: #dc3545;
            color: white;
            padding: 8px 13px;
            border-radius: 6px;
            cursor: pointer;
        }

        .content {
            padding: 25px;
        }

        /* DASHBOARD CARD */
        .page-title {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e5e7eb;
        }

        .card-title {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .card-value {
            font-size: 25px;
            font-weight: bold;
        }
        /* =========================
            GENERAL COMPONENTS
        ========================= */

.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}

.container-fluid {
    width: 100%;
}

/* PAGE HEADER */

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.page-header h1 {
    margin: 0 0 5px 0;
    font-size: 28px;
}

.page-header p {
    margin: 0;
    color: #6b7280;
}

/* BUTTON */

.btn {
    display: inline-block;
    padding: 9px 14px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    font-family: Arial, sans-serif;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #bb2d3b;
}

.btn-sm {
    padding: 6px 10px;
    font-size: 13px;
}

.d-inline {
    display: inline;
}

/* ALERT */

.alert {
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

/* TABLE */

.table-card {
    background: white;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.data-table th {
    background: #f9fafb;
    font-weight: bold;
    color: #374151;
}

.data-table tbody tr:hover {
    background: #f9fafb;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

/* FORM */

.form-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    border: 1px solid #e5e7eb;
}

.form-group {
    margin-bottom: 18px;
}

.form-label {
    display: block;
    margin-bottom: 7px;
    font-weight: bold;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    outline: none;
}

.form-control:focus {
    border-color: #2563eb;
}

textarea.form-control {
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 10px;
}

.text-muted {
    color: #6b7280;
}

.empty-data {
    text-align: center !important;
    color: #6b7280;
    padding: 30px !important;
}
.mb-20 {
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #1f2937;
}

.product-input-grid {
    display: grid;
    grid-template-columns: 1fr 150px 120px;
    gap: 15px;
    align-items: end;
}

.product-button {
    width: 100%;
}

.form-error {
    margin-top: 10px;
    padding: 10px 12px;
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
    border-radius: 6px;
    font-size: 14px;
}

.table-card-header {
    padding: 18px 20px;
    font-size: 18px;
    font-weight: bold;
    border-bottom: 1px solid #e5e7eb;
}

.transaction-bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.summary-row:last-child {
    border-bottom: none;
}

.total-row {
    font-size: 18px;
    padding-top: 18px;
}

.total-row strong {
    font-size: 22px;
}

.payment-info {
    margin-top: 15px;
    margin-bottom: 20px;
    padding: 10px 15px;
    background: #f9fafb;
    border-radius: 6px;
}

.btn-full {
    width: 100%;
}

.cart-quantity {
    width: 80px;
    padding: 7px 8px;
    border: 1px solid #d1d5db;
    border-radius: 5px;
    text-align: center;
}
.alert-danger {
    padding: 12px 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

@media (max-width: 800px) {

    .product-input-grid {
        grid-template-columns: 1fr;
    }

    .transaction-bottom-grid {
        grid-template-columns: 1fr;
    }

}

        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                display: none;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="brand">
            KASIR TOKO BANGUNAN
        </div>

        @if(Auth::user()->role === 'owner')

            <div class="menu-title">Menu Utama</div>

            <a href="{{ route('dashboard.owner') }}">
                Dashboard
            </a>

            <div class="menu-title">Master Data</div>

            <a href="{{ route('owner.products.index') }}">
                Produk
            </a>

            <a href="{{ route('owner.categories.index') }}">
            Kategori Produk
            </a>

            <a href="{{ route('owner.suppliers.index') }}">
                Supplier
            </a>

            <a href="{{ route('customers.index') }}">
                <i class="bi bi-people"></i>
                <span>Pelanggan</span>
            </a>

            <a href="{{ route('owner.petugas.index') }}">
                Petugas
            </a>

            <div class="menu-title">Transaksi</div>

            <a href="{{ route('transactions.index') }}">
                Transaksi Penjualan
            </a>

            <div class="menu-title">STOK</div>
            <a href="{{ route('stocks.index') }}">
                Daftar Stok
            </a>
            <a href="{{ route('owner.stocks.history') }}">
                Riwayat Stok
            </a>
            <a href="{{ route('owner.stock-opnames.index') }}">
                Penyesuaian Stok
            </a>

            <div class="menu-title">Laporan</div>

            <a href="{{ route('owner.laporan.pendapatan') }}">
                Laporan Pendapatan
            </a>

            <a href="{{ route('owner.laporan.penjualan') }}">
                Laporan Penjualan
            </a>

            <a href="{{ route('owner.laporan.stok') }}">
                Laporan Stok
            </a>

        @elseif(Auth::user()->role === 'kasir')

            <div class="menu-title">Menu Utama</div>

            <a href="{{ route('dashboard.kasir') }}">
                Dashboard
            </a>

            <div class="menu-title">Transaksi</div>

            <a href="{{ route('transactions.index') }}">
                Transaksi Penjualan
            </a>

            <a href="{{ route('customers.index') }}">
                <i class="bi bi-people"></i>
                <span>Pelanggan</span>
            </a>

            <a href="{{ route('stocks.index') }}">
                Stok
            </a>
            

        @endif

    </aside>


    {{-- MAIN --}}
    <main class="main">

        <nav class="navbar">

            <div class="navbar-title">
                @yield('page-title', 'Dashboard')
            </div>

            <div class="user-info">

                <div>
                    <strong>{{ Auth::user()->name }}</strong>
                    <div class="role">
                        {{ ucfirst(Auth::user()->role) }}
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="logout-btn">
                        Logout
                    </button>
                </form>

            </div>

        </nav>

        <section class="content">

            @yield('content')

        </section>

    </main>

</div>

@stack('scripts')

</body>
</html>