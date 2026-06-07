<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Laporan') }}
            </h2>
            <a href="{{ route('laporan.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <h3 class="text-2xl font-bold">{{ $laporan->judul }}</h3>
                        <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-500">
                            <span>Kategori: <strong class="text-gray-700">{{ $laporan->kategori->nama }}</strong></span>
                            <span>&bull;</span>
                            <span>Pelapor: <strong class="text-gray-700">{{ $laporan->user->name }}</strong></span>
                            <span>&bull;</span>
                            <span>{{ $laporan->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="px-3 py-1 text-sm rounded-full
                            @if ($laporan->status === 'menunggu') bg-yellow-100 text-yellow-800
                            @elseif ($laporan->status === 'proses') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            Status: {{ ucfirst($laporan->status) }}
                        </span>
                    </div>

                    @if ($laporan->alamat)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Alamat</h4>
                            <p class="text-gray-700">{{ $laporan->alamat }}</p>
                            @if ($laporan->latitude && $laporan->longitude)
                                <a href="{{ route('laporan.peta', ['laporan' => $laporan->id]) }}" class="inline-block mt-2 text-sm text-emerald-600 hover:text-emerald-800 font-medium">
                                    Lihat lokasi di peta &rarr;
                                </a>
                            @endif
                        </div>
                    @endif

                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Deskripsi</h4>
                        <p class="text-gray-700 whitespace-pre-line">{{ $laporan->deskripsi }}</p>
                    </div>

                    @if ($laporan->foto)
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Foto</h4>
                            <img src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto laporan" class="rounded-lg max-w-full h-auto border border-gray-200">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
