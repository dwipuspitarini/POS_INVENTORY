
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->string('kode', 30)->nullable()->unique()->after('id');
        });

        DB::table('stock_opnames')
            ->orderBy('id')
            ->get(['id', 'tanggal_opname'])
            ->each(function ($opname) {
                $tanggal = date(
                    'Ymd',
                    strtotime($opname->tanggal_opname)
                );

                $kode = 'OPN-' . $tanggal . '-' .
                    str_pad(
                        (string) $opname->id,
                        4,
                        '0',
                        STR_PAD_LEFT
                    );

                DB::table('stock_opnames')
                    ->where('id', $opname->id)
                    ->update(['kode' => $kode]);
            });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropUnique(['kode']);
            $table->dropColumn('kode');
        });
    }
};
