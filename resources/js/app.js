// Menu mobile: buka/tutup saat tombol ☰ diklik
const menuButton = document.getElementById('menu-button');
const mobileMenu = document.getElementById('mobile-menu');

if (menuButton && mobileMenu) {
    menuButton.addEventListener('click', () => {
        const isHidden = mobileMenu.classList.toggle('hidden');
        menuButton.setAttribute('aria-expanded', String(!isHidden));
    });
}

// Muat Leaflet hanya jika halaman punya peta
if (document.querySelector('[data-map]')) {
    import('./gym-map').then(({ initMaps }) => initMaps());
}