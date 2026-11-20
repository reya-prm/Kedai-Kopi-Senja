# Kedai Kopi Senja - Sistem Manajemen Kafe

Varian C, UKK RPL 2026-2027. Dikerjakan oleh Muhamad Reya Rizqya Permana.

## 1. Ringkasan

Kedai Kopi Senja selama ini mencatat pesanan di kertas dan baru tahu bahan habis setelah tutup. Sistem ini dipakai Kasir dan Manajer lewat web di komputer. Manajer mengatur bahan, menu, dan resep, mencatat stok masuk atau penyesuaian stok, mengambil harga bahan dari Supplier, lalu melihat laporan. Kasir membuat pesanan. Begitu pesanan dibuat, stok bahan langsung berkurang sesuai resep. Kalau bahan tidak cukup, pesanan ditolak, dan menu yang bahannya kurang otomatis berstatus Habis. Pesanan berjalan dari Baru ke Diproses ke Selesai, dan tiap pesanan punya struk dengan service 5% dan pajak 10%. Dari resep dan harga bahan, sistem menghitung HPP dan margin tiap menu.

Aplikasi Android untuk pelanggan tidak saya kerjakan (lihat bagian 9).

## 2. Teknologi

- PHP 8.5.8 (composer.json minta minimal 8.3) dan Laravel 13.42.0. Saya sudah terbiasa dengan Laravel, dan migrasi, validasi, serta hak akses sudah tersedia di dalamnya.
- MySQL. Dipilih karena mendukung transaksi, penguncian baris (lockForUpdate), dan CHECK constraint.
- Tampilan memakai Blade (dirender di server) dan Tailwind CSS 4 lewat Vite. Spesifikasi mengizinkan web tanpa REST API, jadi saya tidak membuat API.
- Panggilan ke Supplier memakai HTTP client bawaan Laravel.
- Tes memakai Pest.
- APK: tidak dikerjakan.

Aplikasinya satu proyek (monolith). Aturan pesanan dan stok ada di app/Services/OrderService.php, bukan di controller.

Versi yang dipakai saat menguji: Node 22.17.0, npm 10.9.1, Composer 2.10.1, MySQL 8.0.30.

## 3. Cara Menjalankan Server

Sistem operasi target: Windows 10/11, perintah dijalankan di PowerShell. Di Linux atau macOS, ganti copy dengan cp.

Yang harus sudah terpasang: Git, PHP, Composer, MySQL, Node.js dan npm.

```powershell
git clone https://github.com/reya-prm/Kedai-Kopi-Senja.git
cd Kedai-Kopi-Senja
composer install
copy .env.example .env
php artisan key:generate
```

