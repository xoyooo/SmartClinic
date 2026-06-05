<?php
namespace Database\Seeders;

use App\Models\{User, Dokter};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@smartclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => 'active',
            'no_hp'    => '081200000000',
        ]);

        $d1 = User::create([
            'name'     => 'dr. Andi Pratama',
            'email'    => 'andi@smartclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'dokter',
            'status'   => 'active',
            'no_hp'    => '081200000001',
        ]);
        Dokter::create([
            'user_id'   => $d1->id,
            'spesialis' => 'Dokter Umum',
        ]);

        $d2 = User::create([
            'name'     => 'drg. Sari Dewi',
            'email'    => 'sari@smartclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'dokter',
            'status'   => 'active',
            'no_hp'    => '081200000002',
        ]);
        Dokter::create([
            'user_id'   => $d2->id,
            'spesialis' => 'Dokter Gigi',
        ]);
    }
}