# Pengelolaan Konten Beranda CukipGym

## Tujuan

Membuat seluruh konten utama beranda CukipGym dapat dikelola dari panel admin tanpa mengubah susunan visual publik yang sudah ada. Pengelolaan dibagi menjadi dua menu: **Halaman Awal** dan **Tentang CukipGym**.

## Struktur Admin

### Halaman Awal

Menu ini mengelola bagian hero yang pertama kali terlihat saat pengunjung membuka beranda:

- foto hero;
- label kecil di atas judul;
- judul utama dan judul bergaya outline;
- deskripsi;
- teks tombol utama;
- teks tautan menuju bagian Tentang.

Satu foto hero digunakan untuk lapisan berwarna dan lapisan abu-abu. Efek perubahan warna yang sudah ada tetap dipertahankan melalui tampilan, sehingga admin hanya perlu mengunggah satu gambar.

### Tentang CukipGym

Menu ini menggantikan fungsi menu Profil Gym saat ini dan dibagi menjadi beberapa bagian:

1. **Profil dan lokasi gym** — nama, deskripsi profil, jam operasional, telepon, WhatsApp, alamat, koordinat, dan foto gym.
2. **Konten Tentang** — label, judul, dua paragraf deskripsi, serta teks yang menempel pada foto utama galeri.
3. **Galeri** — lima unggahan gambar terpisah yang mempertahankan posisi kolase saat ini.
4. **Footer** — deskripsi singkat dan teks lokasi.
5. **Sponsor dan partner** — daftar dinamis yang dapat ditambah, dihapus, diurutkan, dinonaktifkan, dan diisi nama, logo, serta tautan opsional.

Menu navigasi pada footer tetap berasal dari route aplikasi agar tautan tidak dapat rusak karena salah input. Nama gym pada footer menggunakan nama profil, sedangkan tahun hak cipta tetap dibuat otomatis.

## Penyimpanan Data

Data baru ditambahkan pada data profil gym yang sudah digunakan oleh beranda. Kolom teks dan jalur gambar baru bersifat nullable agar migrasi aman untuk data lama. Daftar sponsor disimpan sebagai JSON terstruktur dan di-cast menjadi array pada model.

Gambar disimpan pada disk `public` di direktori khusus konten beranda. Setiap gambar memiliki nilai bawaan dari aset yang sekarang digunakan. Karena itu, beranda tetap lengkap apabila admin belum mengunggah gambar baru atau mengosongkan salah satu field.

## Aliran Data

1. Admin membuka salah satu dari dua menu konten.
2. Form diisi dari baris profil gym aktif.
3. Setelah disimpan, validasi Filament memeriksa teks dan gambar.
4. Beranda membaca data profil yang sama melalui `HomeController`.
5. Blade menampilkan data database jika tersedia dan memakai konten bawaan sebagai fallback.
6. Footer hanya menampilkan sponsor yang aktif. Logo menggunakan nama sponsor sebagai teks alternatif, dan tautan hanya dibuat jika URL diisi.

## Perilaku Tampilan

- Layout, animasi, palet hitam-putih-abu-abu, ukuran kolase, dan posisi footer tidak diubah.
- Gambar unggahan menggunakan `object-cover` agar susunan tetap rapi pada rasio gambar berbeda.
- Daftar sponsor menyesuaikan jumlah data secara responsif.
- Sponsor tanpa logo tetap dapat ditampilkan memakai nama sponsor, sehingga data tidak menghasilkan gambar rusak.
- Gambar yang gagal atau belum tersedia kembali ke aset bawaan yang sesuai.

## Validasi dan Penanganan Kesalahan

- Unggahan hanya menerima gambar dan dibatasi maksimal 5 MB per gambar.
- Nama sponsor wajib jika satu item sponsor ditambahkan.
- URL sponsor bersifat opsional tetapi harus berupa URL valid jika diisi.
- Item sponsor yang belum lengkap tidak ditampilkan pada footer.
- Penyimpanan menampilkan notifikasi berhasil atau pesan validasi pada field terkait.

## Pengujian

- Admin dapat membuka kedua halaman pengelolaan konten.
- Pengguna non-admin tidak dapat mengakses halaman tersebut.
- Konten hero, Tentang, galeri, footer, dan sponsor tersimpan pada profil gym.
- Beranda menampilkan data tersimpan.
- Beranda memakai konten dan gambar bawaan ketika field baru kosong.
- Sponsor nonaktif tidak ditampilkan, sedangkan sponsor aktif tampil sesuai urutan.
- Build aset dan pengujian fitur terkait beranda serta admin tetap lulus.

## Batasan Ruang Lingkup

Perubahan ini tidak membuat page builder umum dan tidak mengubah urutan section beranda. Fitur hanya mengelola konten hero, Tentang, kolase galeri, profil/lokasi gym, footer, serta sponsor dan partner.
