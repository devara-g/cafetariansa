<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentUser = User::firstOrCreate(
            ['email' => 'siswa.rpl@smkn1ciomas.sch.id'],
            [
                'name' => 'Fajar Pratama (XII RPL 1)',
                'password' => bcrypt('password123'),
                'phone' => '089612345678',
                'role' => 'student',
                'class_or_division' => 'XII RPL 1',
                'nisn_or_nip' => '0061234567',
            ]
        );

        $teacherUser = User::firstOrCreate(
            ['email' => 'guru.tkj@smkn1ciomas.sch.id'],
            [
                'name' => 'Pak Ahmad Maulana, S.Kom',
                'password' => bcrypt('password123'),
                'phone' => '081298765432',
                'role' => 'teacher',
                'class_or_division' => 'Guru Jurusan TKJ & RPL',
                'nisn_or_nip' => '198501012010011002',
            ]
        );

        $menuAyam = Menu::where('slug', 'ayam-crispy-sambal-bawang-nasi')->first();
        $menuEsJeruk = Menu::where('slug', 'es-jeruk-peras-murni')->first();
        $menuNasgor = Menu::where('slug', 'nasi-goreng-spesial-skanic')->first();

        if (! $menuAyam || ! $menuEsJeruk || ! $menuNasgor) {
            return;
        }

        // Order 1: Student order (Paid via QRIS / Midtrans)
        $order1 = Order::updateOrCreate(
            ['order_number' => 'SKN-20260928-0001'],
            [
                'user_id' => $studentUser->id,
                'customer_name' => $studentUser->name,
                'customer_phone' => $studentUser->phone,
                'customer_type' => 'student',
                'pickup_type' => 'pickup_at_stand',
                'delivery_location' => null,
                'pickup_time_slot' => 'Istirahat 1 (09:45 WIB)',
                'notes' => 'Sambal bawang dipisah ya Bu, terima kasih.',
                'subtotal' => 16000,
                'service_fee' => 500,
                'discount_amount' => 0,
                'total_amount' => 16500,
                'order_status' => 'preparing',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'menu_id' => $menuAyam->id],
            [
                'stand_id' => $menuAyam->stand_id,
                'menu_name' => $menuAyam->name,
                'unit_price' => $menuAyam->price,
                'quantity' => 1,
                'subtotal' => $menuAyam->price,
                'notes' => 'Sambal dipisah',
                'item_status' => 'preparing',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'menu_id' => $menuEsJeruk->id],
            [
                'stand_id' => $menuEsJeruk->stand_id,
                'menu_name' => $menuEsJeruk->name,
                'unit_price' => $menuEsJeruk->price,
                'quantity' => 1,
                'subtotal' => $menuEsJeruk->price,
                'notes' => 'Es sedang manis',
                'item_status' => 'ready',
            ]
        );

        Payment::updateOrCreate(
            ['order_id' => $order1->id],
            [
                'payment_reference' => 'PAY-SKN-20260928-QRIS-001',
                'gateway_transaction_id' => 'midtrans-trx-'.Str::uuid(),
                'gateway' => 'midtrans',
                'payment_method' => 'qris',
                'amount' => 16500,
                'fee' => 500,
                'status' => 'paid',
                'paid_at' => now(),
                'gateway_response' => [
                    'transaction_status' => 'settlement',
                    'payment_type' => 'qris',
                    'issuer' => 'gopay',
                    'status_message' => 'Success, transaction is paid',
                ],
            ]
        );

        // Order 2: Teacher order with room delivery
        $order2 = Order::updateOrCreate(
            ['order_number' => 'SKN-20260928-0002'],
            [
                'user_id' => $teacherUser->id,
                'customer_name' => $teacherUser->name,
                'customer_phone' => $teacherUser->phone,
                'customer_type' => 'teacher',
                'pickup_type' => 'deliver_to_room',
                'delivery_location' => 'Ruang Guru Lantai 2 (Meja Pak Ahmad)',
                'pickup_time_slot' => 'Istirahat 2 (12:00 WIB)',
                'notes' => 'Tolong diantar sebelum jam 12:15 WIB untuk makan siang rapat dewan guru.',
                'subtotal' => 20000,
                'service_fee' => 0,
                'discount_amount' => 0,
                'total_amount' => 20000,
                'order_status' => 'confirmed',
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'menu_id' => $menuNasgor->id],
            [
                'stand_id' => $menuNasgor->stand_id,
                'menu_name' => $menuNasgor->name,
                'unit_price' => $menuNasgor->price,
                'quantity' => 2,
                'subtotal' => $menuNasgor->price * 2,
                'notes' => '1 pedas, 1 sedang',
                'item_status' => 'pending',
            ]
        );

        Payment::updateOrCreate(
            ['order_id' => $order2->id],
            [
                'payment_reference' => 'PAY-SKN-20260928-VA-002',
                'gateway_transaction_id' => 'midtrans-trx-'.Str::uuid(),
                'gateway' => 'midtrans',
                'payment_method' => 'bca_va',
                'amount' => 20000,
                'fee' => 0,
                'status' => 'paid',
                'va_number' => '123458901234',
                'paid_at' => now(),
                'gateway_response' => [
                    'transaction_status' => 'settlement',
                    'payment_type' => 'bank_transfer',
                    'bank' => 'bca',
                    'va_number' => '123458901234',
                ],
            ]
        );
    }
}
