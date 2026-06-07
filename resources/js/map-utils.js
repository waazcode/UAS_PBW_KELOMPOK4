import L from 'leaflet';

export const ACEH_CENTER = [4.5, 96.65];
export const ACEH_PIN_CENTER = [5.5483, 95.3238];
export const ACEH_BOUNDS = L.latLngBounds([2.0, 95.0], [6.3, 98.3]);

export const statusColors = {
    menunggu: '#C0D6EA',
    proses: '#11425D',
    selesai: '#DDFF55',
};

export function isWithinAceh(lat, lng) {
    const bounds = ACEH_BOUNDS;
    return lat >= bounds.getSouth()
        && lat <= bounds.getNorth()
        && lng >= bounds.getWest()
        && lng <= bounds.getEast();
}

export function createColoredPinIcon(color) {
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
