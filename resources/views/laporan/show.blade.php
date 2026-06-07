<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-midnight">
                    {{ __('Detail Laporan') }}
                </h2>
                <p class="text-sm text-neptune/70 mt-1">
                    @if ($isAdminView ?? false)
                        Tinjau laporan warga, foto bukti, dan diskusi
                    @else
                        Informasi lengkap dan diskusi laporan
                    @endif
                </p>
            </div>
            <a href="{{ $backUrl ?? route('laporan.index') }}" class="btn-outline text-sm !py-2">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="page-container max-w-4xl">
            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($isAdminView ?? false)
                <div class="card p-4 sm:p-5 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-midnight">Ubah Status Laporan</p>
                        <p class="text-xs text-grape-mist mt-0.5">Perbarui status penanganan laporan ini</p>
                    </div>
                    <form method="POST" action="{{ route('admin.laporan.update-status', $laporan) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select !mt-0 text-sm min-w-[10rem]">
                            <option value="menunggu" @selected($laporan->status === 'menunggu')>Menunggu</option>
                            <option value="proses" @selected($laporan->status === 'proses')>Proses</option>
                            <option value="selesai" @selected($laporan->status === 'selesai')>Selesai</option>
                        </select>
                        <x-primary-button type="submit" class="!text-xs justify-center">Update Status</x-primary-button>
                    </form>
                </div>
            @endif

            <div class="grid lg:grid-cols-5 gap-6 lg:gap-8">
                <div class="lg:col-span-3 space-y-6">
                    <div class="card p-5 sm:p-8">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <x-status-badge :status="$laporan->status" class="!text-sm" />
                            <span class="text-xs font-medium text-neptune bg-pacific/30 px-3 py-1 rounded-lg">
                                {{ $laporan->kategori->nama_tampilan }}
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-midnight">{{ $laporan->judul }}</h1>

                        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm text-grape-mist">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $laporan->user->name }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $laporan->created_at->format('d M Y, H:i') }}
                            </span>
                        </div>

                        @if ($laporan->alamat)
                            <div class="mt-6 p-4 bg-cheviot rounded-xl border border-grape-mist/30">
                                <h3 class="text-xs font-semibold text-neptune uppercase tracking-wide mb-1">Alamat</h3>
                                <p class="text-midnight">{{ $laporan->alamat_tampilan }}</p>
                                @if ($laporan->latitude && $laporan->longitude)
                                    <a href="{{ route('laporan.peta', ['laporan' => $laporan->id]) }}" class="btn-outline btn-sm mt-3">
                                        Lihat di Peta
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="mt-6">
                            <h3 class="text-xs font-semibold text-neptune uppercase tracking-wide mb-2">Deskripsi</h3>
                            <p class="text-midnight/90 whitespace-pre-line leading-relaxed">{{ $laporan->deskripsi }}</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="p-5 sm:p-6 border-b border-grape-mist/30">
                            <h3 class="text-lg font-bold text-midnight">
                                Komentar
                                <span class="text-sm font-normal text-grape-mist">({{ $laporan->komentars->count() }})</span>
                            </h3>
                            <p class="text-xs text-grape-mist mt-1">Komentar terlihat oleh pelapor dan admin</p>
                        </div>

                        <div class="p-5 sm:p-6 space-y-4 max-h-96 overflow-y-auto">
                            @forelse ($laporan->komentars as $komentar)
                                <div class="flex gap-3">
                                    <div class="w-9 h-9 shrink-0 flex items-center justify-center rounded-full {{ $komentar->user->isAdmin() ? 'bg-isotonic text-midnight' : 'bg-neptune text-cheviot' }} text-sm font-bold">
                                        {{ strtoupper(substr($komentar->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-midnight text-sm">{{ $komentar->user->name }}</span>
                                            @if ($komentar->user->isAdmin())
                                                <span class="text-xs px-2 py-0.5 bg-isotonic/30 text-midnight rounded-full font-medium">Admin</span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 bg-pacific/40 text-neptune rounded-full font-medium">Pelapor</span>
                                            @endif
                                            <span class="text-xs text-grape-mist">{{ $komentar->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <p class="text-sm text-neptune/80 mt-1 whitespace-pre-line">{{ $komentar->isi }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-grape-mist text-center py-4">Belum ada komentar. Mulai diskusi di bawah.</p>
                            @endforelse
                        </div>

                        <div class="p-5 sm:p-6 bg-cheviot/50 border-t border-grape-mist/30">
                            <form method="POST" action="{{ route('laporan.komentar.store', $laporan) }}" class="space-y-3">
                                @csrf
                                <x-input-label for="isi" value="{{ ($isAdminView ?? false) ? 'Balas sebagai Admin' : 'Tulis Komentar' }}" />
                                <textarea id="isi" name="isi" rows="3" class="form-input" placeholder="Tulis komentar atau update..." required>{{ old('isi') }}</textarea>
                                <x-input-error :messages="$errors->get('isi')" class="mt-1" />
                                <x-primary-button class="!text-sm">Kirim Komentar</x-primary-button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="card lg:sticky lg:top-24">
                        <div class="p-4 sm:p-5 border-b border-grape-mist/30">
                            <h3 class="text-sm font-semibold text-neptune uppercase tracking-wide">Foto Bukti</h3>
                        </div>
                        <div class="p-4 sm:p-5">
                            @if ($laporan->foto_url)
                                <a href="{{ $laporan->foto_url }}" target="_blank" rel="noopener">
                                    <img
                                        src="{{ $laporan->foto_url }}"
                                        alt="Foto laporan {{ $laporan->judul }}"
                                        class="w-full rounded-xl border border-grape-mist/40 object-cover aspect-video hover:opacity-95 transition"
                                    >
                                </a>
                                <p class="text-xs text-grape-mist mt-2 text-center">Klik untuk memperbesar</p>
                            @else
                                <div class="aspect-video bg-pacific/20 rounded-xl flex items-center justify-center border border-dashed border-grape-mist/50">
                                    <p class="text-sm text-grape-mist">Tidak ada foto</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
