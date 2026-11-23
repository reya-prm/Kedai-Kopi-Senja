# Kedai Kopi Senja - Sistem Manajemen Kafe

## 1. Ringkasan

Sistem bernama Kedai Kopi Senja yang digunakan oleh Manajer dan Kasir. Masalah utama yang diterima klien yaitu pemesanan masih tertulis dan apabila kedai tersebut ramai kertas sering tertukar atau hilang dan stok baru terhitung saat kedai tutup, jadi klien tidak tahu persis apa yang tersisa selama jam buka. Pada sistem ini, Manajer diberikan akses untuk mengelola bahan, menu, resep stok, cek laporan, sedangkan kasir diberikan akses untuk mengelola pesanan dan reset password pelanggan (namun karena tidak adanya APK jadi fitur tersebut tidak berjalan). Sistem memeriksa ketersediaan bahan berdasarkan resep, menolak jika stok tidak cukup, mengurangi stok dan mencatat pesanan agar kasir dapat memperbarui status serta mencetak struk. Manajer dapet memantau penjualan dan jam ramai lewat laporan.

## 2. Teknologi

- PHP 8.5.8 dan Laravel 13.42.0.
- MySQL 8.0.30
- Blade dan Tailwind CSS.
- HTTP client bawaan Laravel.

Alat yang digunakan antara lain VS Code, Git, GitHub, Composer, npm, Thunder Client, dan curl.

## 3. Cara menjalankan server

Siapkan Git, PHP, Composer, MySQL, Node.js, dan npm.

```powershell
git clone https://github.com/reya-prm/Kedai-Kopi-Senja.git
cd Kedai-Kopi-Senja
composer install
copy .env.example .env
php artisan key:generate
```

Buat database kosong, misalnya `ukk_cafe`, lalu isi pengaturan database dan Supplier di `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ukk_cafe
DB_USERNAME=<user MySQL>
DB_PASSWORD=<password MySQL>
APP_TIMEZONE=Asia/Jakarta
SUPPLIER_URL=<base URL Supplier sampai /kafe, tanpa /harga-bahan>
SUPPLIER_API_KEY=<API key Supplier>
```

Jalankan perintah berikut secara berurutan:

```powershell
php artisan migrate --seed
php artisan import:data-awal
php artisan db:seed --class=DemoStokSeeder
npm install
npm run build
php artisan serve
```

Buka http://127.0.0.1:8000. Setelah mengubah `.env`, jalankan `php artisan config:clear`. Hati-hati: `php artisan migrate:fresh --seed` menghapus semua data database.

Jika sinkronisasi Supplier gagal di Windows dan log menunjukkan `cURL error 60`, atur `curl.cainfo` dan `openssl.cafile` di `php.ini` ke file `cacert.pem`.

## 4. Cara memasang APK

Belum ada APK. Jadi fitur pelanggan melalui Andorid belum ada.

## 5. Akun uji

- Manajer: username `manajer`, password `manajer123`
- Kasir: username `kasir`, password `kasir12345`

Akun dibuat oleh `AkunStafSeeder`. Login melalui `/login` memakai username.

## 6. Letak berkas

- Migrasi: `database/migrations/`
- CSV awal dan data demo: `database/data/`
- Impor CSV: `app/Console/Commands/ImportDataAwal.php` dan `app/Support/ImportHelper.php`
- Akun dan data demo: `database/seeders/`
- Rute dan hak akses: `routes/web.php` dan `app/Http/Middleware/CheckRole.php`
- Pesanan dan stok: `app/Services/OrderService.php`
- Supplier: `app/Services/SupplierService.php`
- Laporan: `app/Http/Controllers/Manajer/` dan `resources/views/manajer/laporan/`
- Penyesuaian stok: `app/Http/Controllers/Manajer/PenyesuaianStokController.php`
- Tes: `tests/Feature/`

Halaman utama:

