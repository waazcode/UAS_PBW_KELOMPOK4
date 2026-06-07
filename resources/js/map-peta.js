import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const BANDA_ACEH_CENTER = [5.5483, 95.3238];
const BANDA_ACEH_BOUNDS = L.latLngBounds([5.48, 95.26], [5.60, 95.38]);

const statusColors = {
    menunggu: '#eab308',
    proses: '#3b82f6',
    selesai: '#22c55e',
};

function createColoredPinIcon(color) {
    const width = 36;
    const height = 48;

    return L.divIcon({
        className: 'custom-pin-marker',
        html: `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 48" width="${width}" height="${height}"
                style="display:block;filter:drop-shadow(0 2px 4px rgba(0,0,0,.35));">
                <path d="M18 0C9.716 0 3 6.716 3 15c0 11.25 15 33 15 33s15-21.75 15-33C33 6.716 26.284 0 18 0z"
                    fill="${color}" stroke="#ffffff" stroke-width="2"/>
                <circle cx="18" cy="15" r="6" fill="#ffffff"/>
            </svg>
        `,
        iconSize: [width, height],
        iconAnchor: [width / 2, height],
        popupAnchor: [0, -height + 6],
    });
}

function buildPopupContent(laporan) {
    const statusClass = {
        menunggu: 'bg-yellow-100 text-yellow-800',
        proses: 'bg-blue-100 text-blue-800',
        selesai: 'bg-green-100 text-green-800',
    }[laporan.status] ?? 'bg-gray-100 text-gray-800';

    return `
        <div class="min-w-[200px]">
            <h3 class="font-semibold text-gray-900 mb-1">${laporan.judul}</h3>
            <div class="flex flex-wrap gap-1 mb-2">
                <span class="px-2 py-0.5 text-xs rounded-full ${statusClass}">${laporan.status_label}</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">${laporan.kategori_nama}</span>
            </div>
            ${laporan.alamat ? `<p class="text-sm text-gray-600 mb-1"><strong>Alamat:</strong> ${laporan.alamat}</p>` : ''}
            ${laporan.deskripsi ? `<p class="text-sm text-gray-600 mb-1">${laporan.deskripsi}</p>` : ''}
            <p class="text-xs text-gray-400 mb-2">Oleh ${laporan.pelapor} &middot; ${laporan.created_at}</p>
            ${laporan.detail_url ? `<a href="${laporan.detail_url}" class="text-xs text-emerald-600 hover:underline font-medium">Lihat detail &rarr;</a>` : ''}
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('peta-map');
    if (!el) return;

    const laporans = JSON.parse(el.dataset.laporans || '[]');

    const map = L.map('peta-map', {
        maxBounds: BANDA_ACEH_BOUNDS,
        maxBoundsViscosity: 1.0,
        minZoom: 12,
    }).setView(BANDA_ACEH_CENTER, 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    const markers = new Map();

    laporans.forEach((laporan) => {
        const marker = L.marker(
            [laporan.latitude, laporan.longitude],
            { icon: createColoredPinIcon(statusColors[laporan.status] ?? '#6b7280') }
        )
            .bindPopup(buildPopupContent(laporan))
            .addTo(map);

        markers.set(laporan.id, { marker, laporan });
    });

    const highlightId = parseInt(el.dataset.highlight || '0', 10);

    if (highlightId && markers.has(highlightId)) {
        const { marker } = markers.get(highlightId);
        map.setView(marker.getLatLng(), 16);
        marker.openPopup();
    } else if (laporans.length > 0) {
        const group = L.featureGroup([...markers.values()].map((m) => m.marker));
        map.fitBounds(group.getBounds().pad(0.15), { maxZoom: 16 });
    }

    const statusFilter = document.getElementById('filter-status');
    const kategoriFilter = document.getElementById('filter-kategori');
    const countEl = document.getElementById('marker-count');

    function applyFilters() {
        const status = statusFilter?.value || '';
        const kategori = kategoriFilter?.value || '';
        let visibleCount = 0;

        markers.forEach(({ marker, laporan }) => {
            const matchStatus = !status || laporan.status === status;
            const matchKategori = !kategori || String(laporan.kategori_id) === kategori;
            const show = matchStatus && matchKategori;

            if (show) {
                marker.addTo(map);
                visibleCount++;
            } else {
                marker.remove();
            }
        });

        if (countEl) {
            countEl.textContent = visibleCount;
        }
    }

    statusFilter?.addEventListener('change', applyFilters);
    kategoriFilter?.addEventListener('change', applyFilters);
    applyFilters();
});
