<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PaketFoto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Membuat 1 akun Fotografer/Admin permanen dan banyak pilihan paket foto.
     */
    public function run(): void
    {
        // 1. Akun Admin Fotografer
        User::firstOrCreate(
            ['email' => 'admin@jepretyuk.com'],
            [
                'name'     => 'Admin JepretYuk',
                'password' => Hash::make('password'),
                'role'     => 'fotografer',
            ]
        );

        // 2. Berbagai Pilihan Paket Foto
        $pakets = [
            [
                'nama_paket' => 'Paket Wisuda Silver (Studio)',
                'deskripsi'  => 'Sesi foto wisuda indoor 30 menit, 1 kostum toga, 5 file edit resolusi tinggi, cetak 10R + bingkai minimalis.',
                'harga'      => 250000,
            ],
            [
                'nama_paket' => 'Paket Wisuda Platinum (Family & Friends)',
                'deskripsi'  => 'Sesi foto wisuda 60 menit, bebas foto bersama keluarga & sahabat (maks. 8 orang), 15 file edit, semua file mentah di Google Drive, 2 cetak 10R + bingkai.',
                'harga'      => 550000,
            ],
            [
                'nama_paket' => 'Paket Prewedding Romantic Outdoor',
                'deskripsi'  => 'Sesi foto prewedding outdoor 3 jam di 1 lokasi pilihan, 2 pergantian konsep baju, 20 foto edit warna & retouch, cetak kanvas 16R.',
                'harga'      => 1500000,
            ],
            [
                'nama_paket' => 'Paket Wedding Akad & Resepsi',
                'deskripsi'  => 'Dokumentasi penuh hari H pernikahan (hingga 6 jam), 1 fotografer utama + 1 asisten, unlimited shoot, 50 foto retouch album kolase magnetik, cetak kanvas 20R.',
                'harga'      => 3500000,
            ],
            [
                'nama_paket' => 'Paket Foto Keluarga Harmoni',
                'deskripsi'  => 'Sesi foto studio keluarga hingga 6 orang, durasi 45 menit, 10 foto edit profesional, cetak 12R eksklusif.',
                'harga'      => 400000,
            ],
            [
                'nama_paket' => 'Paket Personal Portrait & CV / LinkedIn',
                'deskripsi'  => 'Foto formal & semi-formal untuk profil profesional, LinkedIn, dan CV. 20 menit sesi studio, 3 hasil edit retouch wajah terbaik.',
                'harga'      => 150000,
            ],
            [
                'nama_paket' => 'Paket Sweet Seventeen / Birthday Party',
                'deskripsi'  => 'Dokumentasi perayaan ulang tahun 3 jam, candid shot, foto bareng tamu, semua file diserahkan + 25 file edit khusus.',
                'harga'      => 850000,
            ],
            [
                'nama_paket' => 'Paket Baby & Maternity Studio',
                'deskripsi'  => 'Sesi foto kehamilan atau newborn/balita 45 menit dengan properti lucu & steril, 8 foto edit istimewa, cetak 10R.',
                'harga'      => 450000,
            ],
            [
                'nama_paket' => 'Paket Foto Produk & Katalog UMKM',
                'deskripsi'  => 'Foto studio produk komersil (makanan, fashion, atau barang), hingga 10 produk berbeda dengan background studio bersih, 20 file edit siap katalog marketplace.',
                'harga'      => 600000,
            ],
            [
                'nama_paket' => 'Paket Bestie / Group Fun Shoot',
                'deskripsi'  => 'Sesi foto seru bareng sahabat/geng (maks. 6 orang), tema kasual/vintage, durasi 45 menit, 12 foto edit warna aesthetic.',
                'harga'      => 300000,
            ],
        ];

        foreach ($pakets as $paket) {
            PaketFoto::firstOrCreate(
                ['nama_paket' => $paket['nama_paket']],
                [
                    'deskripsi' => $paket['deskripsi'],
                    'harga'     => $paket['harga'],
                    'foto'      => null,
                ]
            );
        }
    }
}
