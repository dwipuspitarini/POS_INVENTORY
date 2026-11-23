
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Izinkan nilai mesin dan edc sementara.
        DB::statement("
            ALTER TABLE transactions
            MODIFY metode_pembayaran
            ENUM('tunai', 'qris', 'mesin', 'edc')
            NOT NULL
        ");

        // 2. Ubah data transaksi lama dari mesin menjadi edc.
        DB::table('transactions')
            ->where('metode_pembayaran', 'mesin')
            ->update([
                'metode_pembayaran' => 'edc'
            ]);

        // 3. Hapus pilihan mesin dari ENUM.
        DB::statement("
            ALTER TABLE transactions
            MODIFY metode_pembayaran
            ENUM('tunai', 'qris', 'edc')
            NOT NULL
        ");
    }

    public function down(): void
    {
        // Izinkan kedua nilai sementara.
        DB::statement("
            ALTER TABLE transactions
            MODIFY metode_pembayaran
            ENUM('tunai', 'qris', 'edc', 'mesin')
            NOT NULL
        ");

        // Kembalikan data edc menjadi mesin.
        DB::table('transactions')
            ->where('metode_pembayaran', 'edc')
            ->update([
                'metode_pembayaran' => 'mesin'
            ]);

        // Kembalikan struktur ENUM awal.
        DB::statement("
            ALTER TABLE transactions
            MODIFY metode_pembayaran
            ENUM('tunai', 'qris', 'mesin')
            NOT NULL
        ");
    }
};
