<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Selamat datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600">
                        Anda login sebagai
                        <span class="font-medium {{ Auth::user()->isAdmin() ? 'text-emerald-700' : 'text-blue-700' }}">
                            {{ Auth::user()->isAdmin() ? 'Admin' : 'Pengguna' }}
                        </span>
                        di platform SafeZone.
                    </p>
                </div>
            </div>

            @if (Auth::user()->isAdmin())
                <div class="bg-emerald-50 border border-emerald-200 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-emerald-800 mb-2">Panel Admin</h3>
                        <p class="text-emerald-700 mb-4">Kelola dan perbarui status laporan dari warga.</p>
                        <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                            Kelola Laporan
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-blue-50 border border-blue-200 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Laporkan Masalah</h3>
                        <p class="text-blue-700">Fitur buat laporan akan tersedia di tahap berikutnya. Nantikan update dari tim!</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
