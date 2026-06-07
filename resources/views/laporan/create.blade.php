<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-midnight">
                {{ __('Buat Laporan') }}
            </h2>
            <p class="text-sm text-neptune/70 mt-1">Isi formulir dan tandai lokasi kejadian di peta</p>
        </div>
    </x-slot>

    @push('scripts')
        @vite(['resources/js/map-pin.js'])
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const wrapper = document.getElementById('kategori-wrapper');
                const select = document.getElementById('kategori_id');
                const lainnyaWrap = document.getElementById('kategori-lainnya-wrap');
                const lainnyaInput = document.getElementById('kategori_lain');
                const lainnyaId = wrapper?.dataset.lainnyaId;

                function toggleKategoriLainnya() {
                    const isLainnya = lainnyaId && select.value === lainnyaId;
                    lainnyaWrap?.classList.toggle('hidden', !isLainnya);
                    if (lainnyaInput) {
                        lainnyaInput.required = isLainnya;
                        if (!isLainnya) lainnyaInput.value = '';
                    }
                }

                select?.addEventListener('change', toggleKategoriLainnya);
                toggleKategoriLainnya();
            });
        </script>
    @endpush

    <div class="py-8 sm:py-12">
        <div class="page-container">
            <div class="card">
                <div class="p-5 sm:p-8">
                    <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-5">
                                <div>
                                    <x-input-label for="judul" :value="__('Judul Laporan')" />
                                    <x-text-input id="judul" name="judul" type="text" class="block w-full" :value="old('judul')" placeholder="Contoh: Lampu jalan mati" required />
                                    <x-input-error :messages="$errors->get('judul')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-input" placeholder="Jelaskan masalah secara detail..." required>{{ old('deskripsi') }}</textarea>
                                    <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                                </div>

                                <div
                                    class="relative"
                                    id="alamat-wrapper"
                                    data-suggest-url="{{ route('alamat.suggest') }}"
                                    data-reverse-url="{{ route('alamat.reverse') }}"
                                >
                                    <x-input-label for="alamat" :value="__('Alamat')" />
                                    <input
                                        id="alamat"
                                        name="alamat"
                                        type="text"
                                        value="{{ old('alamat') }}"
                                        autocomplete="off"
                                        placeholder="Contoh: Jl. Teuku Umar, Banda Aceh"
                                        class="form-input"
                                        required
                                    />
                                    <div id="alamat-suggestions" class="hidden absolute z-50 w-full mt-1 bg-white border border-grape-mist/50 rounded-xl shadow-lg max-h-60 overflow-y-auto"></div>
                                    <p class="mt-1.5 text-xs text-grape-mist">Ketik minimal 2 huruf. Saran alamat hanya untuk wilayah Aceh.</p>
                                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                                </div>

                                <div id="kategori-wrapper" data-lainnya-id="{{ $lainnyaKategoriId }}">
                                    <x-input-label for="kategori_id" :value="__('Kategori')" />
                                    <select id="kategori_id" name="kategori_id" class="form-select" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>{{ $kategori->nama_tampilan }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('kategori_id')" class="mt-2" />

                                    <div id="kategori-lainnya-wrap" class="mt-3 hidden">
                                        <x-input-label for="kategori_lain" value="Tulis Kategori Lainnya" />
                                        <x-text-input
                                            id="kategori_lain"
                                            name="kategori_lain"
                                            type="text"
                                            class="block w-full"
                                            :value="old('kategori_lain')"
                                            placeholder="Contoh: Drainase, Trotoar, dll."
                                        />
                                        <p class="mt-1.5 text-xs text-grape-mist">Isi kategori yang belum ada di daftar.</p>
                                        <x-input-error :messages="$errors->get('kategori_lain')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="foto" :value="__('Foto Bukti')" />
                                    <input id="foto" name="foto" type="file" accept="image/*" class="mt-1 block w-full text-sm text-neptune file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-pacific/50 file:text-neptune hover:file:bg-pacific/70 transition" required />
                                    <p class="mt-1.5 text-xs text-grape-mist">Format: JPG, PNG. Maks 2MB.</p>
                                    <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <x-input-label value="Lokasi Kejadian" />
                                    <button type="button" id="btn-geolocate" class="text-xs text-neptune hover:text-midnight font-semibold underline underline-offset-2">
                                        Gunakan Lokasi Saya
                                    </button>
                                </div>
                                <p class="text-xs text-grape-mist">Pilih alamat dari saran atau klik peta untuk menandai lokasi.</p>
                                <div id="pin-map" class="w-full rounded-xl border-2 border-pacific/60 z-0" style="height: 280px;"></div>
                                <div class="card-pacific px-4 py-3 text-sm">
                                    <span class="text-neptune/70">Koordinat: </span>
                                    <span id="coords-display" class="font-medium text-midnight">Belum dipilih</span>
                                </div>
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                                <x-input-error :messages="$errors->get('latitude')" class="mt-1" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-1" />
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-4 border-t border-grape-mist/30">
                            <x-primary-button class="justify-center">{{ __('Kirim Laporan') }}</x-primary-button>
                            <a href="{{ route('laporan.index') }}" class="btn-outline justify-center">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
