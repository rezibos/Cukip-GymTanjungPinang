import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/dist/sweetalert2.min.css';

const supportedAlertIcons = ['success', 'error', 'warning', 'info', 'question'];

const showFilamentAlert = ({
    title = 'Pemberitahuan',
    message = '',
    type = 'info',
    confirmButtonText = 'Tutup',
} = {}) => Swal.fire({
    title,
    text: message || undefined,
    icon: supportedAlertIcons.includes(type) ? type : 'info',
    confirmButtonColor: '#171717',
    confirmButtonText,
});

window.confirmFilamentAction = async ({
    title = 'Lanjutkan tindakan?',
    message = 'Pastikan tindakan ini memang ingin dilakukan.',
    confirmButtonText = 'Ya, lanjutkan',
    cancelButtonText = 'Batal',
} = {}) => {
    const result = await Swal.fire({
        title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#171717',
        cancelButtonColor: '#d33',
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
        focusCancel: true,
    });

    return result.isConfirmed;
};

window.confirmAdminDelete = () => window.confirmFilamentAction({
    title: 'Yakin ingin menghapus?',
    message: 'Data yang dihapus tidak dapat dikembalikan.',
    confirmButtonText: 'Ya, hapus',
});

document.addEventListener('admin-record-created', () => {
    showFilamentAlert({
        title: 'Berhasil ditambahkan!',
        message: 'Data baru telah disimpan.',
        type: 'success',
    });
});

document.addEventListener('admin-record-deleted', () => {
    showFilamentAlert({
        title: 'Berhasil dihapus!',
        message: 'Data telah dihapus.',
        type: 'success',
    });
});

document.addEventListener('filament-sweetalert', (event) => {
    showFilamentAlert(event.detail);
});

const showFilamentFlashNotifications = () => {
    document.querySelectorAll('[data-filament-flash]').forEach((notification) => {
        showFilamentAlert({
            title: notification.dataset.notificationTitle,
            message: notification.textContent.trim(),
            type: notification.dataset.notificationType,
        });
        notification.remove();
    });
};

showFilamentFlashNotifications();
document.addEventListener('livewire:navigated', showFilamentFlashNotifications);

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('[data-member-class-cancel-form]');

    if (!form) {
        return;
    }

    event.preventDefault();

    const className = form.dataset.className || 'kelas ini';
    const isConfirmed = await window.confirmFilamentAction({
        title: `Batalkan booking ${className}?`,
        message: 'Slotmu akan tersedia kembali untuk member lain.',
        confirmButtonText: 'Ya, batalkan',
        cancelButtonText: 'Tetap booking',
    });

    if (isConfirmed) {
        form.submit();
    }
});

const globallySelectableResources = [
    '.fi-resource-transactions',
    '.fi-resource-memberships',
    '.fi-resource-users',
    '.fi-resource-personal-trainers',
    '.fi-resource-trainer-slots',
    '.fi-resource-class-sessions',
    '.fi-resource-class-bookings',
    '.fi-resource-facilities',
    '.fi-resource-membership-plans',
    '.fi-resource-reviews',
].join(', ');

document.addEventListener('click', async (event) => {
    const checkbox = event.target.closest('.fi-ta-page-checkbox');

    if (!checkbox || !checkbox.closest(globallySelectableResources)) {
        return;
    }

    const tableElement = checkbox.closest('.fi-ta');
    const table = tableElement && window.Alpine?.$data(tableElement);

    if (!table?.selectAllRecords || !table?.deselectAllRecords) {
        return;
    }

    const shouldSelectAll = table.getSelectedRecordsCount() === 0;

    event.stopImmediatePropagation();

    if (shouldSelectAll) {
        await table.selectAllRecords();

        return;
    }

    table.deselectAllRecords();
}, true);
