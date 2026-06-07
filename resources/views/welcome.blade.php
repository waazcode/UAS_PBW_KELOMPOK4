<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SafeZone, Platform Pelaporan Lingkungan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-cheviot text-midnight">
    @include('layouts.navigation')

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-midnight via-neptune to-neptune"></div>
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-isotonic rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-pacific rounded-full blur-3xl translate-y-1/3 -translate-x-1/4"></div>
        </div>
        <div class="relative page-container py-20 sm:py-28 lg:py-36">
            <div class="max-w-3xl">
                <span class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold text-isotonic bg-isotonic/10 border border-isotonic/30 rounded-full">
                    Platform Pelaporan Lingkungan
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-cheviot leading-tight mb-6">
                    Bersama Jaga<br>Lingkungan Kita
                </h1>
                <p class="text-lg sm:text-xl text-pacific mb-10 leading-relaxed max-w-2xl">
                    SafeZone memudahkan warga melaporkan masalah lingkungan, dari jalan berlubang hingga lampu mati, dan memantau penanganannya secara transparan.
                </p>
                <div class="flex flex-col sm:flex-row flex-wrap gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn-nav text-base !px-8 !py-3 shadow-lg">
                            Mulai Laporkan
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-cheviot border-2 border-cheviot/40 hover:border-cheviot rounded-xl transition">
                            Masuk
                        </a>
                    @else
                        <a href="{{ route('laporan.create') }}" class="btn-nav text-base !px-8 !py-3 shadow-lg">
                            Buat Laporan
                        </a>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-3 text-base font-bold text-midnight bg-cheviot border-2 border-cheviot rounded-xl hover:bg-white shadow-md transition">
                            Ke Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    {{-- Fitur --}}
    <section class="py-16 sm:py-24">
        <div class="page-container">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-2xl sm:text-3xl font-bold text-midnight mb-4">Bagaimana SafeZone Bekerja?</h2>
                <p class="text-neptune/80 max-w-2xl mx-auto">Tiga langkah sederhana untuk melaporkan dan memantau masalah di lingkungan sekitar Anda.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="card p-6 sm:p-8 hover:shadow-md transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-neptune text-isotonic rounded-xl mb-5 text-xl font-bold">1</div>
                    <h3 class="text-xl font-semibold text-midnight mb-3">Buat Laporan</h3>
                    <p class="text-neptune/70">Daftar akun dan kirim laporan masalah lingkungan beserta foto bukti di lokasi kejadian.</p>
                </div>
                <div class="card-pacific p-6 sm:p-8 hover:shadow-md transition">
                    <div class="w-12 h-12 flex items-center justify-center bg-midnight text-isotonic rounded-xl mb-5 text-xl font-bold">2</div>
                    <h3 class="text-xl font-semibold text-midnight mb-3">Pantau Status</h3>
                    <p class="text-neptune/70">Lacak perkembangan laporan Anda mulai dari <em>menunggu</em>, <em>proses</em>, hingga <em>selesai</em>.</p>
                </div>
                <div class="card p-6 sm:p-8 hover:shadow-md transition sm:col-span-2 lg:col-span-1">
                    <div class="w-12 h-12 flex items-center justify-center bg-isotonic text-midnight rounded-xl mb-5 text-xl font-bold">3</div>
                    <h3 class="text-xl font-semibold text-midnight mb-3">Admin Tangani</h3>
                    <p class="text-neptune/70">Tim admin meninjau dan memperbarui status setiap laporan agar masalah segera ditindaklanjuti.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Status preview --}}
    <section class="py-12 sm:py-16 bg-pacific/20">
        <div class="page-container text-center">
            <h2 class="text-2xl font-bold text-midnight mb-8">Status Laporan</h2>
            <div class="flex flex-wrap justify-center gap-4">
                <x-status-badge status="menunggu" class="!text-sm !px-5 !py-2" />
                <x-status-badge status="proses" class="!text-sm !px-5 !py-2" />
                <x-status-badge status="selesai" class="!text-sm !px-5 !py-2" />
            </div>
        </div>
    </section>

    {{-- CTA --}}
    @guest
    <section class="py-16 sm:py-20 bg-midnight">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-cheviot mb-4">Siap Berkontribusi?</h2>
            <p class="text-pacific mb-8">Gabung SafeZone dan bantu jadikan lingkungan sekitar lebih aman dan nyaman.</p>
            <a href="{{ route('register') }}" class="btn-nav text-base !px-8 !py-3 shadow-lg">
                Daftar Sekarang, Gratis
            </a>
        </div>
    </section>
    @endguest

    <footer class="bg-midnight text-grape-mist py-8 border-t border-neptune/50">
        <div class="page-container flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <x-application-logo class="w-6 h-6 text-isotonic" />
                <span class="font-semibold text-cheviot">SafeZone</span>
            </div>
            <p class="text-sm">&copy; {{ date('Y') }} SafeZone, UAS PBW Kelompok 4</p>
        </div>
    </footer>
</body>
</html>
