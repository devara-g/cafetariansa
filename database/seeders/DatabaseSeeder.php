<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin Kantin SKANIC
        User::firstOrCreate(
            ['email' => 'admin@smkn1ciomas.sch.id'],
            [
                'name' => 'Admin Kantin SMKN 1 Ciomas',
                'password' => Hash::make('admin123'),
                'phone' => '081234567890',
                'role' => 'admin',
                'class_or_division' => 'Pengelola Kantin Sekolah',
                'nisn_or_nip' => '198001012005011001',
            ]
        );

        // 2. Call Seeders
        $this->call([
            CategorySeeder::class,
            StandSeeder::class,
            MenuSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
