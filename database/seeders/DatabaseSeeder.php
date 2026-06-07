<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin SafeZone',
            'email' => 'admin@safezone.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'name' => 'User SafeZone',
            'email' => 'user@safezone.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $kategoris = collect([
            'Infrastruktur',
            'Kebersihan',
            'Keamanan',
            'Lainnya',
        ])->map(fn (string $nama) => Kategori::create(['nama' => $nama]));

        Laporan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategoris[0]->id,
            'judul' => 'Lampu jalan mati',
            'deskripsi' => 'Lampu jalan di depan kampus tidak menyala sejak 2 hari lalu.',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'status' => 'menunggu',
        ]);

        Laporan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategoris[0]->id,
            'judul' => 'Jalan berlubang',
            'deskripsi' => 'Ada lubang besar di jalan utama yang berbahaya untuk pengendara.',
            'latitude' => -6.2146,
            'longitude' => 106.8451,
            'status' => 'proses',
        ]);

        Laporan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategoris[1]->id,
            'judul' => 'Sampah menumpuk',
            'deskripsi' => 'Tumpukan sampah di trotoar tidak diangkut selama seminggu.',
            'latitude' => -6.2012,
            'longitude' => 106.8385,
            'status' => 'selesai',
        ]);
    }
}
