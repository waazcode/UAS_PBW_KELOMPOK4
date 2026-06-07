<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Laporan') }}
        </h2>
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
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pelapor</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($laporans as $laporan)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $laporan->judul }}</div>
                                            @if ($laporan->deskripsi)
                                                <div class="text-sm text-gray-500">{{ Str::limit($laporan->deskripsi, 80) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $laporan->user->name }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if ($laporan->status === 'menunggu') bg-yellow-100 text-yellow-800
                                                @elseif ($laporan->status === 'proses') bg-blue-100 text-blue-800
                                                @else bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst($laporan->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <form method="POST" action="{{ route('admin.laporan.update-status', $laporan) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="border-gray-300 rounded-md text-sm">
                                                    <option value="menunggu" @selected($laporan->status === 'menunggu')>Menunggu</option>
                                                    <option value="proses" @selected($laporan->status === 'proses')>Proses</option>
                                                    <option value="selesai" @selected($laporan->status === 'selesai')>Selesai</option>
                                                </select>
                                                <x-primary-button type="submit">Update</x-primary-button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                            Belum ada laporan.
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
