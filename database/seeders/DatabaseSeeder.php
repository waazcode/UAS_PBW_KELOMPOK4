<?php

namespace Database\Seeders;

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

        Laporan::create([
            'user_id' => $user->id,
            'judul' => 'Lampu jalan mati',
            'deskripsi' => 'Lampu jalan di depan kampus tidak menyala sejak 2 hari lalu.',
            'status' => 'menunggu',
        ]);

        Laporan::create([
            'user_id' => $user->id,
            'judul' => 'Jalan berlubang',
            'deskripsi' => 'Ada lubang besar di jalan utama yang berbahaya untuk pengendara.',
            'status' => 'proses',
        ]);
    }
}
