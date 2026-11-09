import Swal from 'sweetalert2/dist/sweetalert2.js';

export function showNotification(title, message, type = 'success') {
    const isError = type === 'error';
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    return Swal.fire({
        toast: true,
        position: 'top-end',
        width: '22rem',
        icon: isError ? 'error' : 'success',
        iconHtml: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${isError ? '<path d="M12 7v6m0 4h.01"/>' : '<path d="m6 12 4 4 8-8"/>'}</svg>`,
        titleText: title,
        text: message,
        showConfirmButton: false,
        showCloseButton: true,
        closeButtonAriaLabel: 'Tutup notifikasi',
        timer: isError ? undefined : 5000,
        timerProgressBar: !isError,
        customClass: { popup: 'gym-notification' },
        ...(reduceMotion ? { showClass: { popup: '' }, hideClass: { popup: '' } } : {}),
        didOpen: (popup) => {
            popup.addEventListener('mouseenter', Swal.stopTimer);
            popup.addEventListener('mouseleave', Swal.resumeTimer);
            popup.addEventListener('focusin', Swal.stopTimer);
            popup.addEventListener('focusout', (event) => {
                if (!popup.contains(event.relatedTarget)) {
                    Swal.resumeTimer();
                }
            });
        },
    });
}

export async function initNotifications() {
    const notifications = document.querySelectorAll('[data-flash-notification]');

    for (const notification of notifications) {
        const isError = notification.dataset.notificationIcon === 'error';
        const title = notification.dataset.notificationTitle;
        const message = notification.textContent.trim();
        notification.remove();

        await showNotification(title, message, isError ? 'error' : 'success');
    }
}
