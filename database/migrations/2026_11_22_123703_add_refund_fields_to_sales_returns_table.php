
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->decimal('jumlah_pengembalian', 15, 2)
                ->nullable()
                ->after('tanggal_retur');

            $table->enum('metode_pengembalian', ['tunai', 'qris', 'edc'])
                ->nullable()
                ->after('jumlah_pengembalian');
        });
    }

    public function down(): void
    {
        Schema::table('sales_returns', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_pengembalian',
                'metode_pengembalian',
            ]);
        });
    }
};
