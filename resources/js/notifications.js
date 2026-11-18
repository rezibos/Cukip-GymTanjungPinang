import Swal from 'sweetalert2/dist/sweetalert2.js';

export function showNotification(title, message, type = 'success', target = document.body) {
    const isError = type === 'error';
    const isInfo = type === 'info';
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const icon = isError ? 'error' : (isInfo ? 'info' : 'success');
    const iconPath = isError
        ? '<path d="M12 7v6m0 4h.01"/>'
        : (isInfo ? '<path d="M12 11v6m0-10h.01"/>' : '<path d="m6 12 4 4 8-8"/>');

    return Swal.fire({
        toast: true,
        position: 'top-end',
        width: '22rem',
        icon,
        iconHtml: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${iconPath}</svg>`,
        titleText: title,
        text: message,
        showConfirmButton: false,
        showCloseButton: true,
        closeButtonAriaLabel: 'Tutup notifikasi',
        timer: isError ? undefined : 5000,
        timerProgressBar: !isError,
        target,
        customClass: {
            container: target instanceof HTMLDialogElement ? 'gym-dialog-notification-container' : '',
            popup: 'gym-notification',
        },
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

export async function showConfirmation({
    title,
    message,
    confirmButtonText = 'Ya, lanjutkan',
    cancelButtonText = 'Batal',
    target = document.body,
}) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const result = await Swal.fire({
        icon: 'warning',
        titleText: title,
        text: message,
        showCancelButton: true,
        focusConfirm: false,
        focusCancel: false,
        reverseButtons: true,
        confirmButtonText,
        cancelButtonText,
        target,
        buttonsStyling: false,
        customClass: {
            container: target instanceof HTMLDialogElement ? 'gym-dialog-notification-container' : '',
            popup: 'gym-confirmation',
            icon: 'gym-confirmation-icon',
            title: 'gym-confirmation-title',
            htmlContainer: 'gym-confirmation-message',
            actions: 'gym-confirmation-actions',
            confirmButton: 'gym-confirmation-confirm',
            cancelButton: 'gym-confirmation-cancel',
        },
        ...(reduceMotion ? { showClass: { popup: '' }, hideClass: { popup: '' } } : {}),
    });

    return result.isConfirmed;
}

export async function initNotifications() {
    document.addEventListener('gym:notification', (event) => {
        const { title, message, type = 'success', target = document.body } = event.detail ?? {};

        if (!title || !message) {
            return;
        }

        showNotification(title, message, type, target);
    });

    const notifications = document.querySelectorAll('[data-flash-notification]');

    for (const notification of notifications) {
        const isError = notification.dataset.notificationIcon === 'error';
        const title = notification.dataset.notificationTitle;
        const message = notification.textContent.trim();
        notification.remove();

        await showNotification(title, message, isError ? 'error' : 'success');
    }
}
