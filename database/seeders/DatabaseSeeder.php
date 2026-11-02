<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/kasir.sql');
        if (File::exists($path)) {
            // Matikan foreign key check sementara agar tidak error saat eksekusi dump
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            DB::unprepared(File::get($path));
            
            // Nyalakan kembali foreign key check
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}