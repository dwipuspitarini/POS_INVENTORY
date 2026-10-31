@extends('layouts.app')

@section('content')

<div class="container">

    {{-- =========================
        HEADER
    ========================== --}}
    <div class="page-header">

        <div>
            <h1>Tambah Stok</h1>

            <p>
                Tambahkan beberapa produk dari satu supplier sekaligus.
            </p>
        </div>

        <a
            href="{{ route('stocks.index') }}"
            class="btn btn-secondary"
        >
            Kembali
        </a>

    </div>


    {{-- =========================
        INFORMASI STOK
    ========================== --}}
    <div class="form-card mb-20">

        <div class="section-title">
            Informasi Stok
        </div>


        <div class="stock-info-grid">

            {{-- SUPPLIER --}}
            <div class="form-group">

                <label
                    for="supplier-search"
                    class="form-label"
                >
                    Supplier <span class="required">*</span>
                </label>

                <div class="supplier-search-wrapper">

                    <input
                        type="text"
                        id="supplier-search"
                        class="form-control"
                        placeholder="Ketik atau pilih supplier..."
                        autocomplete="off"
                    >

                    <input
                        type="hidden"
                        id="supplier-id"
                    >

                    <div
                        id="supplier-results"
                        class="supplier-results"
                    >

                        @foreach($suppliers as $supplier)

                            <div
                                class="supplier-result-item"
                                data-id="{{ $supplier->id }}"
                                data-name="{{ $supplier->nama }}"
                            >
                                {{ $supplier->nama }}
                            </div>

                        @endforeach

                    </div>

                </div>

                <small
                    id="supplier-error"
                    class="form-error"
                ></small>

            </div>


            {{-- TANGGAL STOK --}}
            <div class="form-group">

                <label
                    for="tanggal_stok"
                    class="form-label"
                >
                    Tanggal Stok <span class="required">*</span>
                </label>

                <input
                    type="datetime-local"
                    id="tanggal_stok"
                    class="form-control"
                    value="{{ now()->format('Y-m-d\TH:i') }}"
                >

                <small class="form-help">
                    Otomatis menggunakan tanggal dan waktu sekarang.
                </small>

            </div>

        </div>

    </div>


    {{-- =========================
        PILIH PRODUK
    ========================== --}}
    <div class="form-card mb-20">

        <div class="section-title">
            Tambahkan Produk
        </div>


        <div class="form-group">

            <label
                for="product-search"
                class="form-label"
            >
                Produk
            </label>


            <div class="product-search-wrapper">

                <div class="product-search-box">

                    <span class="search-icon">
                        🔍
                    </span>

                    <input
                        type="text"
                        id="product-search"
                        class="form-control"
                        placeholder="Ketik nama produk..."
                        autocomplete="off"
                    >

                </div>


                <div
                    id="product-results"
                    class="product-results"
                ></div>

            </div>

        </div>

    </div>


    {{-- =========================
        TABEL PRODUK
    ========================== --}}
    <div class="table-card mb-20">

        <div class="table-card-header">

            <h2>
                Produk yang Akan Ditambahkan
            </h2>

        </div>


        <div class="table-wrapper">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Produk</th>

                        <th>Kategori</th>

                        <th>Stok Saat Ini</th>

                        <th>Jumlah Masuk</th>

                        <th>Stok Setelah</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody id="stock-cart-body">

                    <tr>

                        <td
                            colspan="7"
                            class="empty-cart"
                        >
                            Belum ada produk yang dipilih.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================
        KETERANGAN
    ========================== --}}
    <div class="form-card">

        <div class="section-title">
            Keterangan
        </div>


        <div class="form-group">

            <label
                for="keterangan"
                class="form-label"
            >
                Keterangan
            </label>

            <textarea
                id="keterangan"
                class="form-control"
                rows="4"
                placeholder="Contoh: Stok barang datang dari supplier."
            ></textarea>

        </div>


        <div class="form-actions">

            <a
                href="{{ route('stocks.index') }}"
                class="btn btn-secondary"
            >
                Batal
            </a>

            <button
                type="button"
                id="save-stock"
                class="btn btn-primary"
            >
                Simpan Stok
            </button>

        </div>

    </div>

</div>


{{-- =========================
    FORM TERSEMBUNYI
========================== --}}
<form
    action="{{ route('owner.stocks.store') }}"
    method="POST"
    id="stock-form"
    style="display: none;"
