<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::where('email', 'admin@caballero.com')->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin Caballero',
            'email' => 'admin@caballero.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
        ]);
    }
}
