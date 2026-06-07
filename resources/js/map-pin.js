import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { ACEH_BOUNDS, ACEH_PIN_CENTER, createColoredPinIcon, isWithinAceh, statusColors } from './map-utils';

function formatDisplayText(text) {
    return (text || '').replace(/-/g, ' ').replace(/\s+/g, ' ').trim();
}

function formatAlamatLines(label) {
    const formatted = formatDisplayText(label);
    const parts = formatted.split(',').map((part) => part.trim()).filter(Boolean);

    return {
        utama: parts[0] || formatted,
        detail: parts.slice(1).join(', '),
    };
}

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

    const defaultLat = parseFloat(latInput?.value) || ACEH_PIN_CENTER[0];
    const defaultLng = parseFloat(lngInput?.value) || ACEH_PIN_CENTER[1];
    const pinIcon = createColoredPinIcon(statusColors.menunggu);

    const map = L.map('pin-map', { maxBounds: ACEH_BOUNDS, minZoom: 7 })
        .setView([defaultLat, defaultLng], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;
    let suggestTimer = null;
    let suggestAbort = null;
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

    function placeMarker(latlng, moveMap = true) {
        if (!isWithinAceh(latlng.lat, latlng.lng)) {
            alert('Lokasi harus berada di wilayah Aceh.');
            return;
        }

        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, { icon: pinIcon, draggable: true }).addTo(map);
            marker.on('dragend', async () => {
                const pos = marker.getLatLng();
                if (!isWithinAceh(pos.lat, pos.lng)) {
                    alert('Lokasi harus berada di wilayah Aceh.');
                    marker.setLatLng(latlng);
                    return;
                }
                updateCoords(pos.lat, pos.lng);
                await fillAddressFromCoords(pos.lat, pos.lng);
            });
        }
        updateCoords(latlng.lat, latlng.lng);
        if (moveMap) {
            map.setView(latlng, Math.max(map.getZoom(), 14));
        }
    }

    async function fillAddressFromCoords(lat, lng) {
        if (!reverseUrl || !alamatInput) return;

        try {
            const res = await fetch(`${reverseUrl}?lat=${lat}&lng=${lng}`);
            const data = await res.json();
            if (data.label) {
                alamatInput.value = formatDisplayText(data.label);
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
        if (suggestAbort) {
            suggestAbort.abort();
            suggestAbort = null;
        }
    }

    function showLoadingSuggestions() {
        if (!suggestionsEl) return;
        suggestionsEl.innerHTML = `
            <div class="px-4 py-3 text-sm text-neptune flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-neptune" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Mencari alamat...
            </div>
        `;
        suggestionsEl.classList.remove('hidden');
    }

    function renderSuggestions(items) {
        if (!suggestionsEl) return;

        currentSuggestions = items;
        activeIndex = -1;

        if (items.length === 0) {
            suggestionsEl.innerHTML = '<div class="px-4 py-3 text-sm text-grape-mist">Alamat tidak ditemukan di wilayah Aceh.</div>';
            suggestionsEl.classList.remove('hidden');
            return;
        }

        suggestionsEl.innerHTML = items.map((item, index) => {
            const lines = formatAlamatLines(item.label);

            return `
            <button
                type="button"
                class="alamat-option w-full text-left px-4 py-3 text-sm text-midnight hover:bg-pacific/30 border-b border-grape-mist/30 last:border-b-0"
                data-index="${index}"
            >
                <span class="block font-semibold text-midnight">${lines.utama}</span>
                ${lines.detail ? `<span class="block text-xs text-neptune/70 mt-0.5">${lines.detail}</span>` : ''}
            </button>
        `;
        }).join('');

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
            btn.classList.toggle('bg-pacific/40', index === activeIndex);
        });
    }

    function selectSuggestion(index) {
        const item = currentSuggestions[index];
        if (!item || !alamatInput) return;

        alamatInput.value = formatDisplayText(item.label);
        hideSuggestions();
        placeMarker({ lat: item.lat, lng: item.lng });
    }

    async function searchAddress(query) {
        if (!suggestUrl) return;

        if (suggestAbort) {
            suggestAbort.abort();
        }
        suggestAbort = new AbortController();

        try {
            const res = await fetch(`${suggestUrl}?q=${encodeURIComponent(query)}`, {
                signal: suggestAbort.signal,
                headers: { Accept: 'application/json' },
            });
            const data = await res.json();
            renderSuggestions(data);
        } catch (err) {
            if (err.name !== 'AbortError') {
                hideSuggestions();
            }
        } finally {
            suggestAbort = null;
        }
    }

    alamatInput?.addEventListener('input', () => {
        const query = alamatInput.value.trim();

        clearTimeout(suggestTimer);

        if (query.length < 2) {
            hideSuggestions();
            return;
        }

        showLoadingSuggestions();
        suggestTimer = setTimeout(() => searchAddress(query), 150);
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
