<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-midnight">
                    {{ __('Daftar Laporan') }}
                </h2>
                <p class="text-sm text-neptune/70 mt-1">Kelola dan pantau semua laporan Anda</p>
            </div>
            <a href="{{ route('laporan.create') }}" class="btn-primary shrink-0">
                + Buat Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="page-container">
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($laporans->isEmpty())
                <div class="card p-10 sm:p-16 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-pacific/40 rounded-2xl">
                        <svg class="w-8 h-8 text-neptune" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-midnight mb-2">Belum ada laporan</h3>
                    <p class="text-neptune/70 mb-6">Mulai laporkan masalah di lingkungan sekitar Anda.</p>
                    <a href="{{ route('laporan.create') }}" class="btn-primary">
                        Buat Laporan Pertama
                    </a>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                    @foreach ($laporans as $laporan)
                        <article class="card group hover:shadow-md hover:border-neptune/30 transition flex flex-col">
                            <div class="p-5 sm:p-6 flex-1 flex flex-col">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <span class="text-xs font-medium text-neptune/60 bg-pacific/30 px-2.5 py-1 rounded-lg">
                                        {{ $laporan->kategori->nama_tampilan }}
                                    </span>
                                    <x-status-badge :status="$laporan->status" />
                                </div>

                                <h3 class="text-lg font-bold text-midnight group-hover:text-neptune transition line-clamp-2">
                                    {{ $laporan->judul }}
                                </h3>

                                <p class="text-sm text-neptune/70 mt-2 line-clamp-3 flex-1">
                                    {{ $laporan->deskripsi }}
                                </p>

                                @if ($laporan->alamat)
                                    <p class="text-xs text-grape-mist mt-3 flex items-start gap-1.5">
                                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ Str::limit($laporan->alamat_tampilan, 50) }}
                                    </p>
                                @endif

                                <p class="text-xs text-grape-mist mt-2">
                                    {{ $laporan->created_at->format('d M Y') }}
                                </p>
                            </div>

                            <div class="px-5 sm:px-6 py-4 bg-cheviot/50 border-t border-grape-mist/30 flex flex-wrap gap-2">
                                <a href="{{ route('laporan.show', $laporan) }}" class="btn-secondary btn-sm">
                                    Lihat Detail
                                </a>
                                @if ($laporan->latitude && $laporan->longitude)
                                    <a href="{{ route('laporan.peta', ['laporan' => $laporan->id]) }}" class="btn-outline btn-sm">
                                        Lihat di Peta
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
