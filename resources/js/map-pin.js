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

    const defaultLat = parseFloat(latInput?.value) || -6.2088;
    const defaultLng = parseFloat(lngInput?.value) || 106.8456;

    const map = L.map('pin-map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    let marker = null;

    function updateCoords(lat, lng) {
        if (latInput) latInput.value = lat.toFixed(8);
        if (lngInput) lngInput.value = lng.toFixed(8);
        if (coordsDisplay) {
            coordsDisplay.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            coordsDisplay.classList.remove('text-gray-400');
            coordsDisplay.classList.add('text-emerald-700');
        }
    }

    function placeMarker(latlng) {
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', () => {
                const pos = marker.getLatLng();
                updateCoords(pos.lat, pos.lng);
            });
        }
        updateCoords(latlng.lat, latlng.lng);
    }

    map.on('click', (e) => placeMarker(e.latlng));

    const geoBtn = document.getElementById('btn-geolocate');
    geoBtn?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung geolokasi.');
            return;
        }
        geoBtn.disabled = true;
        geoBtn.textContent = 'Mencari lokasi...';
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
                map.setView(latlng, 16);
                placeMarker(latlng);
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
        placeMarker({ lat: defaultLat, lng: defaultLng });
    }
});
