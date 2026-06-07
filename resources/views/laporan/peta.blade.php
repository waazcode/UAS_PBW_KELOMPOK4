<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-midnight">
                {{ __('Peta Laporan') }}
            </h2>
            <p class="text-sm text-neptune/70 mt-1">Lihat lokasi laporan di peta interaktif</p>
        </div>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/map-peta.js'])
    @endpush

    <div class="py-8 sm:py-12">
        <div class="page-container space-y-4">
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col lg:flex-row lg:items-end gap-4 mb-5">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-midnight mb-1">
                                {{ $isAdmin ? 'Peta Laporan Lingkungan' : 'Peta Laporan Saya' }}
                            </h3>
                            <p class="text-sm text-neptune/70">
                                Klik marker untuk melihat detail.
                                @if ($isAdmin)
                                    Menampilkan <span id="marker-count" class="font-semibold text-midnight">{{ $laporans->count() }}</span> laporan dari semua warga.
                                @else
                                    Menampilkan <span id="marker-count" class="font-semibold text-midnight">{{ $laporans->count() }}</span> laporan Anda.
                                    <a href="{{ route('laporan.index') }}" class="btn-outline btn-sm">Lihat Daftar</a>
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row flex-wrap gap-3">
                            <div>
                                <label for="filter-status" class="block text-xs font-semibold text-neptune mb-1">Status</label>
                                <select id="filter-status" class="form-select !mt-0 text-sm min-w-[11rem]">
                                    <option value="">Semua Status</option>
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="filter-kategori" class="block text-xs font-semibold text-neptune mb-1">Kategori</label>
                                <select id="filter-kategori" class="form-select !mt-0 text-sm min-w-[11rem]">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama_tampilan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (! Auth::user()->isAdmin())
                                <div class="flex items-end">
                                    <a href="{{ route('laporan.create') }}" class="btn-primary !text-sm">
                                        + Buat Laporan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 mb-4 text-xs text-neptune/70">
                        <span class="flex items-center gap-1.5">
                            <svg viewBox="0 0 36 48" width="14" height="18"><path d="M18 0C9.716 0 3 6.716 3 15c0 11.25 15 33 15 33s15-21.75 15-33C33 6.716 26.284 0 18 0z" fill="#C0D6EA" stroke="#11425D" stroke-width="2"/><circle cx="18" cy="15" r="6" fill="#11425D"/></svg>
                            Menunggu
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg viewBox="0 0 36 48" width="14" height="18"><path d="M18 0C9.716 0 3 6.716 3 15c0 11.25 15 33 15 33s15-21.75 15-33C33 6.716 26.284 0 18 0z" fill="#11425D" stroke="#fff" stroke-width="2"/><circle cx="18" cy="15" r="6" fill="#DDFF55"/></svg>
                            Proses
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg viewBox="0 0 36 48" width="14" height="18"><path d="M18 0C9.716 0 3 6.716 3 15c0 11.25 15 33 15 33s15-21.75 15-33C33 6.716 26.284 0 18 0z" fill="#DDFF55" stroke="#002233" stroke-width="2"/><circle cx="18" cy="15" r="6" fill="#002233"/></svg>
                            Selesai
                        </span>
                    </div>

                    <div
                        id="peta-map"
                        class="w-full rounded-xl border-2 border-pacific/60 z-0"
                        style="height: 400px; min-height: 300px;"
                        data-laporans="{{ $laporans->toJson() }}"
                        data-highlight="{{ $highlightId }}"
                    ></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
