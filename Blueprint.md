# Blueprint: Sistem Booking Barbershop ("BarberBook")

> **Update:** versi ini menggunakan pendekatan **web-only (PWA)** dengan stack **Laravel + MySQL**. Pelanggan booking langsung lewat browser (tanpa install app), dengan opsi "Add to Home Screen" agar tetap ada ikon di HP.

## 1. Ringkasan Project

Aplikasi booking online untuk usaha potong rambut/barbershop, dibangun sebagai **satu aplikasi Laravel (monolith)** dengan tampilan Progressive Web App (PWA), mencakup:

- **Halaman pelanggan** — booking layanan, pilih kapster, lihat riwayat (bisa diakses dari link WA/IG/QR code, tanpa install)
- **Dashboard admin** — kelola jadwal, kapster, layanan, laporan

Tujuan: menggantikan sistem booking manual via WhatsApp/telepon yang rawan bentrok jadwal dan tidak punya data pelanggan, tanpa membebani pelanggan dengan proses install aplikasi, dan dengan stack yang familiar & gampang di-deploy di hosting PHP umum di Indonesia.

## 2. Masalah yang Diselesaikan

- Booking manual via chat sering bentrok jadwal & makan waktu admin
- Tidak ada reminder otomatis → banyak pelanggan _no-show_
- Owner tidak punya data histori pelanggan untuk re-marketing
- Sulit tahu jam ramai/sepi dan kapster mana yang paling laris
- **Friksi install app** untuk aplikasi yang jarang dipakai (booking potong rambut biasanya bulanan) — diselesaikan dengan pendekatan web/PWA

## 3. Target Pengguna

| Peran       | Kebutuhan Utama                                                    |
| ----------- | ------------------------------------------------------------------ |
| Pelanggan   | Booking cepat tanpa install, pilih kapster favorit, dapat reminder |
| Owner/Admin | Kontrol jadwal, lihat laporan, kelola kapster & layanan            |
| Kapster     | Lihat jadwal booking harian miliknya                               |

## 4. Ruang Lingkup Fitur

### MVP (Fase 1 — target ±3 bulan)

**Halaman Pelanggan (Web/PWA)**

- Booking tanpa perlu login wajib (opsional: login dengan nomor HP untuk lihat riwayat)
- Pilih layanan (potong rambut, cukur jenggot, hair spa, dll — beda harga & durasi)
- Pilih kapster (opsional, atau "siapa saja")
- Kalender & slot waktu (dicek via AJAX ke server saat pilih tanggal)
- Konfirmasi booking otomatis
- Notifikasi WhatsApp: konfirmasi + reminder H-1
- Riwayat booking + tombol "booking ulang" (jika login)
- Prompt "Add to Home Screen" setelah booking pertama berhasil

**Dashboard Admin (Web)**

- Login admin
- Kelola daftar layanan & harga
- Kelola data kapster (nama, foto, jadwal kerja/shift, hari libur)
- Kalender semua booking (per hari/per kapster)
- Tambah booking manual (untuk walk-in customer)
- Laporan pendapatan harian/bulanan & per kapster
- Data pelanggan (nama, kontak, riwayat kunjungan)

### Fase 2 (Pengembangan Lanjutan)

- Pembayaran DP online (Midtrans/Xendit — keduanya punya SDK resmi Laravel/PHP)
- Program loyalty/poin
- Multi-cabang
- Rating & review per kapster
- Galeri hasil potongan (before-after)
- Analitik jam ramai & layanan terlaris
- Realtime update slot pakai Laravel Echo + Pusher/Soketi (kalau traffic sudah cukup ramai)
- Panel khusus kapster (lihat jadwal sendiri, tandai selesai)

## 5. Alur Pengguna (User Flow)

**Alur Booking Pelanggan:**