- Manajer: `/manajer/bahan`, `/manajer/bahan-menipis`, `/manajer/menu`, `/manajer/resep`, `/manajer/stok-masuk`, dan `/manajer/laporan`
- Kasir: `/kasir/menu`, `/kasir/pesanan`, `/kasir/pesanan/buat`, dan `/kasir/pelanggan`
- Ganti password: `/password`

Seeder demo mengatur stok Susu UHT menjadi 300 ml. Nilainya diatur, bukan ditambahkan, jadi seeder aman dijalankan berulang kali.

## 7. Keputusan Anda

- Login Pengguna (Manajer dan Kasir) menggunakan Username bukan email. Istilah dalam sistem (kode) menggunakan bahasa Inggris, sedangkan website menggunakan bahasa Indonesia
- Validasi ditulis di controller dan rute ditulis satu per satu; proyek ini tidak menggunakan `Route::resource` atau Form Request.
- Laporan penjualan dan jam ramai hanya menghitung pesanan Selesai. HPP dan margin memakai harga bahan terbaru.
- Sinkronisasi harga Supplier dilakukan manual. Jika gagal, harga terakhir tetap digunakan dan tanggal pembaruan ditampilkan.
- Nomor meja 1-10. Pembayaran Tunai dan Qris tidak terhubung dengan payment apapun, jadi hanya mencatat saja.

## 8. Lembar self-check

| No. | Butir | Status |
|---:|---|:---:|
| 1 | Kelola bahan, menu, dan resep | Ya |
| 2 | Stok masuk dan batas minimum | Ya |
| 3 | Pesanan multi-item dan pemotongan stok | Ya |
| 4 | Pesanan ditolak jika stok tidak cukup | Ya |
| 5 | Status menu Habis otomatis | Ya |
| 6 | Alur status dan pembatalan pesanan | Ya |
| 7 | Struk, service, pajak, dan harga historis | Ya |
| 8 | HPP dan margin menu | Ya |
| 9 | Laporan dan hak akses per peran | Ya |
| 10 | Impor CSV dan normalisasi satuan | Ya |
| 11 | Sinkronisasi harga Supplier | Ya |
| 12 | Akun pelanggan melalui APK | Tidak |
| 13 | Pemesanan pelanggan melalui APK | Tidak |
| 14 | Pelanggan melihat pesanan dan struk | Tidak |

Fitur opsional yang dibuat adalah penyesuaian stok dan laporan jam ramai. Ukuran menu, promo, dan reset password lewat email tidak dibuat.

## 9. Belum selesai / keterbatasan

- Tidak ada APK atau REST API untuk pelanggan. Semua pesanan web dibuat oleh Kasir.
- Reset password pelanggan belum menjadi alur yang lengkap. Sistem belum memaksa pelanggan mengganti password sementara.
- Sinkronisasi Supplier masih manual dan login belum memiliki pembatasan percobaan.
- Tes otomatis mencakup alur terpilih. Pembuatan/pembatalan pesanan, impor CSV, dan Supplier belum memiliki tes otomatis.
- Beberapa daftar hanya menampilkan data terbaru. Riwayat perubahan status dan satu buku pergerakan stok belum tersedia.

## 10. Referensi dan Bantuan

Saya menggunakan dokumentasi Laravel, Tailwind, dan Flowbite. Pada Tailwind saya menyesuiakan dengan template yang tersedia pada Flowbite.

Claude saya gunakan sebagai teman belajar untuk memahami konsep, membantu Eksekusi pada alur yang tidak di pahami, berdiskusi soal rancangan, dan mencari penyebab error. Copilot di VS Code membantu saya meninjau/menganalisis struktur proyek dan memahami flow atau logika yang belum saya pahami. Keduanya membantu proses belajar dan penerapan.

File `CLAUDE.md`, `AGENTS.md`, `boost.json`, dan `.mcp.json` hanya konfigurasi alat bantu dari copilot, bukan bagian dari aplikasi.
