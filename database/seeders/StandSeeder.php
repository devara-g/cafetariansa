<?php

namespace Database\Seeders;

use App\Models\Stand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bu Siti (Stand 1)
        $ownerSiti = User::firstOrCreate(
            ['email' => 'busiti@kantin.skanic.sch.id'],
            [
                'name' => 'Ibu Siti Khodijah',
                'password' => Hash::make('password123'),
                'phone' => '081234567801',
                'role' => 'stand_owner',
                'class_or_division' => 'Pengelola Stand 01',
            ]
        );

        // 2. Pak Joko (Stand Utama)
        $ownerJoko = User::firstOrCreate(
            ['email' => 'pakjoko@kantin.skanic.sch.id'],
            [
                'name' => 'Pak Joko Susilo',
                'password' => Hash::make('password123'),
                'phone' => '081234567802',
                'role' => 'stand_owner',
                'class_or_division' => 'Pengelola Stand Utama',
            ]
        );

        // 3. Teh Linda (Stand Minuman)
        $ownerLinda = User::firstOrCreate(
            ['email' => 'tehlinda@kantin.skanic.sch.id'],
            [
                'name' => 'Teh Linda Permata',
                'password' => Hash::make('password123'),
                'phone' => '081234567803',
                'role' => 'stand_owner',
                'class_or_division' => 'Pengelola Stand Minuman',
            ]
        );

        $stands = [
            [
                'user_id' => $ownerSiti->id,
                'name' => 'Stand 1 - Dapur Bu Siti',
                'slug' => 'stand-1-dapur-bu-siti',
                'stand_number' => 'Stand 01',
                'phone' => '081234567801',
                'description' => 'Ayam Geprek sambal bawang, seblak ceker komplit, cireng rujak, soto ayam, dan paket hemat pelajar.',
                'image' => 'assets/images/food-menu-1.png',
                'is_open' => true,
                'opening_time' => '06:30:00',
                'closing_time' => '15:30:00',
            ],
            [
                'user_id' => $ownerJoko->id,
                'name' => 'Stand Utama - Masakan Nusantara & Jajanan',
                'slug' => 'stand-utama-nusantara',
                'stand_number' => 'Stand Utama',
                'phone' => '081234567802',
                'description' => 'Takoyaki sosis keju, pangsit kuah pedas jebew, gurilem pedas Ciomas, cimol bojot, mie jebew, dan nasi goreng.',
                'image' => 'assets/images/food-menu-2.png',
                'is_open' => true,
                'opening_time' => '06:30:00',
                'closing_time' => '16:00:00',
            ],
            [
                'user_id' => $ownerLinda->id,
                'name' => 'Stand Minuman Segar & Pop Ice SKANIC',
                'slug' => 'stand-minuman-segar-skanic',
                'stand_number' => 'Stand 03',
                'phone' => '081234567803',
                'description' => 'Pop Ice blender aneka rasa topping meses & keju, es teh manis jumbo 22oz, es jeruk peras asli, dan es nutrisari dingin.',
                'image' => 'assets/images/food-menu-3.png',
                'is_open' => true,
                'opening_time' => '06:30:00',
                'closing_time' => '16:00:00',
            ],
        ];

        foreach ($stands as $stand) {
            Stand::updateOrCreate(['slug' => $stand['slug']], $stand);
        }
    }
}