```
Klik link (dari WA/IG bio/QR code meja) → Buka langsung di browser (tanpa install)
→ Pilih Layanan → Pilih Kapster (opsional)
→ Pilih Tanggal & Jam (slot dicek via AJAX ke server)
→ Isi nama & nomor HP → Konfirmasi Booking
→ Terima notifikasi WA konfirmasi
→ (H-1) Terima notifikasi WA reminder → Datang ke barbershop
→ (opsional) Muncul prompt "Tambahkan ke Layar Utama" untuk akses lebih cepat lain kali
```

**Alur Admin:**

```
Login → Lihat kalender booking hari ini → Kelola jadwal kapster
→ Tambah booking manual jika ada walk-in → Lihat laporan pendapatan
```

## 6. Tech Stack (Prioritas: Mudah Deploy & Maintenance)

| Layer                    | Teknologi                                                                        | Alasan                                                                                                                             |
| ------------------------ | -------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- |
| Backend + Web (Monolith) | **Laravel** (Blade + Alpine.js) + **Tailwind CSS**                               | Satu framework urus routing, auth, business logic, & tampilan sekaligus — lebih sederhana daripada split frontend/backend terpisah |
| Database                 | **MySQL**                                                                        | Native didukung Eloquent ORM Laravel, hampir semua hosting PHP di Indonesia sudah sediakan ini                                     |
| Auth                     | **Laravel Breeze**                                                               | Login/register siap pakai, tidak perlu setup dari nol                                                                              |
| PWA Layer                | Web App Manifest + Service Worker (manual atau paket `laravel-pwa`)              | Web tetap bisa "Add to Home Screen" walau berbasis Laravel monolith                                                                |
| Cek Slot Booking         | AJAX request ke Laravel Controller + validasi server-side                        | Cukup untuk skala barbershop (tidak perlu websocket); mencegah double-booking lewat validasi saat submit                           |
| Notifikasi WhatsApp      | **Fonnte API**                                                                   | Reminder & konfirmasi paling efektif, tidak bergantung browser/OS pelanggan                                                        |
| Scheduler Reminder       | **Laravel Task Scheduling** + Queue                                              | Bawaan Laravel, cukup 1 baris di `Kernel.php` untuk jadwalkan reminder H-1                                                         |
| Hosting                  | **Shared hosting PHP** (Niagahoster/Hostinger, dll) atau **VPS + Laravel Forge** | Laravel gampang di-deploy ke hosting PHP umum & murah di Indonesia; Forge kalau butuh setup lebih rapi & auto-deploy dari Git      |

> Dengan Laravel + MySQL, project ini jadi **monolith sederhana** — cocok kalau kamu lebih familiar dengan PHP, dan deployment-nya bisa lewat hosting PHP biasa yang banyak dipakai klien lokal (tidak wajib platform khusus seperti Vercel/Railway).

## 7. Arsitektur Sistem (Gambaran Umum)

```
   [Browser Pelanggan]            [Browser Admin]
   (Web/PWA - bisa "Add             (Web - halaman
    to Home Screen")                 /admin/*)
          |                               |
          └────────────→ Laravel App ←────┘
                    (Controllers, Blade Views,
                     Auth, Business Logic)
                              |
                          MySQL DB
                              |
                   Fonnte API (Notifikasi WhatsApp)
```

- Satu aplikasi Laravel menangani route pelanggan (`/booking`) dan admin (`/admin/*`), dipisah lewat middleware `auth` + role
- Service Worker (untuk PWA) menangani caching aset statis & "Add to Home Screen"
- Cek ketersediaan slot lewat endpoint AJAX (`/api/kapsters/{id}/available-slots`), divalidasi ulang di server saat booking disimpan
- Laravel Scheduler menjalankan job harian untuk kirim reminder WA (H-1) lewat Fonnte

## 8. Skema Database (ERD Sederhana — Konvensi Laravel/Eloquent)

**users** (pelanggan)

- id (PK)
- name
- phone
- email (nullable)
- password (nullable, jika pakai login opsional)
- created_at, updated_at

**admins**

- id (PK)
- name
- email
- password
- role
- created_at, updated_at

**kapsters**

- id (PK)
- name
- phone
- photo_url
- is_active (boolean)
- created_at, updated_at

**kapster_schedules** (jadwal kerja per kapster)

