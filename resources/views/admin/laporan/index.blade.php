<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-midnight">
                {{ __('Kelola Laporan') }}
            </h2>
            <p class="text-sm text-neptune/70 mt-1">Tinjau foto, komentar, dan perbarui status laporan warga</p>
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
                <div class="card p-10 text-center text-grape-mist">
                    Belum ada laporan.
                </div>
            @else
                <div class="grid gap-5 sm:gap-6">
                    @foreach ($laporans as $laporan)
                        <article class="card overflow-hidden">
                            <div class="flex flex-col md:flex-row">
                                <div class="md:w-48 lg:w-56 shrink-0 bg-cheviot border-b md:border-b-0 md:border-r border-grape-mist/30">
                                    @if ($laporan->foto_url)
                                        <img
                                            src="{{ $laporan->foto_url }}"
                                            alt="Foto {{ $laporan->judul }}"
                                            class="w-full h-40 md:h-full object-cover"
                                        >
                                    @else
                                        <div class="w-full h-40 md:h-full min-h-[10rem] flex items-center justify-center bg-pacific/20">
                                            <span class="text-xs text-grape-mist text-center px-4">Tidak ada foto</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-5 sm:p-6 flex-1 flex flex-col gap-4">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <x-status-badge :status="$laporan->status" />
                                            <span class="text-xs text-neptune bg-pacific/30 px-2 py-1 rounded-lg">{{ $laporan->kategori->nama_tampilan }}</span>
                                            @if ($laporan->komentars_count > 0)
                                                <span class="text-xs text-neptune bg-cheviot px-2 py-1 rounded-lg border border-grape-mist/40">
                                                    {{ $laporan->komentars_count }} komentar
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-lg font-bold text-midnight">{{ $laporan->judul }}</h3>
                                        @if ($laporan->deskripsi)
                                            <p class="text-sm text-neptune/70 mt-1">{{ Str::limit($laporan->deskripsi, 120) }}</p>
                                        @endif
                                        <p class="text-xs text-grape-mist mt-2">Pelapor: {{ $laporan->user->name }} &bull; {{ $laporan->created_at->format('d M Y') }}</p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-grape-mist/30">
                                        <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn-secondary !text-sm !py-2 justify-center">
                                            Lihat Detail & Komentar
                                        </a>

                                        <form method="POST" action="{{ route('admin.laporan.update-status', $laporan) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select !mt-0 text-sm min-w-[10rem]">
                                                <option value="menunggu" @selected($laporan->status === 'menunggu')>Menunggu</option>
                                                <option value="proses" @selected($laporan->status === 'proses')>Proses</option>
                                                <option value="selesai" @selected($laporan->status === 'selesai')>Selesai</option>
                                            </select>
                                            <x-primary-button type="submit" class="!text-xs justify-center">Update</x-primary-button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
