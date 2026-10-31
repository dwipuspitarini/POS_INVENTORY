<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom SKU sementara
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 50)
                ->nullable()
                ->after('id');
        });

        // Ambil semua produk lama
        $products = DB::table('products')
            ->join('categories', 'products.kategori_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.kategori_id',
                'categories.nama as kategori_nama'
            )
            ->orderBy('products.id')
            ->get();

        // Buat SKU numerik berurutan untuk produk lama
$counter = 1;

foreach ($products as $product) {
    $sku = str_pad(
        $counter,
        3,
        '0',
        STR_PAD_LEFT
    );

    DB::table('products')
        ->where('id', $product->id)
        ->update([
            'sku' => $sku,
        ]);

    $counter++;
}

        // Jadikan SKU wajib diisi dan unik
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 50)
                ->nullable(false)
                ->unique()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->dropColumn('sku');
        });
    }
};