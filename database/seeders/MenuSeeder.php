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
            // ==========================================
            // JAJANAN & SNACK (Camilan Favorit Anak Sekolah)
            // ==========================================
            [
                'stand_id' => $stand2->id,
                'category_id' => $catSnack->id,
                'name' => 'Kerupuk Gurilem Pedas Khas Ciomas',
                'slug' => 'kerupuk-gurilem-pedas-khas-ciomas',
                'description' => 'Kerupuk gurilem renyah dengan taburan bumbu cabai rawit pedas gurih beraroma daun jeruk khas Ciomas, nagih untuk cemilan kelas.',
                'price' => 5000,
                'cost_price' => 3000,
                'image' => 'assets/images/promo-3.png',
                'stock' => 60,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 2,
            ],
            [
                'stand_id' => $stand1->id,
                'category_id' => $catSnack->id,
                'name' => 'Seblak Komplit Prasmanan SKANIC',
                'slug' => 'seblak-komplit-prasmanan-skanic',
                'description' => 'Seblak kuah kencur pedas merah merona dengan isian kerupuk basah kenyal, makaroni, ceker empuk, bakso, sosis, dan sawi hijau.',
                'price' => 10000,
                'cost_price' => 6500,
                'image' => 'assets/images/food-menu-4.png',
                'stock' => 60,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 6,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catSnack->id,
                'name' => 'Pangsit Kuah Pedas Jebew',
                'slug' => 'pangsit-kuah-pedas-jebew',
                'description' => 'Pangsit rebus lembut isi olahan daging ayam gurih disiram kuah pedas gurih harum daun jeruk dan taburan bawang goreng renyah.',
                'price' => 10000,
                'cost_price' => 6500,
                'image' => 'assets/images/food-menu-2.png',
                'stock' => 50,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 5,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catSnack->id,
                'name' => 'Takoyaki Sosis Keju Lumer (5 pcs)',
                'slug' => 'takoyaki-sosis-keju-lumer-5-pcs',
                'description' => 'Bola-bola takoyaki lembut isi potongan sosis sapi dan keju lumer, ditaburi saus takoyaki manis gurih, mayones, dan serutan katsuobushi.',
                'price' => 8000,
                'cost_price' => 5000,
                'image' => 'assets/images/promo-4.png',
                'stock' => 50,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 5,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catSnack->id,
                'name' => 'Cimol Bojot Bumbu Bawang Pedas',
                'slug' => 'cimol-bojot-bumbu-bawang-pedas',
                'description' => 'Cimol kenyal empuk disiram racikan minyak bawang gurih wangi dan taburan bubuk cabai rawit pedas khas Garut.',
                'price' => 5000,
                'cost_price' => 3000,
                'image' => 'assets/images/promo-3.png',
                'stock' => 50,
                'is_available' => true,
                'is_popular' => false,
                'is_recommended' => false,
                'preparation_time_minutes' => 3,
            ],
            [
                'stand_id' => $stand1->id,
                'category_id' => $catSnack->id,
                'name' => 'Cireng Crispy Bumbu Rujak',
                'slug' => 'cireng-crispy-bumbu-rujak',
                'description' => 'Cireng montok renyah di luar kenyal di dalam, disajikan hangat lengkap dengan cocolan sambal rujak gula merah pedas manis gurih.',
                'price' => 6000,
                'cost_price' => 3500,
                'image' => 'assets/images/promo-3.png',
                'stock' => 40,
                'is_available' => true,
                'is_popular' => false,
                'is_recommended' => false,
                'preparation_time_minutes' => 4,
            ],

            // ==========================================
            // MAKANAN BERAT (Makan Siang Mengenyangkan)
            // ==========================================
            [
                'stand_id' => $stand1->id,
                'category_id' => $catFood->id,
                'name' => 'Nasi Ayam Geprek Sambal Bawang',
                'slug' => 'nasi-ayam-geprek-sambal-bawang',
                'description' => 'Ayam goreng krispi renyah digeprek dengan sambal bawang segar pedas nampol disajikan hangat bersama nasi pulen dan lalapan timun.',
                'price' => 12000,
                'cost_price' => 8000,
                'image' => 'assets/images/food-menu-1.png',
                'stock' => 80,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 5,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catFood->id,
                'name' => 'Mie Jebew Pedas Mampus SKANIC',
                'slug' => 'mie-jebew-pedas-mampus-skanic',
                'description' => 'Mie kenyal berbalut racikan minyak cabai rawit pedas membakar, cincangan ayam kecap gurih, dan kerupuk pangsit renyah.',
                'price' => 10000,
                'cost_price' => 6500,
                'image' => 'assets/images/promo-2.png',
                'stock' => 60,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 5,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catFood->id,
                'name' => 'Nasi Goreng Spesial Telur Ceplok',
                'slug' => 'nasi-goreng-spesial-telur-ceplok',
                'description' => 'Nasi goreng harum racikan bumbu khas kantin dengan suwiran ayam, telur mata sapi, acar timun segar, dan kerupuk renyah.',
                'price' => 10000,
                'cost_price' => 6500,
                'image' => 'assets/images/promo-1.png',
                'stock' => 70,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 6,
            ],
            [
                'stand_id' => $stand1->id,
                'category_id' => $catFood->id,
                'name' => 'Soto Ayam Lamongan Kuah Bening',
                'slug' => 'soto-ayam-lamongan-kuah-bening',
                'description' => 'Soto ayam hangat kuah bening rempah harum dilengkapi suwiran ayam, soun, koya gurih, dan irisan jeruk nipis segar.',
                'price' => 10000,
                'cost_price' => 7000,
                'image' => 'assets/images/food-menu-4.png',
                'stock' => 40,
                'is_available' => true,
                'is_popular' => false,
                'is_recommended' => true,
                'preparation_time_minutes' => 5,
            ],

            // ==========================================
            // MINUMAN SEGAR & POP ICE BLENDER
            // ==========================================
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Pop Ice Blender Coklat Topping Meses',
                'slug' => 'pop-ice-blender-coklat-topping-meses',
                'description' => 'Pop Ice rasa coklat kental diblender dingin dengan es serut, susu kental manis coklat, dan taburan meses melimpah.',
                'price' => 5000,
                'cost_price' => 2500,
                'image' => 'assets/images/food-menu-6.png',
                'stock' => 100,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 3,
            ],
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Pop Ice Blender Taro Topping Keju',
                'slug' => 'pop-ice-blender-taro-topping-keju',
                'description' => 'Pop Ice rasa taro ungu manis lembut creamy diblender dingin dengan topping taburan parutan keju cheddar gurih melimpah.',
                'price' => 6000,
                'cost_price' => 3000,
                'image' => 'assets/images/food-menu-6.png',
                'stock' => 80,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 3,
            ],
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Pop Ice Permen Karet Topping Boba',
                'slug' => 'pop-ice-permen-karet-topping-boba',
                'description' => 'Minuman Pop Ice rasa bubble gum warna biru ceria favorit anak sekolahan dengan topping boba tapioka kenyal manis brown sugar.',
                'price' => 6000,
                'cost_price' => 3000,
                'image' => 'assets/images/food-menu-5.png',
                'stock' => 80,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 3,
            ],
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Es Teh Manis Jumbo SKANIC (22oz)',
                'slug' => 'es-teh-manis-jumbo-skanic',
                'description' => 'Seduhan teh melati wangi khas Solo disajikan dingin menyegarkan dalam cup besar 22oz dengan gula tebu asli pelepas dahaga.',
                'price' => 3000,
                'cost_price' => 1200,
                'image' => 'assets/images/food-menu-5.png',
                'stock' => 200,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 2,
            ],
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Es Jeruk Peras Murni Segar',
                'slug' => 'es-jeruk-peras-murni-segar',
                'description' => 'Jeruk peras asli manis alami kaya vitamin C disajikan dingin dengan es batu kristal menyegarkan.',
                'price' => 4000,
                'cost_price' => 2500,
                'image' => 'assets/images/food-menu-3.png',
                'stock' => 100,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => false,
                'preparation_time_minutes' => 3,
            ],
            [
                'stand_id' => $stand3->id,
                'category_id' => $catDrink->id,
                'name' => 'Es Nutrisari Dingin Segar',
                'slug' => 'es-nutrisari-dingin-segar',
                'description' => 'Nutrisari dingin manis segar sari buah jeruk kaya vitamin C, pilihan praktis dan favorit siswa saat istirahat olahraga.',
                'price' => 3000,
                'cost_price' => 1500,
                'image' => 'assets/images/food-menu-3.png',
                'stock' => 100,
                'is_available' => true,
                'is_popular' => false,
                'is_recommended' => false,
                'preparation_time_minutes' => 2,
            ],

            // ==========================================
            // PAKET HEMAT PELAJAR
            // ==========================================
            [
                'stand_id' => $stand1->id,
                'category_id' => $catPackage->id,
                'name' => 'Paket Hemat: Seblak Komplit + Es Teh Jumbo',
                'slug' => 'paket-hemat-seblak-komplit-es-teh-jumbo',
                'description' => 'Paket combo terlaris: Seblak kuah ceker komplit pedas + 1 cup Es Teh Manis Jumbo segar. Pas di kantong, kenyang maksimal!',
                'price' => 12000,
                'cost_price' => 7500,
                'image' => 'assets/images/food-menu-1.png',
                'stock' => 50,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 6,
            ],
            [
                'stand_id' => $stand1->id,
                'category_id' => $catPackage->id,
                'name' => 'Paket Kenyang: Nasi Ayam Geprek + Pop Ice Blender',
                'slug' => 'paket-kenyang-nasi-ayam-geprek-pop-ice-blender',
                'description' => 'Paket makan siang juara: Nasi Ayam Geprek sambal bawang pedas hangat + Pop Ice Blender pilihan aneka rasa manis dingin.',
                'price' => 15000,
                'cost_price' => 10000,
                'image' => 'assets/images/promo-5.png',
                'stock' => 50,
                'is_available' => true,
                'is_popular' => true,
                'is_recommended' => true,
                'preparation_time_minutes' => 6,
            ],
            [
                'stand_id' => $stand2->id,
                'category_id' => $catPackage->id,
                'name' => 'Paket Santai: Takoyaki + Pop Ice Taro Keju',
                'slug' => 'paket-santai-takoyaki-pop-ice-taro-keju',
                'description' => 'Paket jajan istirahat kedua: 1 porsi Takoyaki sosis keju (5 pcs) + 1 cup Pop Ice Taro keju parut dingin segar.',
                'price' => 13000,
                'cost_price' => 8000,
                'image' => 'assets/images/promo-4.png',
                'stock' => 40,
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
