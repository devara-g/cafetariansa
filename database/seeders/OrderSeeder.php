<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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

    }
}
