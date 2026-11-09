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
