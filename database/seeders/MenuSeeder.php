<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Stand;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stand1 = Stand::where('slug', 'stand-1-dapur-bu-siti')->first();
        $stand2 = Stand::where('slug', 'stand-utama-nusantara')->first();
        $stand3 = Stand::where('slug', 'stand-minuman-segar-skanic')->first();

        $catFood = Category::where('slug', 'makanan-berat')->first();
        $catDrink = Category::where('slug', 'minuman-segar')->first();
        $catSnack = Category::where('slug', 'jajanan-snack')->first();
        $catPackage = Category::where('slug', 'paket-hemat-pelajar')->first();

        if (! $stand1 || ! $stand2 || ! $stand3 || ! $catFood) {
            return;
        }

        $menus = [
            // Stand 1: Bu Siti
            [
                'stand_id' => $stand1->id,
                'category_id' => $catFood->id,
                'name' => 'Ayam Crispy Sambal Bawang + Nasi',
                'slug' => 'ayam-crispy-sambal-bawang-nasi',
                'description' => 'Ayam goreng renyah bumbu kuning dipadukan sambal bawang pedas segar dan nasi pulen hangat.',
                'price' => 12000,
                'cost_price' => 8500,
                'image' => 'assets/images/food-menu-1.png',
                'stock' => 80,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 5,
            ],
            [
                'stand_id' => $stand1->id,
                'category_id' => $catFood->id,
                'name' => 'Soto Ayam Lamongan SKANIC',
                'slug' => 'soto-ayam-lamongan-skanic',
                'description' => 'Kuah soto bening rempah harum dengan suwiran ayam, soun, koya gurih, dan kerupuk.',
                'price' => 10000,
                'cost_price' => 7000,
                'image' => 'assets/images/food-menu-4.png',
                'stock' => 50,
                'is_available' => true,
                'is_popular' => false,
                'is_recommended' => true,
                'preparation_time_minutes' => 7,
            ],

            // Stand 2: Stand Utama
            [
                'stand_id' => $stand2->id,
                'category_id' => $catFood->id,
                'name' => 'Nasi Goreng Spesial SKANIC',
                'slug' => 'nasi-goreng-spesial-skanic',
                'description' => 'Nasi goreng harum dengan telur mata sapi, suwiran ayam, acar, dan kerupuk renyah.',
                'price' => 10000,
                'cost_price' => 6500,
                'image' => 'assets/images/promo-1.png',
                'stock' => 90,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 8,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catFood->id,
                'name' => 'Mie Ayam Bakso Pangsit',
                'slug' => 'mie-ayam-bakso-pangsit',
                'description' => 'Mie kenyal gurih dengan cincangan ayam manis gurih, 2 butir bakso sapi, dan pangsit goreng.',
                'price' => 12000,
                'cost_price' => 8000,
                'image' => 'assets/images/food-menu-2.png',
                'stock' => 60,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 6,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catSnack->id,
                'name' => 'Burger Mini Daging Sapi',
                'slug' => 'burger-mini-daging-sapi',
                'description' => 'Roti bun lembut dengan patty daging sapi panggang, selada segar, keju leleh, dan saus bolognese.',
                'price' => 12000,
                'cost_price' => 8000,
                'image' => 'assets/images/hero-banner.png',
                'stock' => 40,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 5,
            ],

            // Stand 3: Stand Minuman
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Es Jeruk Peras Murni',
                'slug' => 'es-jeruk-peras-murni',
                'description' => 'Jeruk peras asli manis alami kaya vitamin C disajikan dingin menyegarkan.',
                'price' => 4000,
                'cost_price' => 2500,
                'image' => 'assets/images/food-menu-3.png',
                'stock' => 150,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 3,
            ],
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Es Teh Manis Jumbo',
                'slug' => 'es-teh-manis-jumbo',
                'description' => 'Seduhan teh melati wangi dengan gula tebu asli dalam gelas ukuran besar 22oz.',
                'price' => 3000,
                'cost_price' => 1200,
                'image' => 'assets/images/food-menu-5.png',
                'stock' => 200,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 2,
            ],
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Jus Alpukat Cokelat',
                'slug' => 'jus-alpukat-cokelat',
                'description' => 'Alpukat mentega kental dengan susu kental manis cokelat premium.',
                'price' => 8000,
                'cost_price' => 5000,
                'image' => 'assets/images/food-menu-6.png',
                'stock' => 50,
                'is_available' => true,
                'is_popular' => false,
                'is_recommended' => true,
                'preparation_time_minutes' => 4,
            ],

            // Paket Hemat Pelajar
            [
                'stand_id' => $stand1->id,
                'category_id' => $catPackage->id,
                'name' => 'Paket Komplit SKANIC Juara',
                'slug' => 'paket-komplit-skanic-juara',
                'description' => 'Paket hemat: Ayam Crispy + Nasi Hangat + Es Teh Manis Jumbo. Hemat dan kenyang!',
                'price' => 14000,
                'cost_price' => 9700,
                'image' => 'assets/images/food-menu-1.png',
                'stock' => 60,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 5,
            ],
        ];

        foreach ($menus as $m) {
            Menu::updateOrCreate(['slug' => $m['slug']], $m);
        }
    }
}
