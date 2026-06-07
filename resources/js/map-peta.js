import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
});

const statusColors = {
    menunggu: '#eab308',
    proses: '#3b82f6',
    selesai: '#22c55e',
};

function createColoredIcon(color) {
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="background:${color};width:14px;height:14px;border-radius:50%;border:2px solid white;box-shadow:0 1px 4px rgba(0,0,0,.4);"></div>`,
        iconSize: [14, 14],
        iconAnchor: [7, 7],
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

    const map = L.map('peta-map').setView([5.5483, 95.3238], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    const markers = new Map();

    laporans.forEach((laporan) => {
        const marker = L.marker(
            [laporan.latitude, laporan.longitude],
            { icon: createColoredIcon(statusColors[laporan.status] ?? '#6b7280') }
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
        map.fitBounds(group.getBounds().pad(0.1));
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
