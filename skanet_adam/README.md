# Sistem Penilaian Sekolah (SKANET)

PHP + MySQL (mysqli) berdasarkan database `skanet_adam`.

## Instalasi (XAMPP)
1. Ekstrak folder `skanet_adam` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL, buka phpMyAdmin.
3. Buat database `skanet_adam`, lalu Import `database/skanet_adam.sql`.
4. Buka http://localhost/skanet_adam/ lalu login: `adam` / `adam`.
5. Jika perlu, ubah kredensial database di `includes/koneksi.php`.

## Fitur
Login, dashboard, CRUD Siswa, Guru, Mapel, Nilai, rapor per siswa (cetak), pencarian.

## Catatan struktur database
- Kolom `users.password` hanya varchar(8) sehingga password disimpan apa adanya (tidak bisa di-hash). Untuk produksi, ubah ke varchar(255) lalu pakai password_hash().
- Tabel `guru` tidak punya PRIMARY KEY; aplikasi memakai `nip` sebagai kunci. Disarankan: `ALTER TABLE guru ADD PRIMARY KEY (nip);`
- Data lama di tabel `nilai` ada yang merujuk ke siswa/mapel/guru yang tidak ada (mis. id_mapel 0). Baris itu tampil bertanda "tidak ditemukan" dan bisa diperbaiki lewat Edit.
- `id_mapel` harus angka, karena kolom `nilai.id_mapel` bertipe int.
