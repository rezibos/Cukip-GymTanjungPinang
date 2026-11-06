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

function bindLocationSelection(map, element, locations, mapMarkers) {
    const explorer = element.closest('[data-location-explorer]');
    if (!explorer || locations.length === 0) {
        return;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const photo = explorer.querySelector('[data-location-photo]');
    const details = explorer.querySelector('[data-location-details]');
    let selectedIndex = -1;

    const selectLocation = (index, pan = true) => {
        const location = locations[index];
        if (!location || selectedIndex === index) {
            return;
        }

        selectedIndex = index;
        explorer.querySelector('[data-location-name]').textContent = location.name;
        explorer.querySelector('[data-location-address]').textContent = location.region ?? '';
        explorer.querySelector('[data-location-hours]').textContent = location.hours ?? 'Hubungi gym';
        explorer.querySelector('[data-location-count]').textContent = `${String(index + 1).padStart(2, '0')} / ${String(locations.length).padStart(2, '0')}`;
        photo.src = location.image;
        photo.alt = `Visual ${location.name}`;
        photo.style.objectPosition = location.image_position ?? 'center';

        mapMarkers.forEach((marker, markerIndex) => {
            marker.getElement()?.classList.toggle('is-selected', markerIndex === index);
            marker.setZIndexOffset(markerIndex === index ? 1000 : 0);
        });

        if (!reducedMotion && pan) {
            details.animate([{ opacity: 0.45, transform: 'translateY(6px)' }, { opacity: 1, transform: 'translateY(0)' }], { duration: 260, easing: 'ease-out' });
        }
        if (pan) {
            map.panTo([location.lat, location.lng], { animate: !reducedMotion, duration: 0.5 });
        }
    };

    mapMarkers.forEach((marker, index) => marker.on('click', () => selectLocation(index)));
    selectLocation(0, false);
}

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
        const isSelectable = el.dataset.selectable === 'true';

        const map = L.map(el, {
            scrollWheelZoom: isFull,                 // peta kecil: scroll mouse tidak men-zoom
            dragging: isFull || !L.Browser.mobile,   // peta kecil di HP: geser jari tetap menggulung halaman
            zoomControl: !isSelectable,
        });

        if (isSelectable) {
            L.control.zoom({ position: 'bottomright' }).addTo(map);
        }

        L.tileLayer(TILE_URL, { maxZoom: 19, attribution: ATTRIBUTION }).addTo(map);

        if (markers.length === 0) {
            map.setView(DEFAULT_CENTER, 10);
            return;
        }

        const points = [];
        const mapMarkers = [];

        markers.forEach((m) => {
            const point = [m.lat, m.lng];
            points.push(point);

            const options = { title: m.name, alt: m.name };
            if (isSelectable) {
                options.icon = L.divIcon({
                    className: 'location-map-marker',
                    html: '<span>CG</span>',
                    iconSize: [44, 44],
                    iconAnchor: [22, 22],
                });
            }

            const marker = L.marker(point, options).addTo(map);
            if (!isSelectable) {
                marker.bindPopup(buildPopup(m), { maxWidth: 240 });
            } else {
                const label = document.createElement('span');
                label.textContent = m.name;
                marker.bindTooltip(label, { direction: 'top', offset: [0, -22] });
            }
            mapMarkers.push(marker);
        });

        if (points.length === 1) {
            map.setView(points[0], 15);
        } else {
            map.fitBounds(points, { padding: [30, 30], maxZoom: 14 });
        }

        if (isSelectable) {
            bindLocationSelection(map, el, markers, mapMarkers);
            new ResizeObserver(() => map.invalidateSize({ pan: false })).observe(el);
        }
    });
}
