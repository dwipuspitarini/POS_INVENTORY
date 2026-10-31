@extends('layouts.app')

@section('title', 'Transaksi Baru')
@section('page-title', 'Transaksi Baru')

@section('content')

<div class="container-fluid">

<form id="transaction-form"
      action="{{ route('transactions.store') }}"
      method="POST">

    @csrf

    <input type="hidden" name="cart" id="cart-data">

        {{-- =========================================================
             ERROR SERVER
        ========================================================== --}}

        @if($errors->any())

            <div class="alert alert-danger">

                <strong>Terjadi kesalahan:</strong>

                <ul style="margin:8px 0 0 20px;">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- =========================================================
             HEADER
        ========================================================== --}}

        <div class="page-header">

            <div>

                <h1>
                    Transaksi Penjualan
                </h1>

                <p>
                    Buat transaksi penjualan baru
                </p>

            </div>


            <a
                href="{{ route('transactions.index') }}"
                class="btn btn-secondary"
            >
                Kembali
            </a>

        </div>


        {{-- =========================================================
             DATA PELANGGAN
        ========================================================== --}}

        <div class="form-card mb-20">

            <div class="section-title">
                Data Pelanggan
            </div>


            <div class="form-group">

                <label class="form-label">
                    Pelanggan
                </label>


                <select
                    id="pelanggan_id"
                    name="pelanggan_id"
                    class="form-control"
                >

                    <option value="">
                        Pelanggan Umum
                    </option>


                    @foreach($customers as $customer)

                        <option
                            value="{{ $customer->id }}"
                            {{ old('pelanggan_id') == $customer->id ? 'selected' : '' }}
                        >
                            {{ $customer->nama }}
                            -
                            {{ $customer->telepon }}
                        </option>

                    @endforeach
                </select>
            </div>
        </div>


        {{-- TANGGAL TRANSAKSI HISTORIS --}}
@if(config('transactions.allow_backdate', false))
    <div class="form-card mb-20">
        <div class="section-title">
            Tanggal Transaksi
        </div>

        <div class="form-group">
            <label for="tanggal_transaksi" class="form-label">
                Tanggal dan Waktu Penjualan
            </label>

            <input
                type="datetime-local"
                id="tanggal_transaksi"
                name="tanggal_transaksi"
                class="form-control"
                value="{{ old('tanggal_transaksi', now()->format('Y-m-d\TH:i')) }}"
                min="2026-09-01T00:00"
                max="2026-09-30T23:59"
                required
            >

            <small class="text-muted">
                Mode input data lama aktif. Pilih tanggal penjualan
                yang sebenarnya pada September 2026.
            </small>
        </div>
    </div>