Buat database kosong di MySQL, misalnya ukk_cafe. Lalu buka file .env dan isi (atau ubah) baris berikut:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ukk_cafe
DB_USERNAME=<user MySQL>
DB_PASSWORD=<password MySQL>
APP_TIMEZONE=Asia/Jakarta
SUPPLIER_URL=<base URL dari dokumen Hari 1, sampai /kafe, tanpa /harga-bahan>
SUPPLIER_API_KEY=<API key dari dokumen Hari 1>
```

Jalankan migrasi, impor data awal, dan seeder demo. Urutannya harus begini:

```powershell
php artisan migrate --seed
php artisan import:data-awal
php artisan db:seed --class=DemoStokSeeder
```

Lalu siapkan tampilan dan jalankan server:

```powershell
npm install
npm run build
php artisan serve
```

Buka http://127.0.0.1:8000 dan login dengan akun di bagian 5.

Catatan:
- Setelah mengubah .env, jalankan php artisan config:clear.
- Harga bahan dari Supplier diambil lewat tombol Sinkron Harga dari Supplier di halaman Bahan dan Laporan (hanya Manajer).
- Kalau sinkron gagal di Windows dan di storage/logs/laravel.log ada "cURL error 60", atur curl.cainfo dan openssl.cafile di php.ini ke file cacert.pem.
- Untuk mengulang dari awal: php artisan migrate:fresh --seed, lalu dua perintah berikutnya di atas. Semua data akan terhapus.

## 4. Cara Memasang APK

Tidak ada. Aplikasi Android tidak dikerjakan (lihat bagian 9).

## 5. Akun Uji

- Manajer: username manajer, password manajer123
- Kasir: username kasir, password kasir12345
- Pelanggan: tidak ada, karena APK tidak dikerjakan

Akun Manajer dan Kasir dibuat oleh AkunStafSeeder saat php artisan migrate --seed. Login lewat halaman /login memakai username.

## 6. Letak Berkas

- Migrasi: database/migrations/
- Command impor data awal: app/Console/Commands/ImportDataAwal.php, dibantu app/Support/ImportHelper.php
- File CSV: database/data/Data_Awal/ (bahan.csv, menu.csv, resep.csv) dan database/data/Data_Seeder_CSV/
- Seeder akun staf: database/seeders/AkunStafSeeder.php
- Seeder data demo: database/seeders/DemoStokSeeder.php
- Aturan pesanan dan stok: app/Services/OrderService.php
- Integrasi Supplier: app/Services/SupplierService.php
- Laporan: app/Http/Controllers/Manajer/LaporanController.php dan resources/views/manajer/laporan/
- Penyesuaian stok: app/Http/Controllers/Manajer/PenyesuaianStokController.php, app/Models/PenyesuaianStok.php, dan resources/views/manajer/stok/penyesuaian.blade.php
- Laporan jam ramai: app/Http/Controllers/Manajer/JamRamaiController.php dan resources/views/manajer/laporan/jam-ramai.blade.php
- Controller lain: app/Http/Controllers/ (folder Web, Manajer, Kasir)
- Middleware peran: app/Http/Middleware/CheckRole.php
- Rute: routes/web.php
- Tes: tests/Feature/
- ERD dan UML: docs/erd/ dan docs/uml/

Perintah impor data awal:

```powershell
php artisan import:data-awal
```

Perintah seeder data demo:

```powershell
php artisan db:seed --class=DemoStokSeeder
```

Seeder demo hanya menyiapkan satu hal, yaitu stok Susu UHT 0,3 liter (300 ml). Nilai 0,3 liter dibaca dari seed_stok_demo.csv lalu diubah ke 300 ml. Stoknya diatur (bukan ditambah), jadi aman dijalankan berkali-kali. Seeder ini tidak menghapus data lain dan tidak membuat transaksi.

## 7. Keputusan Teknis

Format nomor HP: tidak berlaku, karena akun pelanggan hanya dibuat lewat APK yang tidak saya kerjakan.

Harga historis transaksi: tiap baris detail_pesanan menyimpan salinan nama_menu, harga_satuan, jumlah, dan total_baris saat transaksi. Tabel pesanan menyimpan subtotal, service, pajak, dan total. Struk dan laporan penjualan membaca nilai yang tersimpan ini, bukan tabel menu, jadi mengubah harga atau nama menu tidak mengubah struk lama. Harga bahan dan HPP sengaja tidak dibekukan. Laporan margin memakai harga bahan terbaru, dan ini ditulis di halaman laporannya.

Normalisasi satuan: satuan dasar sistem adalah g, ml, dan pcs. Bahan bersatuan kg atau liter dikonversi dengan faktor 1000. Stok dikali 1000 dan harga per satuan dibagi 1000, jadi nilai total stok tidak berubah. Contoh: 5 kg dengan harga Rp180.000 per kg menjadi 5.000 g dengan harga Rp180 per g.

Pencocokan typo nama bahan di resep: nama dirapikan dulu (huruf kecil, spasi pinggir dibuang), lalu dicocokkan persis. Kalau tidak ada yang persis, dihitung jarak Levenshtein ke semua bahan. Hasilnya diterima hanya kalau keempat syarat ini terpenuhi: jarak paling banyak 2, kemiripan minimal 90%, angka di dalam nama sama, dan kandidat terdekatnya hanya satu. Kalau tidak, bahan dianggap tidak terdaftar dan tidak diimpor. Pada data yang ada, "Biji kopi arabica" dicocokkan ke "Biji kopi arabika", sedangkan "Susu oat" dan "Sirup vanila" tidak diimpor. Syarat 90% dibuat karena "Susu oat" hanya beda 2 huruf dari "Susu UHT" dan tidak boleh dianggap sama. Syarat angka dibuat supaya "Gelas 12 oz" tidak tertukar dengan "Gelas 16 oz".

Impor CSV: berjalan dalam satu transaksi, jadi kalau gagal di tengah tidak ada data setengah jadi. Bahan, menu, dan resep yang sudah ada dilewati, jadi impor bisa diulang tanpa menimpa stok. Batas minimum awal semua bahan adalah 0. Menu hasil impor diaktifkan hanya kalau punya resep.

Keputusan lain:
- Login staf pakai username dan password. Pelanggan tidak bisa login lewat web.
- Menu baru selalu nonaktif. Menu baru bisa diaktifkan kalau sudah punya resep. Kalau resepnya dikosongkan, menu otomatis nonaktif.
- Satuan bahan tidak bisa diubah setelah bahan dibuat. Stok tidak diedit langsung dari form bahan; stok berubah lewat stok masuk, pesanan, serta penyesuaian opname atau barang rusak.
- Status Habis dihitung dari stok setiap halaman dibuka, tidak disimpan.
- Stok dipotong saat pesanan dibuat, dalam satu transaksi. Kebutuhan bahan antar item dijumlahkan dulu. Baris bahan dikunci dengan lockForUpdate supaya dua pesanan bersamaan tidak memakai stok yang sama.
- Pembatalan mengembalikan stok dari tabel pemakaian_bahan (jumlah yang benar-benar dipotong), bukan dari resep terbaru. Jadi mengubah resep tidak merusak pesanan lama.
- Service dibulatkan ke rupiah terdekat dari subtotal. Pajak dibulatkan dari subtotal ditambah service.
- Laporan penjualan hanya menghitung pesanan berstatus Selesai, dengan periode berdasarkan tanggal pesanan dibuat. Halaman laporan menampilkan pendapatan menu dan total tagihan.
- Daftar menu Kasir hanya berisi menu aktif yang punya resep. Resep, HPP, dan margin tidak dikirim ke halaman Kasir.
- Harga Supplier disinkronkan manual lewat tombol, dicocokkan lewat kode bahan. URL dan API key ada di .env. Timeout koneksi 3 detik dan total 5 detik. Kalau gagal, harga lama tetap dipakai dan tanggal pembaruan terakhir ditampilkan.
- Stock opname menyimpan jumlah fisik yang dihitung sebagai stok baru; barang rusak dicatat sebagai jumlah yang dibuang dan mengurangi stok. Keduanya menyimpan stok sebelum/sesudah, jenis, waktu, pencatat, dan catatan opsional di riwayat penyesuaian.
- Laporan Jam Ramai mengelompokkan pesanan berstatus Selesai berdasarkan jam pesanan dibuat pada periode tanggal yang dipilih. Laporan menampilkan jumlah pesanan, porsi, serta total tagihan (termasuk service dan pajak). Zona waktu aplikasi dapat diatur lewat APP_TIMEZONE.
- Nomor meja wajib 1 sampai 10.
- Pembayaran tunai atau QRIS hanya dicatat, tidak ada integrasi pembayaran.
- Keamanan dasar: password di-hash, semua form memakai CSRF, validasi di server, hak akses dicek per rute lewat middleware, dan database punya UNIQUE, FOREIGN KEY, serta CHECK pada stok, takaran, dan jumlah.

## 8. Lembar Self-Check

| No | Butir Penerimaan | Klaim (Ya/Tidak) | Langkah Uji Singkat |
|:---:|:---|:---:|:---|
| 1 | Kelola bahan (satuan dasar, nama tidak ganda), menu (bisa dinonaktifkan), dan resep (multi bahan, takaran). | Ya | Login Manajer. Tambah bahan, lalu coba nama yang sama beda huruf atau spasi (ditolak). Nonaktifkan dan aktifkan menu. Di halaman Resep, tambah, ubah takaran, dan hapus bahan. |
| 2 | Pencatatan stok masuk; batas minimum per bahan diatur Manajer dan daftar bahan menipis langsung mengikutinya. | Ya | Stok Masuk: catat Susu UHT, stok bertambah. Ubah batas minimum, lalu cek Bahan Menipis ikut berubah (stok sama dengan batas tetap menipis). |
| 3 | Pesanan dengan beberapa item; stok bahan berkurang sesuai `resep × jumlah porsi`; menu tanpa resep atau nonaktif tidak dapat dipesan. | Ya | Login Kasir, buat pesanan dengan beberapa menu, cek stok di halaman Bahan berkurang sebesar takaran × porsi. Menu nonaktif tidak muncul di form. |
| 4 | **[BUTIR INTI]** Pesanan ditolak bila bahan tidak cukup, kebutuhan gabungan antar item dihitung benar. | Ya | Setelah seeder demo (Susu UHT 300 ml): pesan Latte (150 ml), Cappuccino (120 ml), Flat White (100 ml) sekaligus, total 370 ml. Ditolak dengan rincian bahan yang kurang, stok dan jumlah pesanan tidak berubah. |
| 5 | Menu otomatis berstatus `Habis` bila bahan tidak cukup untuk 1 porsi. | Ya | Pesan Latte 2 porsi (300 ml). Semua menu yang memakai susu jadi Habis di Daftar Menu dan tidak bisa dipesan. Setelah stok masuk, kembali Tersedia. |
| 6 | Status pesanan berjenjang, loncat status ditolak; batal hanya saat `Baru`, hanya oleh Kasir, dan stok kembali. | Ya | Di detail pesanan: Proses lalu Selesai. Loncat atau mundur ditolak server. Batalkan pesanan Baru, stok kembali persis. Pesanan Diproses tidak bisa dibatalkan. |
| 7 | **[BUTIR INTI]** Struk: subtotal, service 5%, pajak 10% (urutan benar), total; struk lama tidak berubah bila harga jual menu diubah. | Ya | Pesan Kentang Goreng 4 porsi (Rp25.000), buka Lihat Struk: subtotal 100.000, service 5.000, pajak 10.500, total 115.500. Ubah harga dan nama menu, buka struk lama, tidak berubah. |
| 8 | HPP dan margin per menu dihitung dari resep. | Ya | Login Manajer, buka Laporan. Latte (harga data awal Rp28.000): HPP Rp6.640, margin Rp21.360, dengan harga bahan dari CSV sebelum sinkron Supplier. |
| 9 | Laporan penjualan per menu, bahan menipis, margin per menu; hak akses ketat (halaman manajemen ditolak bila diakses langsung oleh peran lain; Kasir tidak melihat resep, HPP, margin; Manajer tidak membuat/mengubah status pesanan). | Ya | Selesaikan satu pesanan, lalu cek penjualan di Laporan. Login Kasir, buka /manajer/bahan lewat URL: 403. Login Manajer, buka /kasir/pesanan: 403. Di halaman Kasir, cek kode sumber halaman (Ctrl+U): tidak ada resep, HPP, atau margin. |
| 10 | Data awal CSV: satuan dinormalkan, nama bahan salah ketik di resep dicocokkan, bahan tidak dikenal tidak diimpor, batas minimum awal `0`. | Ya | Jalankan php artisan import:data-awal: 20 bahan, 14 menu, 63 resep. Biji kopi arabika 5.000 g, Susu UHT 20.000 ml. Flat White punya "Biji kopi arabika". "Susu oat" dan "Sirup vanila" tidak masuk. Batas minimum 0. Dijalankan lagi, tidak ada data bertambah. |
| 11 | Integrasi layanan Supplier (dengan API key), *fallback* ke harga terakhir + keterangan tanggal pembaruan bila layanan gagal. | Ya | Klik Sinkron Harga dari Supplier, harga bahan berubah. Ubah SUPPLIER_API_KEY atau SUPPLIER_URL di .env jadi salah, jalankan php artisan config:clear, klik lagi: muncul pesan gagal, harga terakhir tetap dipakai dengan tanggal pembaruannya, sistem tidak error. |
| 12 | **[APK]** Akun pelanggan lengkap: daftar mandiri dengan nomor HP unik + *password* min. 8 karakter, *login* dengan nomor HP, ganti *password*, lupa *password* direset Kasir (wajib ganti saat *login* berikutnya), *logout*. | Tidak | Tidak dikerjakan. |
| 13 | **[APK]** Pelanggan memesan dengan memilih nomor meja (1–10); aturan pesanan dan stok sama dengan pesanan dari Kasir; hanya boleh satu pesanan berstatus `Baru` pada satu waktu. | Tidak | Tidak dikerjakan. |
| 14 | **[APK]** Pelanggan memantau status dan struk pesanannya sendiri; pesanan milik pelanggan lain tidak dapat diakses. | Tidak | Tidak dikerjakan. |

Fitur opsional:

| No | Fitur | Dikerjakan |
|:---:|:---|:---:|
| 01 | Ukuran Reguler dan Large | Tidak |
| 02 | Promo Happy Hour | Tidak |
| 03 | Penyesuaian Stok (Stock Opname / Rusak) | Ya |
| 04 | Laporan Jam Ramai | Ya |
| 05 | Reset Password via Email | Tidak |

Penyesuaian stok hanya dapat dilakukan Manajer lewat **Kelola Stok > Penyesuaian Stok**. Pilih bahan dan jenis penyesuaian. Untuk opname, masukkan hasil hitung fisik termasuk 0 bila stok habis; untuk barang rusak, masukkan jumlah yang dibuang. Jumlah rusak tidak boleh melebihi stok tersedia. Riwayat mencatat stok sebelum dan sesudah perubahan.

Laporan Jam Ramai tersedia di halaman **Laporan**. Pilih rentang tanggal; pesanan yang belum selesai atau dibatalkan tidak dihitung. Jam puncak ditentukan dari jumlah pesanan selesai terbanyak; jika seri, jam paling awal ditampilkan.

## 9. Belum Selesai / Keterbatasan

- Butir 12, 13, dan 14 tidak dikerjakan. Tidak ada aplikasi Android dan tidak ada REST API untuk pelanggan. Sistem hanya punya tampilan web untuk Kasir dan Manajer. Folder /apk kosong.
- Halaman Kasir untuk reset password pelanggan sudah ada, tapi belum bisa dipakai pada alur sebenarnya karena akun pelanggan hanya bisa dibuat lewat aplikasi Android. Kolom must_change_password tersimpan dan dikosongkan saat ganti password, tapi belum ada middleware yang memaksa pelanggan ke halaman ganti password. Password sementara hasil reset ditampilkan di pesan di layar Kasir.
- Kolom pelanggan_id di pesanan selalu kosong karena semua pesanan dibuat Kasir. Aturan satu pesanan Baru per pelanggan belum dipakai.
- Fitur opsional 01, 02, dan 05 tidak dikerjakan. Fitur 03 dan 04 sudah tersedia.
- Sinkron harga Supplier dijalankan manual lewat tombol, tidak terjadwal.
- Login tidak dibatasi jumlah percobaannya (tanpa throttle).
- Tes otomatis masih mencakup alur terpilih: pengalihan login, laporan penjualan, penyesuaian stok, dan laporan jam ramai. Pembuatan pesanan, pembatalan, impor CSV, dan Supplier belum punya tes otomatis.
- Daftar pesanan Kasir dan riwayat stok masuk hanya menampilkan 50 data terakhir, daftar pelanggan 100, tanpa halaman berikutnya.
- Tidak ada riwayat perubahan status pesanan. Riwayat stok masuk, pemakaian bahan, dan penyesuaian tersimpan terpisah, belum sebagai satu buku pergerakan stok terpadu.

## 10. Referensi dan Bantuan

Saya memakai Claude (Anthropic) sebagai mentor dan asisten. Bantuannya: menjelaskan konsep (migrasi, relasi, transaksi, lockForUpdate, jarak Levenshtein), memberi usulan rancangan database, menyarankan kode (migrasi, model, OrderService, command impor CSV, SupplierService, tampilan Blade), dan membantu mencari penyebab error.

Selain Claude, saya memakai Copilot di VS Code pada sesi ini untuk meninjau struktur dan alur aplikasi serta membantu menyusun penjelasan repository. Bagian laporan (LaporanController dan view laporan) dan tes sudah ada di repository, tetapi isi kode saja tidak membuktikan siapa atau alat apa yang menulisnya; saya tidak mengklaim bahwa agen tertentu membuat bagian tersebut.

File CLAUDE.md, AGENTS.md, boost.json, dan .mcp.json di repo adalah konfigurasi alat bantu AI (Laravel Boost), tidak dipakai aplikasi.

Hal yang dapat saya jelaskan dari implementasi: login staf memakai username; menu baru nonaktif sampai memiliki resep; data CSV disimpan di database/data; dan aturan transaksi dipusatkan di OrderService. Saya tidak menyebutnya sebagai perubahan pribadi terhadap hasil AI karena asal-usul perubahan tersebut tidak dapat dipastikan dari kode saja.

Saya sudah membaca dan bisa menjelaskan kode yang dikumpulkan.

Sumber lain: dokumentasi Laravel, dokumentasi PHP (fungsi levenshtein), dokumen spesifikasi proyek, dokumen layanan Supplier dari Hari 1, serta Thunder Client dan curl untuk mencoba API Supplier. Tampilan mengikuti gaya Flowbite memakai kelas Tailwind biasa, tanpa library tambahan.