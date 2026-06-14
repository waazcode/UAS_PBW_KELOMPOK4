# SafeZone — Sistem Pelaporan Fasilitas & Lingkungan

SafeZone adalah aplikasi web berbasis Laravel yang memungkinkan masyarakat melaporkan masalah fasilitas umum dan lingkungan secara langsung. Setiap laporan dilengkapi dengan informasi lokasi yang ditampilkan di peta interaktif, sehingga pihak terkait dapat memantau dan menindaklanjuti laporan dengan lebih mudah.

---

## Tujuan Pengembangan

Aplikasi ini dikembangkan sebagai proyek UAS mata kuliah Pemrograman Berbasis Web (PBW) dengan tujuan:

- Memberikan platform bagi masyarakat untuk melaporkan kerusakan fasilitas dan masalah lingkungan secara digital
- Mempermudah admin/pihak berwenang dalam memantau dan memperbarui status penanganan laporan
- Menampilkan sebaran laporan secara visual melalui peta interaktif berbasis lokasi nyata

---

## Fitur Utama

**Untuk pengguna umum:**
- Registrasi dan login akun
- Membuat laporan baru lengkap dengan judul, deskripsi, kategori, foto, dan titik lokasi
- Autocomplete pencarian alamat menggunakan Nominatim (OpenStreetMap)
- Menentukan lokasi laporan melalui klik peta atau deteksi GPS otomatis
- Melihat daftar semua laporan beserta statusnya
- Melihat detail laporan dan memberikan komentar
- Melihat sebaran laporan pada peta interaktif dengan filter kategori
- Mengelola profil akun (nama, email, password)

**Untuk admin:**
- Dashboard khusus admin untuk melihat seluruh laporan
- Memperbarui status laporan: Menunggu → Proses → Selesai

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Framework utama | Laravel 13 (PHP 8.3) |
| Autentikasi | Laravel Breeze |
| Frontend styling | Tailwind CSS v3 |
| Komponen interaktif | Alpine.js |
| Build tool | Vite |
| Peta interaktif | Leaflet.js 1.9.4 |
| Geocoding / reverse geocoding | Nominatim API (OpenStreetMap) |
| Database | MySQL |
| Template engine | Blade |

---

## Struktur Database

### Tabel users
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | string | Nama pengguna |
| email | string | Email (unik) |
| password | string | Password terenkripsi |
| role | enum | admin atau user |
| created_at / updated_at | timestamp | |

### Tabel kategoris
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| nama | string | Nama kategori (Infrastruktur, Kebersihan, Keamanan, Lainnya) |

### Tabel laporans
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | Foreign key ke users |
| kategori_id | bigint | Foreign key ke kategoris |
| judul | string | Judul laporan |
| deskripsi | text | Deskripsi detail masalah |
| foto | string | Path file foto (opsional) |
| status | enum | menunggu, proses, selesai |
| latitude | decimal(10,8) | Koordinat lintang lokasi |
| longitude | decimal(11,8) | Koordinat bujur lokasi |
| alamat | string | Alamat tekstual lokasi |

### Tabel komentars
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| laporan_id | bigint | Foreign key ke laporans |
| user_id | bigint | Foreign key ke users |
| isi | text | Isi komentar |

---

## Panduan Instalasi

### Prasyarat
- PHP 8.3+
- Composer
- Node.js & npm
- MySQL

### Cara 1 — Otomatis (Windows)

Cukup jalankan script yang sudah disediakan:

```bash
# Jalankan setup (install dependency, migrate, seed, build assets)
setup.bat

# Setelah setup selesai, jalankan server
serve.bat
```

Akses aplikasi di: `http://127.0.0.1:8000`

### Cara 2 — Manual

**1. Clone/ekstrak repositori**
```bash
git clone <url-repo>
cd UAS_PBW_KELOMPOK4
```

**2. Install dependency PHP**
```bash
composer install
```

**3. Salin file konfigurasi dan generate key**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi database di file `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safezone
DB_USERNAME=root
DB_PASSWORD=
```

**5. Jalankan migrasi dan seeder**
```bash
php artisan migrate --seed
```

**6. Buat symlink storage (untuk upload foto)**
```bash
php artisan storage:link
```

**7. Install dependency frontend dan build assets**
```bash
npm install
npm run build
```

**8. Jalankan server development**
```bash
php artisan serve
```

Akses aplikasi di: `http://127.0.0.1:8000`

### Akun Default (dari seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@safezone.com | password |
| User | user@safezone.com | password |

---

## Screenshot

### 1. Halaman Welcome
> Tampilan awal aplikasi sebelum login.
<img width="1890" height="902" alt="image" src="https://github.com/user-attachments/assets/8d3f4125-8a20-4690-9121-1582e9dff64b" />

---

### 2. Halaman Register & Login
> Form registrasi akun baru dan halaman login.
<img width="1897" height="905" alt="image" src="https://github.com/user-attachments/assets/822afaa7-46bc-4090-a765-90fcf94d79a0" />
<img width="1918" height="908" alt="image" src="https://github.com/user-attachments/assets/cbc0b183-cf45-4bad-a3cc-0c3b5541743b" />

---

### 3. Dashboard
> Halaman utama setelah pengguna berhasil login.
<img width="1897" height="906" alt="image" src="https://github.com/user-attachments/assets/1ff75dc1-dda6-4db1-89fc-7051045bffad" />

---

### 4. Daftar Laporan
> Menampilkan semua laporan yang masuk beserta status penanganannya.
<img width="1901" height="900" alt="image" src="https://github.com/user-attachments/assets/08661541-ce50-47e9-b257-46d4d7f736bb" />

---

### 5. Buat Laporan
> Form pengisian laporan baru, dilengkapi map picker untuk menentukan lokasi.
<img width="1897" height="911" alt="image" src="https://github.com/user-attachments/assets/53d4599a-862f-4a94-b186-f50e6b357bf2" />

> Halaman detail laporan lengkap dengan foto, lokasi, dan kolom komentar.
<img width="1901" height="901" alt="image" src="https://github.com/user-attachments/assets/ada7e529-aa7e-431a-9cca-fc1da40e8d2e" />

---

### 6. Peta Interaktif
> Sebaran semua laporan ditampilkan dalam peta dengan filter kategori.
<img width="1896" height="905" alt="image" src="https://github.com/user-attachments/assets/35d1ac92-860d-4303-9617-4be7d0900ca8" />

---

### 7. Dashboard Admin
> Tampilan khusus admin untuk memantau dan memperbarui status laporan.
<img width="1896" height="906" alt="image" src="https://github.com/user-attachments/assets/ec09b326-eaba-4f5b-888d-030b8a27f689" />

---

### 8. Panel Kelola Admin
> Untuk peninjauan foto, komentar, dan perbarui status laporan warga
<img width="1898" height="911" alt="image" src="https://github.com/user-attachments/assets/dd5f299f-e6f3-42fb-8734-50d36eadb156" />

> Lihat detail, dan pengupdate an status oleh admin
<img width="1901" height="911" alt="image" src="https://github.com/user-attachments/assets/3336424f-45a9-4590-80ad-30bc891366c1" />

