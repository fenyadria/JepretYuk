<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Membuat 1 akun Fotografer/Admin permanen.
     * Menggunakan firstOrCreate agar tidak duplikat saat re-seed.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@jepretyuk.com'],
            [
                'name'     => 'Admin JepretYuk',
                'password' => Hash::make('password'),
                'role'     => 'fotografer',
            ]
        );
    }
}
