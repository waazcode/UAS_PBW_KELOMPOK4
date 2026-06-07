<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Laporan') }}
            </h2>
            <a href="{{ route('laporan.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                + Buat Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Alamat</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($laporans as $laporan)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $laporan->judul }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($laporan->deskripsi, 60) }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($laporan->alamat ?? '-', 40) }}</td>
                                        <td class="px-4 py-3">{{ $laporan->kategori->nama }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if ($laporan->status === 'menunggu') bg-yellow-100 text-yellow-800
                                                @elseif ($laporan->status === 'proses') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst($laporan->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $laporan->created_at->format('d M Y') }}</td>
                                        <td class="px-4 py-3 space-x-3">
                                            <a href="{{ route('laporan.show', $laporan) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Detail</a>
                                            @if ($laporan->latitude && $laporan->longitude)
                                                <a href="{{ route('laporan.peta', ['laporan' => $laporan->id]) }}" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Lihat di Peta</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada laporan. <a href="{{ route('laporan.create') }}" class="text-blue-600 hover:underline">Buat laporan pertama</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
