import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('pin-map');
    if (!el) return;

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const coordsDisplay = document.getElementById('coords-display');
    const alamatInput = document.getElementById('alamat');
    const suggestionsEl = document.getElementById('alamat-suggestions');
    const alamatWrapper = document.getElementById('alamat-wrapper');

    const suggestUrl = alamatWrapper?.dataset.suggestUrl;
    const reverseUrl = alamatWrapper?.dataset.reverseUrl;

    const BANDA_ACEH_CENTER = [5.5483, 95.3238];
    const BANDA_ACEH_BOUNDS = L.latLngBounds([5.48, 95.26], [5.60, 95.38]);

    const defaultLat = parseFloat(latInput?.value) || BANDA_ACEH_CENTER[0];
    const defaultLng = parseFloat(lngInput?.value) || BANDA_ACEH_CENTER[1];

    const map = L.map('pin-map', { maxBounds: BANDA_ACEH_BOUNDS, minZoom: 12 })
        .setView([defaultLat, defaultLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;
    let suggestTimer = null;
    let activeIndex = -1;
    let currentSuggestions = [];

    function updateCoords(lat, lng) {
        if (latInput) latInput.value = lat.toFixed(8);
        if (lngInput) lngInput.value = lng.toFixed(8);
        if (coordsDisplay) {
            coordsDisplay.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            coordsDisplay.classList.remove('text-gray-400');
            coordsDisplay.classList.add('text-emerald-700');
        }
    }

    function isWithinBandaAceh(lat, lng) {
        return lat >= 5.48 && lat <= 5.60 && lng >= 95.26 && lng <= 95.38;
    }

    function placeMarker(latlng, moveMap = true) {
        if (!isWithinBandaAceh(latlng.lat, latlng.lng)) {
            alert('Lokasi harus berada di wilayah Banda Aceh.');
            return;
        }

        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', async () => {
                const pos = marker.getLatLng();
                updateCoords(pos.lat, pos.lng);
                await fillAddressFromCoords(pos.lat, pos.lng);
            });
        }
        updateCoords(latlng.lat, latlng.lng);
        if (moveMap) {
            map.setView(latlng, Math.max(map.getZoom(), 16));
        }
    }

    async function fillAddressFromCoords(lat, lng) {
        if (!reverseUrl || !alamatInput) return;

        try {
            const res = await fetch(`${reverseUrl}?lat=${lat}&lng=${lng}`);
            const data = await res.json();
            if (data.label) {
                alamatInput.value = data.label;
            }
        } catch {
            // Abaikan jika reverse geocode gagal.
        }
    }

    function hideSuggestions() {
        if (!suggestionsEl) return;
        suggestionsEl.classList.add('hidden');
        suggestionsEl.innerHTML = '';
        activeIndex = -1;
        currentSuggestions = [];
    }

    function renderSuggestions(items) {
        if (!suggestionsEl) return;

        currentSuggestions = items;
        activeIndex = -1;

        if (items.length === 0) {
            suggestionsEl.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500">Alamat tidak ditemukan.</div>';
            suggestionsEl.classList.remove('hidden');
            return;
        }

        suggestionsEl.innerHTML = items.map((item, index) => `
            <button
                type="button"
                class="alamat-option w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 border-b border-gray-100 last:border-b-0"
                data-index="${index}"
            >
                <span class="block font-medium text-gray-900">${item.label.split(',')[0]}</span>
                <span class="block text-xs text-gray-500 mt-0.5">${item.label}</span>
            </button>
        `).join('');

        suggestionsEl.classList.remove('hidden');

        suggestionsEl.querySelectorAll('.alamat-option').forEach((btn) => {
            btn.addEventListener('click', () => {
                selectSuggestion(parseInt(btn.dataset.index, 10));
            });
        });
    }

    function highlightOption() {
        if (!suggestionsEl) return;

        suggestionsEl.querySelectorAll('.alamat-option').forEach((btn, index) => {
            btn.classList.toggle('bg-emerald-50', index === activeIndex);
        });
    }

    function selectSuggestion(index) {
        const item = currentSuggestions[index];
        if (!item || !alamatInput) return;

        alamatInput.value = item.label;
        hideSuggestions();
        placeMarker({ lat: item.lat, lng: item.lng });
    }

    async function searchAddress(query) {
        if (!suggestUrl) return;

        try {
            const res = await fetch(`${suggestUrl}?q=${encodeURIComponent(query)}`);
            const data = await res.json();
            renderSuggestions(data);
        } catch {
            hideSuggestions();
        }
    }

    alamatInput?.addEventListener('input', () => {
        const query = alamatInput.value.trim();

        clearTimeout(suggestTimer);

        if (query.length < 3) {
            hideSuggestions();
            return;
        }

        suggestTimer = setTimeout(() => searchAddress(query), 400);
    });

    alamatInput?.addEventListener('keydown', (e) => {
        if (!suggestionsEl || suggestionsEl.classList.contains('hidden')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = Math.min(activeIndex + 1, currentSuggestions.length - 1);
            highlightOption();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = Math.max(activeIndex - 1, 0);
            highlightOption();
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            selectSuggestion(activeIndex);
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });

    document.addEventListener('click', (e) => {
        if (!alamatWrapper?.contains(e.target)) {
            hideSuggestions();
        }
    });

    map.on('click', async (e) => {
        placeMarker(e.latlng, false);
        await fillAddressFromCoords(e.latlng.lat, e.latlng.lng);
    });

    const geoBtn = document.getElementById('btn-geolocate');
    geoBtn?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung geolokasi.');
            return;
        }
        geoBtn.disabled = true;
        geoBtn.textContent = 'Mencari lokasi...';
        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                const latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                placeMarker(latlng);
                await fillAddressFromCoords(latlng.lat, latlng.lng);
                geoBtn.disabled = false;
                geoBtn.textContent = 'Gunakan Lokasi Saya';
            },
            () => {
                alert('Gagal mendapatkan lokasi. Silakan klik peta secara manual.');
                geoBtn.disabled = false;
                geoBtn.textContent = 'Gunakan Lokasi Saya';
            }
        );
    });

    if (latInput?.value && lngInput?.value) {
        placeMarker({ lat: defaultLat, lng: defaultLng }, false);
    }
});