>

    @csrf

    <input
        type="hidden"
        name="supplier_id"
        id="hidden-supplier-id"
    >

    <input
        type="hidden"
        name="tanggal_stok"
        id="hidden-tanggal-stok"
    >

    <input
        type="hidden"
        name="keterangan"
        id="hidden-keterangan"
    >

    <div id="hidden-items"></div>

</form>


@push('styles')

<style>

/* =========================================================
   REQUIRED
========================================================= */

.required {
    color: #dc3545;
}


/* =========================================================
   STOCK INFORMATION
========================================================= */

.stock-info-grid {

    display: grid;

    grid-template-columns:
        1fr
        1fr;

    gap: 25px;

}


/* =========================================================
   SUPPLIER SEARCH
========================================================= */

.supplier-search-wrapper {

    position: relative;

}


.supplier-results {

    position: absolute;

    top: calc(100% + 5px);

    left: 0;

    right: 0;

    background: #ffffff;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    box-shadow:
        0 8px 20px
        rgba(0, 0, 0, 0.10);

    max-height: 220px;

    overflow-y: auto;

    z-index: 200;

    display: none;

}


.supplier-result-item {

    padding: 12px 15px;

    cursor: pointer;

    border-bottom:
        1px solid #eeeeee;

}


.supplier-result-item:last-child {

    border-bottom: none;

}


.supplier-result-item:hover {

    background: #f3f6fb;

}


.form-help {

    display: block;

    margin-top: 6px;

    font-size: 13px;

    color: #6b7280;

}


/* =========================================================
   PRODUCT SEARCH
========================================================= */

.product-search-wrapper {

    position: relative;

}


.product-search-box {

    position: relative;

}


.product-search-box .form-control {

    padding-left: 45px;

}


.search-icon {

    position: absolute;

    left: 15px;

    top: 50%;

    transform:
        translateY(-50%);

    font-size: 17px;

    z-index: 2;

}


.product-results {

    position: absolute;

    top: calc(100% + 5px);

    left: 0;

    right: 0;

    background: #ffffff;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    box-shadow:
        0 8px 20px
        rgba(0, 0, 0, 0.10);

    max-height: 300px;

    overflow-y: auto;

    z-index: 100;

    display: none;

}


.product-result-item {

    padding: 13px 15px;

    cursor: pointer;

    border-bottom:
        1px solid #eeeeee;

}


.product-result-item:last-child {

    border-bottom: none;

}


.product-result-item:hover {

    background: #f3f6fb;

}


.product-result-name {

    font-weight: 600;

    color: #1f2937;

    margin-bottom: 4px;

}


.product-result-info {

    font-size: 13px;

    color: #6b7280;

}


.no-product-result {

    padding: 15px;

    text-align: center;

    color: #6b7280;

}


/* =========================================================
   TABLE
========================================================= */

.empty-cart {

    text-align: center;

    padding: 35px !important;

    color: #6b7280;

}


.stock-quantity-input {

    width: 100px;

    min-width: 80px;

    text-align: center;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 800px) {

    .stock-info-grid {

        grid-template-columns: 1fr;

        gap: 0;

    }


    .table-wrapper {

        overflow-x: auto;

    }


    .data-table {

        min-width: 900px;

    }

}

</style>

