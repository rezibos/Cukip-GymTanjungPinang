# Pengelolaan Lokasi Gym pada Beranda

## Tujuan

Menghapus bagian **Profil dan lokasi gym** yang tidak mengendalikan tampilan beranda, lalu menggantinya dengan pengelolaan banyak lokasi gym yang benar-benar menjadi sumber data peta pada homepage.

## Struktur Admin

### Halaman Awal

Menu **Halaman Awal** tetap mengelola foto dan tulisan hero beranda. Tidak ada data alamat, telepon, atau koordinat pada menu ini.

### Tentang CukipGym

Card **Profil dan lokasi gym** dihapus. Halaman ini hanya mengelola:

- konten Tentang;
- lima foto galeri;
- konten footer;
- sponsor dan partner.

Nama website yang sebelumnya berada pada card profil tidak lagi diedit dari halaman ini. Identitas merek CukipGym tetap memakai nilai profil yang sudah ada agar perubahan ini tidak merusak metadata dan halaman lain.

### Lokasi Gym

Ditambahkan satu resource Filament bernama **Lokasi Gym** di grup **Konten website**. Halaman daftar mengikuti layout tabel admin yang sudah digunakan resource lain. Admin dapat menambah dan mengubah lokasi melalui modal.

Setiap lokasi memiliki data:

- nama lokasi;
- alamat atau wilayah;
- latitude;
- longitude;
- jam operasional;
- nomor WhatsApp opsional;
- foto lokasi;
- posisi fokus foto;
- status aktif;
- urutan tampil.

Form menyediakan field untuk menempel koordinat, URL Google Maps, atau kode embed. Parser koordinat yang sudah digunakan aplikasi akan mengisi latitude dan longitude secara otomatis.

## Penyimpanan Data

Data lokasi disimpan pada tabel baru `gym_locations`, terpisah dari singleton `gym_profiles`. Pemisahan ini mencegah profil website tercampur dengan data cabang dan memungkinkan jumlah lokasi bertambah tanpa batas tetap.

Enam lokasi contoh dari `config/gym_locations.php` dipindahkan ke database sebagai data awal jika tabel lokasi masih kosong. Proses ini mempertahankan tampilan beranda yang sekarang sekaligus membuat setiap lokasi dapat diedit. File konfigurasi contoh tidak lagi menjadi sumber utama peta.

Foto yang belum diganti menggunakan aset lama sebagai fallback. Foto baru disimpan pada disk `public` di direktori `gym-locations`.

## Aliran Data Beranda

1. `HomeController` mengambil lokasi aktif dari `gym_locations` berdasarkan nilai urutan lalu ID.
2. Model lokasi menghasilkan URL foto, URL WhatsApp, dan URL Google Maps.
3. Controller mengubah koleksi lokasi menjadi struktur marker yang sudah dipahami `gym-map.js`.
4. Peta menampilkan satu marker untuk setiap lokasi aktif.
5. Marker pertama menjadi kartu lokasi awal.
6. Klik marker mengganti nama, alamat, jam, foto, posisi foto, dan penghitung lokasi tanpa memuat ulang halaman.
7. Lokasi nonaktif tidak muncul pada peta.

Jika belum ada lokasi aktif, beranda menampilkan keadaan kosong yang sudah tersedia dan peta tidak gagal dirender.

## Perilaku CRUD

- Admin dapat menambah, mengubah, dan menghapus lokasi.
- Status aktif menentukan apakah lokasi tampil pada beranda.
- Nilai urutan menentukan posisi marker dan kartu awal.
- Nama, alamat, koordinat, jam operasional, dan urutan wajib diisi.
- WhatsApp, foto, dan posisi fokus foto bersifat opsional.
- Koordinat dibatasi pada rentang latitude `-90..90` dan longitude `-180..180`.
- Foto maksimal 5 MB dan hanya menerima format gambar.

## Kompatibilitas

- Layout hero, bagian Tentang, kolase, peta, dan footer tidak diubah.
- Struktur JSON marker tetap kompatibel dengan `resources/js/gym-map.js`.
- Data lama pada `gym_profiles` tidak dihapus oleh migration.
- URL admin lama `/admin/manage-gym-profile` tetap digunakan untuk halaman Tentang.

## Pengujian

- Migration membuat tabel lokasi dan memindahkan enam lokasi contoh saat tabel kosong.
- Admin dapat membuka resource Lokasi Gym.
- Admin dapat membuat dan mengubah lokasi beserta foto.
- Koordinat dan input Google Maps divalidasi.
- Lokasi aktif tampil sesuai urutan pada homepage.
- Lokasi nonaktif tidak tampil.
- Klik marker tetap didukung oleh struktur data yang dihasilkan.
- Halaman Tentang tidak lagi menampilkan card Profil dan lokasi gym.
- Tes admin, homepage, peta, dan build frontend tetap lulus.
