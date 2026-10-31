<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_invoice', 50)->unique();

            $table->foreignId('pelanggan_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();

            $table->foreignId('dibuat_oleh')
                ->constrained('users')
                ->restrictOnDelete();

            $table->dateTime('tanggal_transaksi');

            $table->decimal('total', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_akhir', 15, 2);
            $table->decimal('jumlah_bayar', 15, 2);

            $table->enum('metode_pembayaran', [
                'tunai',
                'qris',
                'mesin'
            ]);

            $table->enum('status_pembayaran', [
                'lunas',
                'dp'
            ]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};