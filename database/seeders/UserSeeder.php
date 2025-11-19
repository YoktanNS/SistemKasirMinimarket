<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@kampus.ac.id',
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Admin SmartMart Kampus',
                'role' => 'Admin',
                'status' => 'Aktif',
            ],
            [
                'email' => 'kasir@kampus.ac.id',
                'password' => Hash::make('kasir123'),
                'nama_lengkap' => 'Kasir SmartMart Kampus',
                'role' => 'Kasir',
                'status' => 'Aktif',
            ],
            [
                'email' => 'kepala@kampus.ac.id',
                'password' => Hash::make('kepala123'),
                'nama_lengkap' => 'Kepala Minimarket Kampus',
                'role' => 'Kepala',
                'status' => 'Aktif',
            ],
            [
                'email' => 'supplier@kampus.ac.id',
                'password' => Hash::make('supplier123'),
                'nama_lengkap' => 'Supplier SmartMart Kampus',
                'role' => 'Supplier',
                'status' => 'Aktif',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