@endif




        {{-- =================TAMBAH PRODUK================== --}}
        <div class="form-card mb-20">
            <div class="section-title">Tambah Produk</div>
            <div class="product-input-grid">
                {{-- PRODUK --}}
                <div>
                    <label class="form-label">Produk</label>
                    <input type="text" id="product-search" class="form-control"
                    placeholder="Cari SKU atau nama produk..."
                    autocomplete="off"> 
                    <div id="product-results" style="
                    display: none;
                    margin-top: 5px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    background: #fff;
                    max-height: 250px;
                    overflow-y: auto;">
                    </div>
                    <input type="hidden" id="product_id">
                </div>
                {{-- JUMLAH --}}
                <div>
                    <label class="form-label">
                        Jumlah
                    </label>
                    <input type="number"id="quantity"class="form-control"value="1"min="1">
                </div>
                {{-- TOMBOL TAMBAH --}}
                <div class="product-button">
                    <label class="form-label">
                        &nbsp;
                    </label>
                    <button type="button" id="add-product" class="btn btn-primary">
                        + Tambah
                    </button>
                </div>
            </div>
            <div id="product-error"class="form-error"style="display:none;">
            </div>
        </div>
        {{-- ==================KERANJANG========================== --}}
        <div class="table-card mb-20">
            <div class="table-card-header">
                Keranjang Belanja
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th width="120">Jumlah</th>
                            <th>Subtotal</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr>
                            <td colspan="6"class="empty-data">
                                Belum ada produk dalam keranjang.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        {{-- =========================================================
             BAGIAN BAWAH
        ========================================================== --}}

        <div class="transaction-bottom-grid">


            {{-- =====================================================
                 RINGKASAN
            ====================================================== --}}

            <div class="form-card">

                <div class="section-title">
                    Ringkasan Transaksi
                </div>


                <div class="summary-row">

                    <span>
                        Total
                    </span>


                    <strong id="display-total">
                        Rp 0
                    </strong>

                </div>


                <div class="form-group">

                    <label class="form-label">
                        Diskon
                    </label>
                    <input type="number"id="diskon"name="diskon"class="form-control"value="{{ old('diskon', 0) }}"min="0">
                </div>
                <div class="summary-row total-row">

                    <span>
                        Total Akhir
                    </span>


                    <strong id="display-total-akhir">
                        Rp 0
                    </strong>

                </div>

            </div>


            {{-- =================PEMBAYARAN==================== --}}

            <div class="form-card">

                <div class="section-title">
                    Pembayaran
                </div>


                {{-- METODE --}}

                <div class="form-group">

                    <label class="form-label">
                        Metode Pembayaran
                    </label>


                    <select
                        id="metode_pembayaran"
                        name="metode_pembayaran"
                        class="form-control"
                    >

                        <option value="tunai">
                            Tunai
                        </option>

                        <option value="qris">
                            QRIS
                        </option>

                        <option value="mesin">
                            Mesin
                        </option>

                    </select>

                </div>


                {{-- STATUS --}}

                <div class="form-group">

                    <label class="form-label">
                        Status Pembayaran
                    </label>


                    <select
                        id="status_pembayaran"
                        name="status_pembayaran"
                        class="form-control"
                    >

                        <option value="lunas">
                            Lunas
                        </option>

                        <option value="dp">
                            DP
                        </option>

                    </select>

                </div>


                {{-- JUMLAH BAYAR --}}

                <div class="form-group">

                    <label class="form-label">
                        Jumlah Bayar
                    </label>


                    <input
                        type="number"
                        id="jumlah_bayar"
                        name="jumlah_bayar"
                        class="form-control"
                        min="0"
                        value="{{ old('jumlah_bayar', 0) }}"
                    >

                </div>


                {{-- INFORMASI PEMBAYARAN --}}

                <div class="payment-info">


                    <div class="summary-row">

                        <span>
                            Total Akhir
                        </span>


                        <strong id="payment-total">
                            Rp 0
                        </strong>

                    </div>


                    <div class="summary-row">

                        <span>
                            Kembalian
                        </span>


                        <strong id="display-kembalian">
                            Rp 0
                        </strong>

                    </div>


                    <div class="summary-row">

                        <span>
                            Sisa Pembayaran
                        </span>


                        <strong id="display-sisa">
                            Rp 0
                        </strong>

                    </div>

                </div>


                {{-- ERROR PEMBAYARAN --}}

                <div
                    id="payment-error"
                    class="form-error"
                    style="display:none;"
                >
                </div>


                {{-- SIMPAN --}}

                <button
                    type="submit"
                    id="save-transaction"
                    class="btn btn-primary btn-full"
                >
                    Simpan Transaksi
                </button>

            </div>

        </div>
