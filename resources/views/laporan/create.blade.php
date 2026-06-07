<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Laporan') }}
        </h2>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/map-pin.js'])
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="judul" :value="__('Judul')" />
                                    <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" :value="old('judul')" required />
                                    <x-input-error :messages="$errors->get('judul')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                                    <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('deskripsi') }}</textarea>
                                    <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="kategori_id" :value="__('Kategori')" />
                                    <select id="kategori_id" name="kategori_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>{{ $kategori->nama }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('kategori_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="foto" :value="__('Foto')" />
                                    <input id="foto" name="foto" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required />
                                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG. Maks 2MB.</p>
                                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <x-input-label value="Lokasi Kejadian" />
                                    <button type="button" id="btn-geolocate" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                                        Gunakan Lokasi Saya
                                    </button>
                                </div>
                                <p class="text-sm text-gray-500">Klik pada peta untuk menandai lokasi laporan.</p>
                                <div id="pin-map" class="w-full rounded-lg border border-gray-200 z-0" style="height: 300px;"></div>
                                <p class="text-sm">
                                    Koordinat:
                                    <span id="coords-display" class="text-gray-400">Belum dipilih</span>
                                </p>
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                                <x-input-error :messages="$errors->get('latitude')" class="mt-1" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Kirim Laporan') }}</x-primary-button>
                            <a href="{{ route('laporan.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