@endpush


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | ELEMENT
    |--------------------------------------------------------------------------
    */

    const supplierSearch =
        document.getElementById('supplier-search');

    const supplierId =
        document.getElementById('supplier-id');

    const supplierResults =
        document.getElementById('supplier-results');

    const supplierError =
        document.getElementById('supplier-error');


    const productSearch =
        document.getElementById('product-search');

    const productResults =
        document.getElementById('product-results');


    const stockCartBody =
        document.getElementById('stock-cart-body');


    const tanggalStok =
        document.getElementById('tanggal_stok');


    const keterangan =
        document.getElementById('keterangan');


    const saveButton =
        document.getElementById('save-stock');


    const stockForm =
        document.getElementById('stock-form');


    const hiddenItems =
        document.getElementById('hidden-items');


    const hiddenSupplierId =
        document.getElementById('hidden-supplier-id');


    const hiddenTanggalStok =
        document.getElementById('hidden-tanggal-stok');


    const hiddenKeterangan =
        document.getElementById('hidden-keterangan');


    /*
    |--------------------------------------------------------------------------
    | DATA PRODUK
    |--------------------------------------------------------------------------
    */

    const products = @json($productData);


    /*
    |--------------------------------------------------------------------------
    | CART
    |--------------------------------------------------------------------------
    */

    let cart = [];


    /*
    |--------------------------------------------------------------------------
    | SUPPLIER SEARCH
    |--------------------------------------------------------------------------
    */

    supplierSearch.addEventListener(
        'focus',
        function () {

            showSupplierResults(
                this.value
            );

        }
    );


    supplierSearch.addEventListener(
        'input',
        function () {

            supplierId.value = '';

            supplierError.textContent = '';

            showSupplierResults(
                this.value
            );

        }
    );


    function showSupplierResults(keyword) {

        const search =
            keyword
                .toLowerCase()
                .trim();


        const items =
            supplierResults
                .querySelectorAll(
                    '.supplier-result-item'
                );


        let found = false;


        items.forEach(item => {

            const name =
                item.dataset.name
                    .toLowerCase();


            if (
                search === '' ||
                name.includes(search)
            ) {

                item.style.display =
                    'block';

                found = true;

            } else {

                item.style.display =
                    'none';

            }

        });


        supplierResults.style.display =
            found
                ? 'block'
                : 'none';

    }


    /*
    |--------------------------------------------------------------------------
    | PILIH SUPPLIER
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '.supplier-result-item'
        )
        .forEach(item => {

            item.addEventListener(
                'click',
                function () {

                    supplierId.value =
                        this.dataset.id;

                    supplierSearch.value =
                        this.dataset.name;

                    supplierError.textContent =
                        '';

                    supplierResults.style.display =
                        'none';

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | SEARCH PRODUK
    |--------------------------------------------------------------------------
    */

    productSearch.addEventListener(
        'input',
        function () {

            const keyword =
                this.value
                    .toLowerCase()
                    .trim();


            productResults.innerHTML = '';


            if (keyword === '') {

                productResults.style.display =
                    'none';

                return;

            }


            const results =
                products.filter(product => {

                    const alreadyAdded =
                        cart.some(
                            item =>
                                item.product_id ==
                                product.id
                        );


                    if (alreadyAdded) {

                        return false;

                    }


                    return (
                        product.nama
                            .toLowerCase()
                            .includes(keyword)

                        ||

                        product.kategori
                            .toLowerCase()
                            .includes(keyword)
                    );

                });


            if (results.length === 0) {

                productResults.innerHTML = `

                    <div class="no-product-result">
                        Produk tidak ditemukan.
                    </div>

                `;

                productResults.style.display =
                    'block';

                return;

            }


            results.forEach(product => {

                const item =
                    document.createElement(
                        'div'
                    );


                item.className =
                    'product-result-item';


                item.innerHTML = `

                    <div class="product-result-name">

                        ${escapeHtml(
                            product.nama
                        )}

                    </div>


                    <div class="product-result-info">

                        ${escapeHtml(
                            product.kategori
                        )}

                        ·

                        Stok:
                        ${product.stok}

                    </div>

                `;


                item.addEventListener(
                    'click',
                    function () {

                        addProduct(
                            product
                        );

                    }
                );


                productResults.appendChild(
                    item
                );

            });


            productResults.style.display =
                'block';

        }
    );


    /*
    |--------------------------------------------------------------------------
    | TAMBAH PRODUK
    |--------------------------------------------------------------------------
    */

    function addProduct(product) {

        const existing =
            cart.find(
                item =>
                    item.product_id ==
                    product.id
            );


        if (existing) {

            existing.jumlah += 1;

        } else {

            cart.push({

                product_id:
                    product.id,

                nama:
                    product.nama,

                kategori:
                    product.kategori,

                stok:
                    Number(
                        product.stok
                    ),

                jumlah:
                    1

            });

        }


        productSearch.value = '';

        productResults.style.display =
            'none';


        renderCart();

    }


    /*
    |--------------------------------------------------------------------------
    | RENDER CART
    |--------------------------------------------------------------------------
    */

    function renderCart() {

        stockCartBody.innerHTML = '';


        if (cart.length === 0) {

            stockCartBody.innerHTML = `

                <tr>

                    <td
                        colspan="7"
                        class="empty-cart"
                    >
                        Belum ada produk yang dipilih.
                    </td>

                </tr>

            `;

            return;

        }


        cart.forEach(
            (item, index) => {

                const row =
                    document.createElement(
                        'tr'
                    );


                row.innerHTML = `

                    <td>
                        ${index + 1}
                    </td>


                    <td>

                        <strong>
                            ${escapeHtml(
                                item.nama
                            )}
                        </strong>

                    </td>


                    <td>

                        ${escapeHtml(
                            item.kategori
                        )}

                    </td>


                    <td>

                        ${item.stok}

                    </td>


                    <td>

                        <input

                            type="number"

                            min="1"

                            class="form-control stock-quantity-input"

                            value="${item.jumlah}"

                            data-index="${index}"

                        >

                    </td>


                    <td>

                        <strong>
                            ${item.stok + item.jumlah}
                        </strong>

                    </td>


                    <td>

                        <button

                            type="button"

                            class="btn btn-danger btn-sm remove-product"

                            data-index="${index}"

                        >
                            Hapus
                        </button>

                    </td>

                `;


                stockCartBody.appendChild(
                    row
                );

            }
        );


        bindCartEvents();

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT CART
    |--------------------------------------------------------------------------
    */

    function bindCartEvents() {


        /*
        | JUMLAH
        */

        document
            .querySelectorAll(
                '.stock-quantity-input'
            )
            .forEach(input => {

                input.addEventListener(
                    'input',
                    function () {

                        const index =
                            Number(
                                this.dataset.index
                            );


                        let value =
                            parseInt(
                                this.value
                            );


                        if (
                            isNaN(value) ||
                            value < 1
                        ) {

                            value = 1;

                        }


                        cart[index].jumlah =
                            value;


                        renderCart();

                    }
                );

            });


        /*
        | HAPUS
        */

        document
            .querySelectorAll(
                '.remove-product'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    function () {

                        const index =
                            Number(
                                this.dataset.index
                            );


                        cart.splice(
                            index,
                            1
                        );


                        renderCart();

                    }
                );

            });

    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN
    |--------------------------------------------------------------------------
    */

    saveButton.addEventListener(
        'click',
        function () {


            /*
            | Cek supplier
            */

            if (!supplierId.value) {

                supplierError.textContent =
                    'Supplier harus dipilih.';

                supplierSearch.focus();

                return;

            }


            /*
            | Cek tanggal
            */

            if (!tanggalStok.value) {

                alert(
                    'Tanggal stok harus diisi.'
                );

                tanggalStok.focus();

                return;

            }


            /*
            | Cek produk
            */

            if (cart.length === 0) {

                alert(
                    'Silakan pilih minimal satu produk.'
                );

                productSearch.focus();

                return;

            }


            /*
            | Bersihkan hidden input
            */

            hiddenItems.innerHTML = '';


            /*
            | Supplier
            */

            hiddenSupplierId.value =
                supplierId.value;


            /*
            | Tanggal
            */

            hiddenTanggalStok.value =
                tanggalStok.value;


            /*
            | Keterangan
            */

            hiddenKeterangan.value =
                keterangan.value;


            /*
            | Produk
            */

            cart.forEach(
                (item, index) => {

                    hiddenItems.insertAdjacentHTML(
                        'beforeend',
                        `

                            <input
                                type="hidden"
                                name="items[${index}][product_id]"
                                value="${item.product_id}"
                            >

                            <input
                                type="hidden"
                                name="items[${index}][jumlah]"
                                value="${item.jumlah}"
                            >

                        `
                    );

                }
            );


            stockForm.submit();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | KLIK DI LUAR SEARCH
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {


            if (
                !event.target.closest(
                    '.supplier-search-wrapper'
                )
            ) {

                supplierResults.style.display =
                    'none';

            }


            if (
                !event.target.closest(
                    '.product-search-wrapper'
                )
            ) {

                productResults.style.display =
                    'none';

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | ESCAPE HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return String(value)

            .replace(
                /&/g,
                '&amp;'
            )

            .replace(
                /</g,
                '&lt;'
            )

            .replace(
                />/g,
                '&gt;'
            )

            .replace(
                /"/g,
                '&quot;'
            )

            .replace(
                /'/g,
                '&#039;'
            );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL
    |--------------------------------------------------------------------------
    */

    renderCart();

});

</script>

@endpush

@endsection