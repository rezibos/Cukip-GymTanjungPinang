import L from 'leaflet';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Perbaikan ikon marker: Vite mengubah lokasi gambar, jadi kita arahkan manual.
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

const TILE_URL = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
const ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
const DEFAULT_CENTER = [0.95, 104.5]; // sekitar Tanjungpinang dan Bintan

// Isi popup: nama, wilayah, rating, tombol lihat detail
function buildPopup(m) {
    const box = document.createElement('div');
    box.className = 'min-w-40 text-sm';

    const name = document.createElement('div');
    name.className = 'font-semibold text-gray-900';
    name.textContent = m.name;
    box.appendChild(name);

    const area = document.createElement('div');
    area.className = 'text-gray-600';
    area.textContent = m.district ? `${m.district}, ${m.region}` : m.region;
    box.appendChild(area);

    const rating = document.createElement('div');
    rating.className = 'text-gray-600';
    rating.textContent = m.rating !== null
        ? `★ ${Number(m.rating).toFixed(1)} (${m.reviews ?? 0} review)`
        : 'Belum ada rating';
    box.appendChild(rating);

    if (m.url) {
        const link = document.createElement('a');
        link.href = m.url;
        link.textContent = 'Lihat detail';
        link.style.cssText =
            'display:inline-block;margin-top:6px;padding:4px 10px;background:#166534;color:#fff;border-radius:4px;text-decoration:none';
        box.appendChild(link);
    }

    return box;
}

export function initMaps() {
    document.querySelectorAll('[data-map]').forEach((el) => {
        const markers = JSON.parse(el.dataset.markers || '[]');
        const isFull = el.dataset.full === 'true';

        const map = L.map(el, {
            scrollWheelZoom: isFull,                 // peta kecil: scroll mouse tidak men-zoom
            dragging: isFull || !L.Browser.mobile,   // peta kecil di HP: geser jari tetap menggulung halaman
        });

        L.tileLayer(TILE_URL, { maxZoom: 19, attribution: ATTRIBUTION }).addTo(map);

        if (markers.length === 0) {
            map.setView(DEFAULT_CENTER, 10);
            return;
        }

        const points = [];

        markers.forEach((m) => {
            const point = [m.lat, m.lng];
            points.push(point);

            L.marker(point, { title: m.name, alt: m.name })
                .addTo(map)
                .bindPopup(buildPopup(m), { maxWidth: 240 });
        });

        if (points.length === 1) {
            map.setView(points[0], 15);
        } else {
            map.fitBounds(points, { padding: [30, 30], maxZoom: 14 });
        }
    });
}