- id (PK)
- kapster_id (FK → kapsters)
- day_of_week
- start_time
- end_time

**services**

- id (PK)
- name
- price
- duration_minutes

**bookings**

- id (PK)
- customer_id (FK → users)
- kapster_id (FK → kapsters)
- service_id (FK → services)
- booking_date
- booking_time
- status (enum: pending / confirmed / completed / cancelled)
- created_at, updated_at

**reviews** (Fase 2)

- id (PK)
- booking_id (FK → bookings)
- rating
- comment
- created_at

## 9. Contoh Route Utama (Laravel)

**Route Pelanggan (Web)**

- `GET /booking` — halaman booking pelanggan
- `GET /api/kapsters/{id}/available-slots?date=` — AJAX, cek slot kosong
- `POST /booking` — simpan booking baru
- `GET /riwayat` — riwayat booking (jika login)

**Route Admin (prefix `/admin`, middleware `auth`)**

- `GET /admin/dashboard` — kalender booking hari ini
- `GET /admin/bookings` — daftar semua booking
- `POST /admin/bookings` — tambah booking manual (walk-in)
- `PATCH /admin/bookings/{id}` — update status booking
- `GET /admin/reports?range=` — laporan pendapatan
- `GET|POST /admin/kapsters` — CRUD kapster
- `GET|POST /admin/services` — CRUD layanan

**Command/Job**

- `php artisan schedule:run` (dijalankan via cron di hosting) → trigger job reminder H-1 → panggil Fonnte API

## 10. Rencana Deployment (Step-by-Step)

1. **Setup project**: `composer create-project laravel/laravel barberbook`, install Laravel Breeze untuk auth
2. **Buat migration & model** sesuai skema di atas, jalankan `php artisan migrate`
3. **Bangun halaman booking** (Blade + Alpine.js untuk interaktivitas kalender/slot + Tailwind untuk styling)
4. **Bangun dashboard admin** (Blade, dengan middleware role admin)
5. **Tambahkan PWA support**: buat `manifest.json` + `sw.js` (service worker), daftarkan di layout utama
6. **Setup Fonnte**: daftar akun, hubungkan nomor WA, simpan API token di `.env`
7. **Setup Scheduler**: buat Artisan Command untuk reminder H-1, daftarkan di `routes/console.php` atau `Kernel.php`
8. **Pilih hosting & deploy**:
   - Opsi termudah: shared hosting PHP (Niagahoster/Hostinger) — upload lewat Git/File Manager, set `.env`, jalankan migration
   - Opsi lebih rapi: VPS + Laravel Forge (auto-deploy dari GitHub, SSL otomatis)
9. **Setup cron job** di hosting untuk `php artisan schedule:run` tiap menit (dibutuhkan untuk reminder otomatis)
10. **Testing end-to-end**: booking dari HP → cek prompt "Add to Home Screen" → notifikasi WA masuk → tampil di dashboard admin
11. **Demo ke calon klien** dengan data dummy sebelum onboarding barbershop asli

## 11. Timeline Pengembangan (Estimasi Part-Time)

| Minggu | Fokus                                                                   |
| ------ | ----------------------------------------------------------------------- |
| 1–2    | Wireframe UI + migration & model database (MySQL)                       |
| 3–4    | Setup Auth (Breeze) + CRUD layanan & kapster (admin dasar)              |
| 5–6    | Logic booking, cek slot via AJAX, validasi anti-bentrok jadwal          |
| 7–8    | Halaman booking pelanggan (mobile-responsive) + dashboard admin lengkap |
| 9      | Tambah PWA layer (manifest, service worker, "Add to Home Screen")       |
| 10     | Integrasi notifikasi WhatsApp (Fonnte) + scheduler reminder H-1         |
| 11     | Testing lintas device/browser, polish UI, setup hosting & deploy        |
| 12     | Siapkan materi demo & studi kasus untuk klien                           |

---

_Blueprint ini adalah dokumen hidup — sesuaikan skema, fitur, dan timeline seiring berjalannya development._