</form>
</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    let cart = [];
    const products = @json($products);

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }


    // =========================
    // ELEMENT
    // =========================

    const productSearch =
        document.getElementById('product-search');

    const productResults =
        document.getElementById('product-results');

    const productIdInput =
        document.getElementById('product_id');

    const quantityInput =
        document.getElementById('quantity');

    const addProductButton =
        document.getElementById('add-product');

    const cartBody =
        document.getElementById('cart-body');

    const diskonInput =
        document.getElementById('diskon');

    const jumlahBayarInput =
        document.getElementById('jumlah_bayar');

    const statusPembayaran =
        document.getElementById('status_pembayaran');

    const transactionForm =
        document.getElementById('transaction-form');

    const cartData =
        document.getElementById('cart-data');


    // =========================
    // PENCARIAN PRODUK
    // =========================]
    productSearch.addEventListener('input', function () {
    const keyword =
        this.value.trim().toLowerCase();
    productResults.innerHTML = '';
    if (!keyword) {
        productResults.style.display = 'none';
        productIdInput.value = '';
        return;
    }
    const results = products.filter(function (product) {
        return (
            product.sku.toLowerCase().includes(keyword) ||
            product.nama.toLowerCase().includes(keyword)
        );
    });
    if (results.length === 0) {
        productResults.innerHTML = `
            <div style="padding: 10px; color: #777;">
                Produk tidak ditemukan.
            </div>
        `;
        productResults.style.display = 'block';
        return;
    }
    results.forEach(function (product) {
        const item =
            document.createElement('div');

        item.style.padding = '10px';
        item.style.cursor = 'pointer';
        item.style.borderBottom = '1px solid #eee';
        item.innerHTML = `
            <strong>
                ${product.sku} — ${product.nama}
            </strong>

            <div style="font-size: 12px; color: #666; margin-top: 4px;">
                Rp ${new Intl.NumberFormat('id-ID').format(product.harga)}
                — Stok: ${product.stok}
            </div>
        `;
        item.addEventListener('click', function () {

            productIdInput.value =
                product.id;

            productSearch.value =
                product.sku + ' — ' + product.nama;

            productResults.style.display =
                'none';
        });
        productResults.appendChild(item);
    });
    productResults.style.display = 'block';
    });

    // =========================
    // TAMBAH PRODUK
    // =========================

    addProductButton.addEventListener('click', function () {

        const productId =
        productIdInput.value;
        const selectedProduct =
        products.find(function (product) {

        return product.id == productId;
        });

        const quantity =
            parseInt(quantityInput.value);

        showProductError('');


        // Produk belum dipilih
        if (!productId) {

            showProductError(
                'Silakan pilih produk terlebih dahulu.'
            );

            return;
        }


        // Jumlah tidak valid
        if (!quantity || quantity < 1) {

            showProductError(
                'Jumlah produk minimal 1.'
            );

            return;
        }


        const nama =
        selectedProduct.nama;

        const sku =
        selectedProduct.sku;

        const harga =
        parseFloat(selectedProduct.harga_jual);

        const stok =
        parseInt(selectedProduct.stok);


        // Cek stok
        if (quantity > stok) {

            showProductError(
                'Jumlah melebihi stok tersedia. Stok saat ini: ' + stok
            );

            return;
        }


        // Cek apakah produk sudah ada
        const existingProduct =
            cart.find(function (item) {

                return item.product_id == productId;

            });


        if (existingProduct) {

            const newQuantity =
                existingProduct.quantity + quantity;


            if (newQuantity > stok) {

                showProductError(
                    'Jumlah produk melebihi stok tersedia.'
                );

                return;
            }


            existingProduct.quantity =
                newQuantity;

            existingProduct.subtotal =
                existingProduct.harga *
                existingProduct.quantity;

        } else {

            cart.push({
                product_id: productId,
                sku: sku,
                nama: nama,
                harga: harga,
                quantity: quantity,
                subtotal: harga * quantity,
                stok: stok
            });

        }


        // Reset input
        productSearch.value = '';
        productIdInput.value = '';
        productResults.style.display = 'none';

        quantityInput.value = 1;


        // Tampilkan keranjang
        renderCart();

    });


    // =========================
    // TAMPILKAN KERANJANG
    // =========================

    function renderCart() {

        cartBody.innerHTML = '';


        if (cart.length === 0) {

            cartBody.innerHTML = `
                <tr>
                    <td colspan="6"
                        class="empty-data">
                        Belum ada produk dalam keranjang.
                    </td>
                </tr>
            `;

            updateSummary();

            return;
        }


        cart.forEach(function (item, index) {

            const row =
                document.createElement('tr');


            row.innerHTML = `

                <td>
                    ${index + 1}
                </td>

                <td>
                    <strong>
                        ${item.nama}
                    </strong>
                    <div style="font-size: 12px; color: #666; margin-top: 4px;">
                        SKU: ${item.sku}
                    </div>
                </td>

                <td>
                    ${formatRupiah(item.harga)}
                </td>

                <td>

                    <input
                        type="number"
                        min="1"
                        max="${item.stok}"
                        value="${item.quantity}"
                        class="cart-quantity"
                        data-index="${index}"
                    >

                </td>

                <td>
                    ${formatRupiah(item.subtotal)}
                </td>

                <td>

                    <button
                        type="button"
                        class="btn btn-danger btn-sm remove-product"
                        data-index="${index}">
                        Hapus
                    </button>

                </td>

            `;


            cartBody.appendChild(row);

        });


        // =========================
        // UBAH JUMLAH
        // =========================

        
document
    .querySelectorAll('.cart-quantity')
    .forEach(function (input) {
        input.addEventListener('change', function () {
            const index = parseInt(this.dataset.index);
            const previousQuantity = cart[index].quantity;
            const quantity = parseInt(this.value);

            if (!quantity || quantity < 1) {
                alert('Jumlah produk minimal 1.');
                this.value = previousQuantity;
                return;
            }

            if (quantity > cart[index].stok) {
                alert(
                    'Stok tidak mencukupi!\n' +
                    'Produk: ' + cart[index].nama + '\n' +
                    'Stok tersedia: ' + cart[index].stok + '\n' +
                    'Jumlah yang diminta: ' + quantity
                );

                this.value = previousQuantity;
                return;
            }

            cart[index].quantity = quantity;
            cart[index].subtotal =
                cart[index].harga * quantity;

            renderCart();
        });
    });



        // =========================
        // HAPUS PRODUK
        // =========================

        document
            .querySelectorAll('.remove-product')
            .forEach(function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        const index =
                            parseInt(
                                this.dataset.index
                            );


                        cart.splice(index, 1);


                        renderCart();

                    }
                );

            });


        updateSummary();

    }


    // =========================
    // TOTAL
    // =========================

    function getTotal() {

        return cart.reduce(
            function (total, item) {

                return total + item.subtotal;

            },
            0
        );

    }


    // =========================
    // RINGKASAN
    // =========================

    function updateSummary() {

        const total =
            getTotal();


        let diskon =
            parseFloat(
                diskonInput.value
            ) || 0;


        if (diskon < 0) {

            diskon = 0;

        }


        if (diskon > total) {

            diskon = total;

            diskonInput.value =
                total;

        }


        const totalAkhir =
            total - diskon;


        document
            .getElementById('display-total')
            .textContent =
            formatRupiah(total);


        document
            .getElementById('display-total-akhir')
            .textContent =
            formatRupiah(totalAkhir);


        document
            .getElementById('payment-total')
            .textContent =
            formatRupiah(totalAkhir);


        updatePayment();

    }


    // =========================
    // PEMBAYARAN
    // =========================

    function updatePayment() {

        const total =
            getTotal();


        const diskon =
            parseFloat(
                diskonInput.value
            ) || 0;


        const totalAkhir =
            Math.max(
                total - diskon,
                0
            );


        const jumlahBayar =
            parseFloat(
                jumlahBayarInput.value
            ) || 0;


        let kembalian = 0;

        let sisa = 0;


        if (jumlahBayar >= totalAkhir) {

            kembalian =
                jumlahBayar - totalAkhir;

        } else {

            sisa =
                totalAkhir - jumlahBayar;

        }


        document
            .getElementById('display-kembalian')
            .textContent =
            formatRupiah(kembalian);


        document
            .getElementById('display-sisa')
            .textContent =
            formatRupiah(sisa);

    }


    // =========================
    // EVENT PEMBAYARAN
    // =========================

    diskonInput.addEventListener(
        'input',
        updateSummary
    );


    jumlahBayarInput.addEventListener(
        'input',
        updatePayment
    );


    statusPembayaran.addEventListener(
        'change',
        updatePayment
    );


    // =========================
    // ERROR PRODUK
    // =========================

    function showProductError(message) {

        const error =
            document.getElementById(
                'product-error'
            );


        if (!message) {

            error.style.display =
                'none';

            error.textContent =
                '';

            return;

        }


        error.textContent =
            message;

        error.style.display =
            'block';

    }


    // =========================
    // SUBMIT TRANSAKSI
    // =========================

    transactionForm.addEventListener(
        'submit',
        function (event) {

            event.preventDefault();


            const paymentError =
                document.getElementById(
                    'payment-error'
                );


            paymentError.style.display =
                'none';

            paymentError.textContent =
                '';


            // Keranjang kosong
            if (cart.length === 0) {

                paymentError.textContent =
                    'Keranjang masih kosong. Tambahkan produk terlebih dahulu.';

                paymentError.style.display =
                    'block';

                return;

            }


            const total =
                getTotal();


            const diskon =
                parseFloat(
                    diskonInput.value
                ) || 0;


            const totalAkhir =
                total - diskon;


            const jumlahBayar =
                parseFloat(
                    jumlahBayarInput.value
                ) || 0;


            const status =
                statusPembayaran.value;


            // LUNAS
            if (
                status === 'lunas' &&
                jumlahBayar < totalAkhir
            ) {

                paymentError.textContent =
                    'Untuk pembayaran lunas, jumlah bayar tidak boleh kurang dari total akhir.';

                paymentError.style.display =
                    'block';

                return;

            }


            // DP
            if (
                status === 'dp' &&
                jumlahBayar <= 0
            ) {

                paymentError.textContent =
                    'Untuk pembayaran DP, jumlah bayar harus lebih dari 0.';

                paymentError.style.display =
                    'block';

                return;

            }


            // =========================
            // KIRIM CART KE LARAVEL
            // =========================

            cartData.value =
                JSON.stringify(

                    cart.map(function (item) {

                        return {

                            product_id:
                                item.product_id,

                            quantity:
                                item.quantity

                        };

                    })

                );


            // Submit form
            transactionForm.submit();

        }
    );


    // =========================
    // JALANKAN SAAT AWAL
    // =========================

    renderCart();

});
</script>

@endsection