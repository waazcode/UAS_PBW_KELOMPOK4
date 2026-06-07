<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-midnight">
                {{ __('Dashboard') }}
            </h2>
            <p class="text-sm text-neptune/70 mt-1">Selamat datang di SafeZone</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="page-container space-y-6">
            <div class="card p-6 sm:p-8">
                <h3 class="text-lg sm:text-xl font-bold text-midnight mb-2">Halo, {{ Auth::user()->name }}!</h3>
                <p class="text-neptune/70">
                    Anda login sebagai
                    <span class="font-semibold {{ Auth::user()->isAdmin() ? 'text-neptune' : 'text-midnight' }}">
                        {{ Auth::user()->isAdmin() ? 'Admin' : 'Pengguna' }}
                    </span>
                    di platform SafeZone.
                </p>
            </div>

            @if (Auth::user()->isAdmin())
                <div class="card-pacific p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-midnight mb-2">Panel Admin</h3>
                    <p class="text-neptune/70 mb-5">Kelola dan perbarui status laporan dari warga.</p>
                    <a href="{{ route('admin.laporan.index') }}" class="btn-secondary">
                        Kelola Laporan
                    </a>
                </div>
            @else
                <div class="card p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-midnight mb-2">Laporkan Masalah</h3>
                    <p class="text-neptune/70 mb-5">Laporkan masalah di lingkungan Anda dengan melampirkan foto.</p>
                    <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                        <a href="{{ route('laporan.create') }}" class="btn-primary justify-center">
                            Buat Laporan
                        </a>
                        <a href="{{ route('laporan.peta') }}" class="btn-outline justify-center">
                            Lihat Peta
                        </a>
                        <a href="{{ route('laporan.index') }}" class="btn-outline justify-center">
                            Laporan Saya
                        </a>
                    </div>
                </div>
            @endif

            <div class="card p-6 sm:p-8">
                <h3 class="text-lg font-bold text-midnight mb-2">Peta Laporan</h3>
                <p class="text-neptune/70 mb-5">Lihat semua laporan di peta interaktif. Filter berdasarkan status dan kategori.</p>
                <a href="{{ route('laporan.peta') }}" class="btn-secondary">
                    Buka Peta
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
