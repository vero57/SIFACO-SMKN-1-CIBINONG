# SIFACO

SIFACO adalah aplikasi absensi sekolah berbasis Laravel. Aplikasi ini membantu siswa melakukan absen, mengajukan izin, mengisi jurnal pelajaran, dan membantu guru atau admin memantau data dari dashboard.

Proyek ini juga mendukung validasi lokasi sekolah, foto absen, deteksi wajah dari sisi browser menggunakan `face-api.js`, export Excel, dan pembuatan PDF untuk laporan tertentu.

## Fitur Utama

- Absensi siswa dengan check-in dan check-out.
- Validasi radius lokasi sekolah berdasarkan koordinat di `.env`.
- Verifikasi wajah dari frontend menggunakan model `face-api.js`.
- Status absensi: Hadir, Sakit, Izin, Alfa, dan Telat.
- Poin pelanggaran otomatis untuk siswa yang terlambat.
- Pengajuan izin atau sakit dengan foto dan lampiran file.
- Jurnal pelajaran dengan deskripsi dan bukti foto.
- Dashboard admin dan guru untuk melihat absensi, izin, jurnal, pelanggaran, kelas, siswa, mapel, dan user.
- Export data ke Excel menggunakan Laravel Excel.
- Generate laporan PDF menggunakan DomPDF.
- API sederhana dengan proteksi API key.

## Teknologi

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- Laravel DomPDF
- Laravel Excel
- MySQL atau SQLite
- Node.js dan Vite
- Tailwind CSS
- Chart.js
- SweetAlert2
- face-api.js

## Struktur Folder Penting

```text
app/
  Http/Controllers/        Controller untuk landing page, dashboard, auth, dan API
  Http/Middleware/         Middleware akses role, fitur login, dan API key
  Models/                  Model Eloquent
  Exports/                 Class export Excel
  Imports/                 Class import data siswa

config/
  school.php               Konfigurasi nama sekolah, koordinat, dan radius absen
  dompdf.php               Konfigurasi PDF

database/
  migrations/              Struktur tabel database
  seeders/                 Data awal role, user, kelas, mapel, status absen, dll

public/
  faceapi/                 Library, model, dan data gambar untuk face-api.js
  js/                      JavaScript publik
  assets/                  Gambar landing page dan dashboard

resources/
  views/                   Blade template untuk landing, dashboard, auth, dan PDF
  js/                      JavaScript Vite
  css/                     CSS aplikasi

routes/
  web.php                  Menggabungkan route auth, landing, dan dashboard
  auth.php                 Login/register siswa dan login dashboard
  landing.php              Fitur siswa di halaman utama
  dashboard.php            Fitur admin dan guru
  api.php                  Endpoint API
```

## Kebutuhan Lokal

Pastikan perangkat sudah memiliki:

- PHP 8.2 atau lebih baru
- Composer
- Node.js 20 atau lebih baru
- NPM
- Database, bisa SQLite untuk lokal atau MySQL untuk penggunaan tim

Untuk Windows, XAMPP atau Laragon bisa dipakai selama versi PHP-nya sesuai.

## Instalasi

Clone repository:

```bash
git clone <url-repository>
cd absen-sija
```

Install dependency PHP:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

Salin file environment:

```bash
cp .env.example .env
```

Jika memakai PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

## Konfigurasi Environment

Sesuaikan isi `.env` sebelum menjalankan migrasi.

Contoh konfigurasi SQLite:

```env
DB_CONNECTION=sqlite
```

Lalu buat file database jika belum ada:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
```

Contoh konfigurasi MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absen_sija
DB_USERNAME=root
DB_PASSWORD=
```

Konfigurasi sekolah:

```env
SCHOOL_NAME="SMK SIJA"
SCHOOL_LAT=-6.521976890944639
SCHOOL_LNG=106.80741031694744
SCHOOL_RADIUS=100
```

Nilai `SCHOOL_LAT`, `SCHOOL_LNG`, dan `SCHOOL_RADIUS` dipakai untuk validasi lokasi absen. Jangan isi asal untuk production, karena data ini menentukan apakah siswa boleh melakukan absen atau tidak.

Jika memakai endpoint API di `routes/api.php`, tambahkan API key:

```env
MY_API_KEY=isi_api_key_yang_kuat
```

Key tersebut sudah dibaca oleh `config/app.php` melalui `my_api_key`:

```php
'my_api_key' => env('MY_API_KEY'),
```

## Migrasi dan Seeder

Jalankan migrasi dan isi data awal:

```bash
php artisan migrate --seed
```

Seeder akan membuat role, beberapa user awal, data parent, kelas, mapel, status absen, dan aturan pelanggaran.

