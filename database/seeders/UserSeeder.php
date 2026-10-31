<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Owner',
            'username' => 'owner',
            'password' => 'owner123',
            'role' => 'owner',
        ]);

        User::create([
            'name' => 'Kasir',
            'username' => 'kasir',
            'password' => 'kasir123',
            'role' => 'kasir',
        ]);
    }
}