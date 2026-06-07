<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Komentar;
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

        $laporan1 = Laporan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategoris[0]->id,
            'judul' => 'Lampu jalan mati',
            'deskripsi' => 'Lampu jalan di depan kampus tidak menyala sejak 2 hari lalu.',
            'alamat' => 'Jl. Teuku Umar No. 1, Banda Aceh',
            'latitude' => 5.5483,
            'longitude' => 95.3238,
            'status' => 'menunggu',
        ]);

        Komentar::create([
            'laporan_id' => $laporan1->id,
            'user_id' => $admin->id,
            'isi' => 'Terima kasih atas laporannya. Tim kami akan segera mengecek lokasi tersebut.',
        ]);

        Laporan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategoris[0]->id,
            'judul' => 'Jalan berlubang',
            'deskripsi' => 'Ada lubang besar di jalan utama yang berbahaya untuk pengendara.',
            'alamat' => 'Jl. Muhammad Hasan, Banda Aceh',
            'latitude' => 5.5521,
            'longitude' => 95.3172,
            'status' => 'proses',
        ]);

        Laporan::create([
            'user_id' => $user->id,
            'kategori_id' => $kategoris[1]->id,
            'judul' => 'Sampah menumpuk',
            'deskripsi' => 'Tumpukan sampah di trotoar tidak diangkut selama seminggu.',
            'alamat' => 'Jl. Soekarno-Hatta, Banda Aceh',
            'latitude' => 5.5412,
            'longitude' => 95.3315,
            'status' => 'selesai',
        ]);
    }
}
