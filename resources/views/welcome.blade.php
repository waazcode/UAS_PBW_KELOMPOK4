<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SafeZone') }} — Platform Pelaporan Lingkungan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    {{-- Navbar --}}
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center gap-2">
                    <x-application-logo class="w-8 h-8 text-emerald-600" />
                    <span class="font-bold text-xl text-emerald-700">SafeZone</span>
                </a>
                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-emerald-700 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-emerald-700 transition">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-teal-700"></div>
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="max-w-3xl">
                <span class="inline-block px-3 py-1 mb-6 text-sm font-medium text-emerald-100 bg-white/20 rounded-full backdrop-blur-sm">
                    Platform Pelaporan Lingkungan
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                    Bersama Jaga<br>Lingkungan Kita
                </h1>
                <p class="text-lg sm:text-xl text-emerald-100 mb-10 leading-relaxed">
                    SafeZone memudahkan warga melaporkan masalah lingkungan — dari jalan berlubang hingga lampu mati — dan memantau penanganannya secara transparan.
                </p>
                <div class="flex flex-wrap gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="px-6 py-3 text-base font-semibold text-emerald-700 bg-white hover:bg-emerald-50 rounded-lg shadow-lg transition">
                            Mulai Laporkan
                        </a>
                        <a href="{{ route('login') }}" class="px-6 py-3 text-base font-semibold text-white border-2 border-white/60 hover:border-white rounded-lg transition">
                            Masuk
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 text-base font-semibold text-emerald-700 bg-white hover:bg-emerald-50 rounded-lg shadow-lg transition">
                            Ke Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    {{-- Fitur --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Bagaimana SafeZone Bekerja?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Tiga langkah sederhana untuk melaporkan dan memantau masalah di lingkungan sekitar Anda.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-emerald-50 border border-emerald-100">
                    <div class="w-12 h-12 flex items-center justify-center bg-emerald-600 text-white rounded-xl mb-5 text-xl font-bold">1</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Buat Laporan</h3>
                    <p class="text-gray-600">Daftar akun dan kirim laporan masalah lingkungan beserta foto bukti di lokasi kejadian.</p>
                </div>
                <div class="p-8 rounded-2xl bg-blue-50 border border-blue-100">
                    <div class="w-12 h-12 flex items-center justify-center bg-blue-600 text-white rounded-xl mb-5 text-xl font-bold">2</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Pantau Status</h3>
                    <p class="text-gray-600">Lacak perkembangan laporan Anda — mulai dari <em>menunggu</em>, <em>proses</em>, hingga <em>selesai</em>.</p>
                </div>
                <div class="p-8 rounded-2xl bg-teal-50 border border-teal-100">
                    <div class="w-12 h-12 flex items-center justify-center bg-teal-600 text-white rounded-xl mb-5 text-xl font-bold">3</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Admin Tangani</h3>
                    <p class="text-gray-600">Tim admin meninjau dan memperbarui status setiap laporan agar masalah segera ditindaklanjuti.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Status badge preview --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Status Laporan</h2>
            <div class="flex flex-wrap justify-center gap-4">
                <span class="px-4 py-2 text-sm font-medium bg-yellow-100 text-yellow-800 rounded-full">Menunggu</span>
                <span class="px-4 py-2 text-sm font-medium bg-blue-100 text-blue-800 rounded-full">Proses</span>
                <span class="px-4 py-2 text-sm font-medium bg-green-100 text-green-800 rounded-full">Selesai</span>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    @guest
    <section class="py-20 bg-emerald-700">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Siap Berkontribusi?</h2>
            <p class="text-emerald-100 mb-8">Gabung SafeZone dan bantu jadikan lingkungan sekitar lebih aman dan nyaman.</p>
            <a href="{{ route('register') }}" class="inline-block px-8 py-3 text-base font-semibold text-emerald-700 bg-white hover:bg-emerald-50 rounded-lg shadow-lg transition">
                Daftar Sekarang — Gratis
            </a>
        </div>
    </section>
    @endguest

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <x-application-logo class="w-6 h-6 text-emerald-500" />
                <span class="font-semibold text-white">SafeZone</span>
            </div>
            <p class="text-sm">&copy; {{ date('Y') }} SafeZone — UAS PBW Kelompok 4</p>
        </div>
    </footer>
</body>
</html>
