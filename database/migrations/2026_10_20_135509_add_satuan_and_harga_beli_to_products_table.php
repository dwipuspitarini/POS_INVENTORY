<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('satuan', 30)->after('nama');
            $table->decimal('harga_beli', 15, 2)
                ->default(0)
                ->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'harga_beli']);
        });
    }
};