Akun awal dari seeder:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@example.com` | `password` |
| Guru | `guru1@example.com` | `password` |
| Guru | `guru2@example.com` | `password` |
| Siswa | `siswa1@example.com` | `password` |
| Siswa | `siswa2@example.com` | `password` |

Ganti password akun seed sebelum aplikasi dipakai di lingkungan production.

## Storage Upload

Aplikasi menyimpan foto absen, foto izin, dan lampiran ke disk `public`. Jalankan:

```bash
php artisan storage:link
```

Tanpa command ini, file yang sudah ter-upload bisa tersimpan tetapi tidak bisa diakses dari browser.

## Menjalankan Aplikasi

Cara paling praktis untuk development:

```bash
composer run dev
```

Command ini menjalankan server Laravel, queue listener, log viewer, dan Vite secara bersamaan.

Jika ingin menjalankan manual:

```bash
php artisan serve
npm run dev
php artisan queue:listen --tries=1
```

Buka aplikasi di:

```text
http://127.0.0.1:8000
```

## Halaman dan Route Penting

- `/` untuk landing page.
- `/auth` untuk login dan register siswa.
- `/auth_dash` untuk login admin atau guru.
- `/dashboard` untuk dashboard setelah login.
- `/absen` untuk fitur absen siswa.
- `/izin` untuk pengajuan izin.
- `/jurnal` untuk pengisian jurnal.
- `/profile` untuk profil siswa.

Route dashboard dibatasi berdasarkan role:

- Admin bisa mengelola user, siswa, kelas, mapel, absensi, izin, jurnal, dan pelanggaran.
- Guru bisa mengakses dashboard, kelas, mapel, absensi, izin, jurnal, dan pelanggaran.
- Siswa menggunakan fitur landing seperti absen, izin, jurnal, dan profil.

## Endpoint API

Endpoint API berada di `routes/api.php` dan dilindungi header:

```http
X-API-KEY: isi_api_key
```

Endpoint yang tersedia:

- `GET /api/data-absen`
- `GET /api/users`
- `GET /api/classes`
- `GET /api/class-students`

Gunakan API key yang berbeda untuk setiap environment. Jangan commit API key asli ke repository.

## Face Recognition

File untuk face recognition ada di:

```text
public/faceapi/
```

Folder ini berisi:

- `face-api.min.js`
- model face-api.js
- daftar gambar wajah siswa
- `list.json`

Hal yang perlu dijaga:

- Nama label wajah harus konsisten dengan nama user yang dipakai aplikasi.
- Jangan simpan foto pribadi yang tidak diperlukan.
- Untuk production, pastikan izin penggunaan foto sudah jelas.
- Jika data wajah berubah, perbarui gambar dan daftar labelnya bersama-sama.

## Testing

Jalankan test:

```bash
composer test
```

Atau langsung:

```bash
php artisan test
```

Untuk format kode Laravel:

```bash
./vendor/bin/pint
```

Di Windows PowerShell:

```powershell
.\vendor\bin\pint
```

## Build Frontend

Untuk production build:

```bash
npm run build
```

Pastikan command ini berhasil sebelum deploy, karena asset frontend diproses oleh Vite.

## Best Practice Pengembangan

- Buat branch terpisah untuk setiap fitur atau bugfix.
- Jalankan `php artisan test` sebelum merge.
- Jalankan Pint sebelum commit agar style kode konsisten.
- Simpan validasi request di controller atau Form Request jika validasinya mulai panjang.
- Pakai route name saat redirect, bukan hardcode URL.
- Hindari query berat langsung di Blade. Siapkan data dari controller.
- Untuk data yang punya relasi, gunakan eager loading seperti `with()` agar tidak terjadi N+1 query.
- Jangan commit file `.env`, database lokal, cache, atau file upload pribadi.
- Jangan menaruh credential, API key, token, atau password di kode.
- Gunakan migration untuk perubahan struktur database, jangan mengubah database manual tanpa jejak.
- Tambahkan seeder jika fitur baru butuh data awal.
- Simpan file upload di disk Laravel, bukan di folder acak.
- Bersihkan pesan error agar tetap sopan dan aman untuk user.

## Catatan Deployment

Checklist umum sebelum deploy:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Pastikan web server mengarah ke folder `public`, bukan root project.

Untuk production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-aplikasi
```

Jika queue dipakai untuk notifikasi atau pekerjaan background, jalankan worker secara permanen menggunakan Supervisor, systemd, atau process manager lain:

```bash
php artisan queue:work --tries=3
```

## Troubleshooting

Jika asset tidak muncul:

```bash
npm run dev
```

atau build ulang:

```bash
npm run build
```

Jika foto upload tidak bisa dibuka:

```bash
php artisan storage:link
```

Jika konfigurasi `.env` tidak terbaca:

```bash
php artisan config:clear
```

Jika database lokal kosong:

```bash
php artisan migrate:fresh --seed
```

Gunakan `migrate:fresh` hanya untuk database development, karena command ini menghapus seluruh tabel dan membuat ulang dari awal.

## Ringkasan Alur Aplikasi

1. Siswa login melalui `/auth`.
2. Siswa melakukan absen melalui `/absen`.
3. Browser mengirim data wajah, lokasi, dan foto.
4. Server mengecek kelas, jadwal, radius sekolah, status izin, dan status keterlambatan.
5. Data absensi disimpan ke database.
6. Jika telat, sistem menambahkan poin pelanggaran.
7. Admin atau guru memantau data dari `/dashboard`.

Dokumentasi ini mengikuti kondisi kode saat ini. Jika ada perubahan besar pada route, role, struktur database, atau cara face recognition bekerja, perbarui README di commit yang sama agar pengembang berikutnya tidak perlu menebak-nebak.
