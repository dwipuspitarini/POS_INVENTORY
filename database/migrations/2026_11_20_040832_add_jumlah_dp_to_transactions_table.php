
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('jumlah_dp', 15, 2)
                ->default(0)
                ->after('jumlah_bayar');
        });

        // Salin nominal pembayaran awal untuk transaksi
        // lama yang masih berstatus DP.
        DB::table('transactions')
            ->where('status_pembayaran', 'dp')
            ->update([
                'jumlah_dp' => DB::raw('jumlah_bayar')
            ]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('jumlah_dp');
        });
    }
};
