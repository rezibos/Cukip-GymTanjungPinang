import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/dist/sweetalert2.min.css';

window.confirmAdminDelete = async () => {
    const result = await Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#171717',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
    });

    return result.isConfirmed;
};

document.addEventListener('admin-record-created', () => {
    Swal.fire({
        title: 'Berhasil ditambahkan!',
        text: 'Data baru telah disimpan.',
        icon: 'success',
        draggable: true,
        confirmButtonColor: '#171717',
    });
});

document.addEventListener('admin-record-deleted', () => {
    Swal.fire({
        title: 'Berhasil dihapus!',
        text: 'Data telah dihapus.',
        icon: 'success',
        confirmButtonColor: '#171717',
    });
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
