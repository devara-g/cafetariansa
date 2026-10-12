<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan Berat',
                'slug' => 'makanan-berat',
                'icon' => 'restaurant-outline',
                'description' => 'Aneka menu nasi, ayam, mi, dan hidangan mengenyangkan untuk makan siang.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Minuman Segar',
                'slug' => 'minuman-segar',
                'icon' => 'water-outline',
                'description' => 'Minuman dingin, es teh jumbo, jus buah segar, dan aneka minuman kekinian.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Jajanan & Snack',
                'slug' => 'jajanan-snack',
                'icon' => 'pizza-outline',
                'description' => 'Camilan ringan, gorengan higienis, roti bakar, dan pastry untuk istirahat singkat.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Paket Hemat Pelajar',
                'slug' => 'paket-hemat-pelajar',
                'icon' => 'flash-outline',
                'description' => 'Menu hemat ramah kantong siswa SMKN 1 Ciomas lengkap dengan minuman.',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
