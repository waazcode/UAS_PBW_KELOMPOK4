<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Peta Laporan') }}
        </h2>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/map-peta.js'])
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4 mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                {{ $isAdmin ? 'Peta Laporan Lingkungan' : 'Peta Laporan Saya' }}
                            </h3>
                            <p class="text-sm text-gray-600">
                                Klik marker untuk melihat detail.
                                @if ($isAdmin)
                                    Menampilkan <span id="marker-count" class="font-semibold">{{ $laporans->count() }}</span> laporan dari semua warga.
                                @else
                                    Menampilkan <span id="marker-count" class="font-semibold">{{ $laporans->count() }}</span> laporan Anda.
                                    <a href="{{ route('laporan.index') }}" class="text-emerald-600 hover:underline">Lihat daftar laporan</a>
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <div>
                                <label for="filter-status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                                <select id="filter-status" class="border-gray-300 rounded-md text-sm shadow-sm">
                                    <option value="">Semua Status</option>
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="filter-kategori" class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                                <select id="filter-kategori" class="border-gray-300 rounded-md text-sm shadow-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (! Auth::user()->isAdmin())
                                <div class="flex items-end">
                                    <a href="{{ route('laporan.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                                        + Buat Laporan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 mb-4 text-xs text-gray-600">
                        <span class="flex items-center gap-1.5">
                            <svg viewBox="0 0 36 48" width="14" height="18"><path d="M18 0C9.716 0 3 6.716 3 15c0 11.25 15 33 15 33s15-21.75 15-33C33 6.716 26.284 0 18 0z" fill="#eab308" stroke="#fff" stroke-width="2"/><circle cx="18" cy="15" r="6" fill="#fff"/></svg>
                            Menunggu
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg viewBox="0 0 36 48" width="14" height="18"><path d="M18 0C9.716 0 3 6.716 3 15c0 11.25 15 33 15 33s15-21.75 15-33C33 6.716 26.284 0 18 0z" fill="#3b82f6" stroke="#fff" stroke-width="2"/><circle cx="18" cy="15" r="6" fill="#fff"/></svg>
                            Proses
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg viewBox="0 0 36 48" width="14" height="18"><path d="M18 0C9.716 0 3 6.716 3 15c0 11.25 15 33 15 33s15-21.75 15-33C33 6.716 26.284 0 18 0z" fill="#22c55e" stroke="#fff" stroke-width="2"/><circle cx="18" cy="15" r="6" fill="#fff"/></svg>
                            Selesai
                        </span>
                    </div>

                    <div
                        id="peta-map"
                        class="w-full rounded-lg border border-gray-200 z-0"
                        style="height: 500px;"
                        data-laporans="{{ $laporans->toJson() }}"
                        data-highlight="{{ $highlightId }}"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
