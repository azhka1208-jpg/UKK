<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Sistem',
                'email' => 'admin@sistema.test',
                'password' => Hash::make('admin123'),
                'phone' => '081200000001',
                'address' => 'Ruang Administrasi, Gedung Utama',
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'petugas'],
            [
                'name' => 'Petugas Inventaris',
                'email' => 'petugas@sistema.test',
                'password' => Hash::make('petugas123'),
                'phone' => '081200000002',
                'address' => 'Gudang Inventaris',
                'role' => User::ROLE_PETUGAS,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'peminjam'],
            [
                'name' => 'Rina Peminjam',
                'email' => 'peminjam@sistema.test',
                'password' => Hash::make('peminjam123'),
                'phone' => '081200000003',
                'address' => 'Jl. Pendidikan No. 10',
                'role' => User::ROLE_PEMINJAM,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['username' => 'budi'],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@sistema.test',
                'password' => Hash::make('peminjam123'),
                'phone' => '081200000004',
                'address' => 'Jl. Melati No. 22',
                'role' => User::ROLE_PEMINJAM,
                'is_active' => true,
            ]
        );
    }
}