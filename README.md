# Smart EduHub — Sistem Informasi Sekolah (SIS)

![logo](docs/bukti/logo/SEH.png)

> **Tim Smart EduHUb** — Lomba Direktorat Jenderal Pendidikan Vokasi

Sistem Informasi Sekolah berbasis web yang mengelola aktivitas akademik dan non-akademik sebuah SMK (Smart EduHub), mulai dari **Pendaftaran Anggota/Siswa Baru di Sistem**, **absensi QR code**, **e-learning (modul & tugas)**, **ujian online**, **simpan-pinjam digital (kantin cashless)**, hingga **portal siswa & orang tua/wali**.

---

## Daftar Isi
1. [Nama dan Deskripsi Produk](#1-nama-dan-deskripsi-produk)
2. [Alur Inti](#2-alur-inti)
3. [Cara Menjalankan (How to Run)](#3-cara-menjalankan-how-to-run)
4. [Tangkapan Layar Alur Inti](#4-tangkapan-layar-alur-inti)
5. [Cara Menjalankan Pengujian](#5-cara-menjalankan-pengujian)
6. [Fitur dan Batasan](#6-fitur-dan-batasan)
7. [Struktur Folder](#7-struktur-folder)
8. [Tim Pengembang](#8-tim-pengembang)


## 1. Nama dan Deskripsi Produk

**Nama produk:** Smart EduHub

**Apa yang dilakukan website ini:**
Smart EduHub adalah sistem informasi manajemen sekolah yang mengintegrasikan berbagai kebutuhan administrasi sekolah dalam satu platform. Sistem ini menangani:

- **Pendaftaran anggota/siswa baru** — formulir pendaftaran siswa baru di sistem beserta pelacakan status.
- **Manajemen data siswa & guru** — data master, angkatan, kartu siswa (dengan QR).
- **Absensi** — input manual, bulk, dan scan QR code berbasis web.
- **Poin perilaku & leaderboard** — pemberian poin prestasi/pelanggaran beserta aturan poin.
- **E-learning** — mata pelajaran, modul, soal, dan pengumpulan tugas.
- **Ujian online** — bank soal, ujian, dan rekap nilai.
- **Keuangan / Simpan Pinjam Digital** — kartu elektronik siswa, top-up, tarik tunai, transfer antar siswa, pembayaran, dan rekap transaksi (cashless).
- **Izin siswa** — pengajuan dan persetujuan izin.
- **Pengaduan & notifikasi** — pengaduan siswa/orang tua dan notifikasi untuk semua peran.
- **Portal siswa & portal orang tua/wali** — dashboard mandiri sesuai peran.

**Untuk siapa website ini:**
- **Admin sekolah** — mengelola seluruh master data, pendaftaran siswa baru, keuangan, dan laporan.
- **Guru / staf** — mengelola absensi, poin, modul, nilai, dan izin.
- **Teller** — staf keuangan yang menangani operasional harian: top-up saldo, tarik tunai, pembayaran, pengelolaan kartu, dan persetujuan request kartu. Landing page: dashboard keuangan.
- **BUMS (Badan Usaha Milik Sekolah)** — staf yang mengawasi transaksi keuangan dan rekap pembayaran untuk unit usaha sekolah. Landing page: riwayat pembayaran.
- **Siswa** — melihat profil, absensi, poin, mengikuti modul/ujian, mengajukan izin, menggunakan dompet digital, melihat nilai, dan mengirim pengaduan.
- **Orang tua / wali** — memantau perkembangan anak (absensi, poin, keuangan, izin, notifikasi) dan mengajukan izin/pengaduan.
- **Publik / calon siswa** — mendaftar sebagai siswa baru dan mengecek status pendaftaran.

---

## 2. Alur Inti

Berikut adalah langkah-langkah yang dilakukan pengguna dari awal sampai akhir untuk alur utama **pendaftaran siswa baru hingga siswa aktif menggunakan sistem**.

### Alur 1 — Pendaftaran Anggota/Siswa Baru di Sistem

1. **Calon siswa membuka landing page** di alamat utama (`/`), lalu klik menu pendaftaran.
2. **Mengisi formulir pendaftaran** di `/daftar` (data pribadi, sekolah asal, orang tua, dll.) lalu mengirim.
3. **Mendapatkan ID pendaftaran** dan dapat **mengecek status** pendaftaran di `/cek-status` menggunakan ID.
4. **Admin meninjau pendaftaran** di menu Registrasi (`/registrations`), memverifikasi berkas, lalu **mengubah status** (pending → verified / accepted / rejected).
5. **Calon siswa mengunduh informasi hasil seleksi** di `/download-info`.
6. Jika diterima, **data pendaftar dikonversi menjadi data siswa** oleh admin, dan siswa mendapatkan akun portal siswa (username + PIN).

### Alur 2 — Siswa Menggunakan Portal

1. **Siswa login** di `/siswa/login` menggunakan `username` dan `PIN`.
2. **Melihat dashboard** — ringkasan absensi, poin, keuangan, dan notifikasi.
3. **Belajar (e-learning)** — pilih mata pelajaran → buka modul → baca materi → kerjakan soal/tugas → kirim.
4. **Mengikuti ujian** — buka ujian dari daftar → jawab soal → submit → lihat nilai.
5. **Transaksi keuangan** — request kartu, top-up (oleh admin), transfer antar siswa, bayar, lihat riwayat.
6. **Mengajukan izin / mengirim pengaduan** sesuai kebutuhan.
7. **Logout**.

### Alur 3 — Absensi dengan QR Code

1. Admin/guru membuka halaman **scan absensi** (`/attendance-scan`).
2. Siswa menunjukkan **kartu QR** (dicetak dari menu siswa).
3. Sistem **memindai QR** dan mencatat kehadiran (hadir/izin/sakit/alpa) beserta waktu check-in.
4. Admin/guru dapat melihat **laporan absensi** (`/attendance/report`) dan melakukan **input bulk** jika diperlukan.

### Alur 4 — Orang Tua/Wali

1. **Orang tua mendaftar** di `/wali/register` (dihubungkan ke data siswa) lalu **login** di `/wali/login`.
2. **Melihat dashboard** — ringkasan data anak.
3. **Memantau absensi, poin, keuangan, izin, dan notifikasi** anak.
4. **Mengajukan izin / pengaduan** atas nama anak.
5. **Logout**.

---

## 3. Cara Menjalankan (How to Run)

### Prasyarat

| Prasyarat | Versi | Keterangan |
|-----------|-------|------------|
| PHP | ^8.2 | (minimal 8.2, rekomendasi 8.3) |
| Composer | 2.x | manajer dependensi PHP |
| Node.js | 20.x | (atau yang kompatibel dengan Vite 7) |
| NPM | 10.x | menyertai Node.js |
| MySQL | 8.0 | database utama (atau SQLite untuk pengujian) |
| Browser | Chrome / Edge / Firefox terbaru | untuk mengakses aplikasi |

> Aplikasi dikembangkan dengan **Laravel 12**, **Tailwind CSS v4**, **Vite 7**, dan menggunakan **MySQL** sebagai database utama (atau SQLite untuk testing).

### 1. Clone Repository

```bash
git clone <URL_REPOSITORY> lomba_direktorat
cd lomba_direktorat
```

### 2. Masuk ke folder aplikasi

```bash
cd smart-acp
```

### 3. Instalasi dependensi

```bash
composer install
npm install
```

### 4. Konfigurasi environment

Salin file `.env.example` menjadi `.env`:

```bash
copy .env.example .env
```

Sesuaikan konfigurasi database di file `.env`:

```env
APP_NAME="Smart EduHub"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart-edu
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `smart-edu` di MySQL terlebih dahulu.

> Jika ingin menggunakan SQLite (lebih cepat untuk demo), set `DB_CONNECTION=sqlite` dan buat file `database/database.sqlite`.

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Migrasi database & seeding data awal

```bash
php artisan migrate --seed
```

> Perintah `--seed` akan menjalankan `DatabaseSeeder` yang membuat akun admin, data angkatan, dan siswa contoh.

### 7. Build aset frontend

```bash
npm run build
```

### 8. Menjalankan aplikasi

```bash
php artisan serve
```

Akses di browser:

- **Landing page:** <http://localhost:8000>
- **Login Admin/Guru:** <http://localhost:8000/login>
- **Login Siswa:** <http://localhost:8000/siswa/login>
- **Login Orang Tua/Wali:** <http://localhost:8000/wali/login>
- **Pendaftaran Siswa Baru:** <http://localhost:8000/daftar>
- **Cek Status Pendaftaran:** <http://localhost:8000/cek-status>
- **Scan Absensi:** <http://localhost:8000/attendance-scan>

### Akun / Data Contoh

Akun berikut dibuat otomatis melalui `DatabaseSeeder` dan `StudentSeeder`:

| Peran | Username / Email | Password / PIN |
|-------|------------------|----------------|
| **Admin** | `admin@smartaxioo.com` (username: `admin`) | `password` |
| **Siswa** | `siswa123` | PIN: `123456` |

> Akun guru/staf (termasuk **Teller** dan **BUMS**) dapat dibuat oleh admin melalui menu **Management Users** setelah login sebagai admin. Pilih role `teller` atau `bums` saat membuat user. Akun orang tua/wali dapat didaftarkan sendiri melalui `/wali/register` atau dikelola admin di menu **Parents**.

---

## 4. Tangkapan Layar Alur Inti

Tangkapan layar untuk setiap langkah alur inti tersedia di folder `docs/bukti/Screenshot/`.

**Alur Pendaftaran Siswa Baru:**
- Halaman utama Smart EduHub

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Smart EduHub Web](<docs/bukti/Screenshot/Smart EduHub.png>) | ![Smart EduHub Mobile](<docs/bukti/Screenshot/Smart EduHub (1).png>) |

- Formulir pendaftaran siswa baru

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Formulir Pendaftaran Web](<docs/bukti/Screenshot/Pendaftaran Siswa Baru - Smart Edu Hub (1).png>) | ![Formulir Pendaftaran Mobile](<docs/bukti/Screenshot/Pendaftaran Siswa Baru - Smart Edu Hub.png>) |

- Halaman pelacakan / proses pendaftaran

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Proses Pendaftaran Web](<docs/bukti/Screenshot/Proses Pendaftaran - Smart Edu Hub.png>) | ![Proses Pendaftaran Mobile](<docs/bukti/Screenshot/Proses Pendaftaran - Smart Edu Hub (1).png>) |

- Dashboard admin mengelola pendaftaran

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Student Registrations Web](<docs/bukti/Screenshot/Smart Edu Hub - Student Registrations.png>) | ![Student Registrations Mobile](<docs/bukti/Screenshot/Smart Edu Hub - Student Registrations (1).png>) |

**Alur Portal Siswa dan E-Learning:**
- Halaman login siswa

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Login Siswa Web](<docs/bukti/Screenshot/Login Siswa - Smart EduHub (1).png>) | ![Login Siswa Mobile](<docs/bukti/Screenshot/Login Siswa - Smart EduHub.png>) |

- Dashboard siswa

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Dashboard Siswa Web](<docs/bukti/Screenshot/Dashboard Siswa - Smart Edu Hub.png>) | ![Dashboard Siswa Mobile](<docs/bukti/Screenshot/Dashboard Siswa - Smart Edu Hub (1).png>) |

- Halaman kursus siswa

| Tampilan Web |
| :---: |
| ![Kursus Saya Web](<docs/bukti/Screenshot/Kursus Saya - Smart Edu Hub.png>) |

- Halaman modul dan materi e-learning

| Tampilan Mobile |
| :---: |
| ![Modul dan Materi Mobile](<docs/bukti/Screenshot/Modul & Materi - Smart Edu Hub.png>) |

- Halaman keuangan / dompet digital siswa

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Keuangan Siswa Web](<docs/bukti/Screenshot/Keuangan - Smart Edu Hub.png>) | ![Keuangan Siswa Mobile](<docs/bukti/Screenshot/Keuangan - Smart Edu Hub (1).png>) |

- Halaman transfer saldo

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Transfer Saldo Web](<docs/bukti/Screenshot/Transfer Saldo - Smart Edu Hub (1).png>) | ![Transfer Saldo Mobile](<docs/bukti/Screenshot/Transfer Saldo - Smart Edu Hub.png>) |

**Alur Absensi QR:**
- Halaman terminal scan absensi QR code

| Tampilan Web |
| :---: |
| ![Attendance Terminal Web](<docs/bukti/Screenshot/Attendance Terminal - Smart Edu Hub.png>) |

- Kartu siswa dengan QR

| Tampilan Web |
| :---: |
| ![Kartu Siswa Web](<docs/bukti/Screenshot/Cetak Kartu Siswa - Smart Edu Hub.png>) |

- Laporan absensi bulanan

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Laporan Absensi Web](<docs/bukti/Screenshot/Smart Edu Hub - Laporan Absensi Bulanan (1).png>) | ![Laporan Absensi Mobile](<docs/bukti/Screenshot/Smart Edu Hub - Laporan Absensi Bulanan (2).png>) |

**Alur Orang Tua/Wali:**
- Halaman login orang tua/wali

| Tampilan Web |
| :---: |
| ![Login Wali Web](<docs/bukti/Screenshot/Login Wali Murid - Smart EduHub.png>) |

- Dashboard orang tua/wali

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Dashboard Wali Web](<docs/bukti/Screenshot/Dashboard Wali Murid - Smart Edu Hub.png>) | ![Dashboard Wali Mobile](<docs/bukti/Screenshot/Dashboard Wali Murid - Smart Edu Hub (1).png>) |

- Presensi anak

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Presensi Anak Web](<docs/bukti/Screenshot/Presensi Anak - Smart Edu Hub (1).png>) | ![Presensi Anak Mobile](<docs/bukti/Screenshot/Presensi Anak - Smart Edu Hub.png>) |

**Alur Modul Keuangan:**
- Dashboard keuangan

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Dashboard Keuangan Web](<docs/bukti/Screenshot/Smart Edu Hub - (1).png>) | ![Dashboard Keuangan Mobile](<docs/bukti/Screenshot/Smart Edu Hub -.png>) |

- Pengajuan kartu

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Pengajuan Kartu Web](<docs/bukti/Screenshot/Smart Edu Hub - Pengajuan Kartu.png>) | ![Pengajuan Kartu Mobile](<docs/bukti/Screenshot/Smart Edu Hub - Pengajuan Kartu (1).png>) |

- Daftar kartu

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Daftar Kartu Web](<docs/bukti/Screenshot/Smart Edu Hub - Daftar Kartu (1).png>) | ![Daftar Kartu Mobile](<docs/bukti/Screenshot/Smart Edu Hub - Daftar Kartu.png>) |

- Rekap laporan keuangan

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Rekap Laporan Keuangan Web](<docs/bukti/Screenshot/Smart Edu Hub - Rekap Laporan Keuangan.png>) | ![Rekap Laporan Keuangan Mobile](<docs/bukti/Screenshot/Smart Edu Hub - Rekap Laporan Keuangan (1).png>) |

- Bukti transaksi top-up

| Tampilan Mobile |
| :---: |
| ![Bukti Pembayaran Mobile](<docs/bukti/Screenshot/bukti_pembayaran.png>) |

**Alur Dashboard Admin:**
- Data siswa

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Data Siswa Web](<docs/bukti/Screenshot/Smart Edu Hub - Data Siswa (1).png>) | ![Data Siswa Mobile](<docs/bukti/Screenshot/Smart Edu Hub - Data Siswa.png>) |

- Manajemen user

| Tampilan Web | Tampilan Mobile |
| :---: | :---: |
| ![Management User Web](<docs/bukti/Screenshot/Smart Edu Hub - Management User.png>) | ![Management User Mobile](<docs/bukti/Screenshot/Smart Edu Hub - Management User (1).png>) |
---

## 5. Cara Menjalankan Pengujian

Aplikasi menggunakan **PHPUnit** (bawaan Laravel) untuk pengujian. Konfigurasi testing menggunakan database **SQLite in-memory** (`:memory:`) sehingga tidak mengganggu database utama.

### Perintah menjalankan test

```bash
php artisan test
```

Atau menggunakan composer script:

```bash
composer test
```

### Hasil yang diharapkan

```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

  Tests:    2 passed (2 assertions)
  Duration: 0.xx s
```

- `Tests\Unit\ExampleTest` — pengujian unit dasar (assert true).
- `Tests\Feature\ExampleTest` — memastikan halaman utama (`/`) mengembalikan status HTTP **200 OK**.

Semua test harus **PASSED**. Jika ada test yang gagal, periksa konfigurasi environment testing dan pastikan dependensi sudah terinstal.

---

## 6. Fitur dan Batasan

### Fitur yang sudah bisa dilakukan

- ✅ **Pendaftaran anggota/siswa baru** — formulir pendaftaran, cek status, verifikasi & persetujuan oleh admin, unduh informasi hasil.
- ✅ **Manajemen siswa & guru** — CRUD, impor/ekspor Excel & PDF, cetak kartu siswa (QR), reset PIN.
- ✅ **Manajemen angkatan & tahun ajaran** — multi tahun ajaran, pengaturan tahun aktif.
- ✅ **Absensi** — input manual, bulk, scan QR code, laporan absensi.
- ✅ **Poin perilaku** — pemberian poin, aturan poin, leaderboard.
- ✅ **E-learning** — mata pelajaran, modul (materi + soal), pengumpulan & penilaian tugas, progress belajar.
- ✅ **Ujian online** — bank soal, ujian, rekap & ekspor nilai (Excel/PDF).
- ✅ **Keuangan / Simpan Pinjam Digital** — kartu elektronik, top-up, tarik tunai, transfer antar siswa (PIN), pembayaran, rekap & ekspor. Dapat diakses oleh **Admin, Teller, dan BUMS** dengan izin keuangan yang sama.
- ✅ **Izin siswa** — pengajuan oleh siswa/orang tua, persetujuan oleh admin/guru.
- ✅ **Pengaduan & notifikasi** — pengaduan siswa/orang tua, notifikasi per peran.
- ✅ **Portal siswa** & **portal orang tua/wali** — dashboard mandiri.
- ✅ **Hari libur** & **pengaturan sistem** — backup & reset data.

### Batasan / yang belum bisa dilakukan

- ⚠️ **Pembayaran gateway otomatis** belum terintegrasi (top-up masih manual oleh admin).
- ⚠️ **Notifikasi real-time** masih berbasis polling/refresh halaman, belum menggunakan WebSocket.
- ⚠️ **Chat/diskusi** antar siswa-guru dalam modul belum tersedia.
- ⚠️ **Kalender akademik** terintegrasi penuh belum ada (baru hari libur).
- ⚠️ **Akses mobile native** hanya responsif web, belum ada aplikasi mobile.
- ⚠️ Beberapa fitur lanjutan seperti **penjadwalan pelajaran otomatis** dan **analitik prediktif** belum diimplementasikan.

---

## 7. Struktur Folder

```
lomba_direktorat/
├── README.md                    ← Dokumentasi utama (file ini)
├── docs/
│   └── bukti/                   ← Tangkapan layar alur inti
└── smart-acp/                   ← Aplikasi Laravel (kode utama)
    ├── app/
    │   ├── Console/
    │   │   └── Commands/
    │   │       └── MarkAbsentStudents.php   ← Command tandai siswa tidak hadir otomatis
    │   ├── Exports/              ← Ekspor data ke Excel/PDF
    │   │   ├── ExamQuestionsExport.php
    │   │   ├── ExamQuestionsTemplateExport.php
    │   │   ├── ExamResultsExport.php
    │   │   ├── FinanceReportExport.php
    │   │   ├── FinanceStudentExport.php
    │   │   ├── RaportTemplateExport.php
    │   │   ├── StudentsExport.php
    │   │   └── StudentsTemplateExport.php
    │   ├── Http/
    │   │   ├── Controllers/      ← Logika bisnis tiap fitur
    │   │   │   ├── AuthController.php              ← Login/logout admin & guru, profile
    │   │   │   ├── ParentAuthController.php        ← Login/register orang tua
    │   │   │   ├── StudentPortalController.php     ← Portal siswa (login, dashboard, modul, nilai)
    │   │   │   ├── ParentPortalController.php      ← Portal orang tua
    │   │   │   ├── DashboardController.php         ← Dashboard admin & guru
    │   │   │   ├── StudentController.php           ← CRUD data siswa, cetak kartu QR
    │   │   │   ├── ManagementUserController.php    ← CRUD user guru/staf
    │   │   │   ├── ParentController.php            ← CRUD data orang tua
    │   │   │   ├── RegistrationController.php      ← Pendaftaran siswa baru & status
    │   │   │   ├── AttendanceController.php        ← Absensi (manual, bulk, scan QR, laporan)
    │   │   │   ├── PointController.php             ← Poin perilaku & leaderboard
    │   │   │   ├── PointRuleController.php         ← Aturan pemberian poin
    │   │   │   ├── CourseController.php            ← Mata pelajaran & modul e-learning
    │   │   │   ├── ExamController.php              ← Bank soal & ujian online
    │   │   │   ├── StudentPermitController.php     ← Izin siswa
    │   │   │   ├── RaportIndustriController.php    ← Raport industri (capaian & sertifikasi)
    │   │   │   ├── StudentFinanceController.php    ← Keuangan sisi siswa (kartu, transfer)
    │   │   │   ├── AdminFinanceController.php      ← Keuangan sisi admin (topup, rekap)
    │   │   │   ├── ComplaintController.php         ← Pengaduan siswa & orang tua
    │   │   │   ├── StudentNotificationController.php  ← Notifikasi ke siswa
    │   │   │   ├── AdminNotificationController.php    ← Notifikasi ke admin
    │   │   │   ├── AngkatanController.php          ← Manajemen angkatan
    │   │   │   ├── HolidayController.php           ← Hari libur
    │   │   │   ├── SettingsController.php          ← Pengaturan sistem (tahun ajaran, backup)
    │   │   │   └── ConfigController.php            ← Endpoint config untuk perangkat scanner
    │   │   └── Middleware/
    │   │       ├── CheckMaintenanceMode.php        ← Cek mode maintenance
    │   │       └── SetLocale.php                   ← Set bahasa (id/en/ar)
    │   ├── Imports/              ← Impor data dari Excel
    │   │   ├── ExamQuestionsImport.php
    │   │   ├── RaportImport.php
    │   │   └── StudentsImport.php
    │   ├── Models/               ← Model Eloquent (representasi tabel DB)
    │   │   ├── User.php, ManagementUser.php        ← User admin & guru
    │   │   ├── Student.php, ParentUser.php         ← Data siswa & orang tua
    │   │   ├── Angkatan.php, AcademicYear.php
    │   │   ├── AcademicYearStudent.php
    │   │   ├── Registration.php                    ← Data pendaftar siswa baru
    │   │   ├── Attendance.php                      ← Data kehadiran
    │   │   ├── Point.php, PointRule.php            ← Poin & aturan poin
    │   │   ├── Course.php, Module.php, ModuleProgress.php, ModuleQuestion.php
    │   │   ├── AssignmentSubmission.php            ← Pengumpulan tugas
    │   │   ├── ExamQuestion.php, ExamResult.php    ← Soal & hasil ujian
    │   │   ├── StudentPermit.php                   ← Izin siswa
    │   │   ├── RaportIndustri.php, RaportCapaianPembelajaran.php, RaportUjiSertifikasi.php
    │   │   ├── StudentCard.php, StudentCardRequest.php, StudentCardTransaction.php  ← Keuangan
    │   │   ├── Complaint.php                       ← Pengaduan
    │   │   ├── StudentNotification.php, ParentNotification.php, AdminNotification.php
    │   │   ├── Holiday.php
    │   │   └── Setting.php                         ← Pengaturan global
    │   ├── Observers/            ← Observer model (event-driven)
    │   │   ├── ComplaintObserver.php
    │   │   ├── PointObserver.php
    │   │   ├── StudentCardTransactionObserver.php
    │   │   └── StudentObserver.php
    │   └── Providers/
    │       └── AppServiceProvider.php
    ├── bootstrap/                ← Bootstrap aplikasi Laravel
    ├── config/                   ← Konfigurasi aplikasi
    │   ├── app.php, auth.php, database.php
    │   ├── mail.php, queue.php, session.php
    │   ├── cache.php, filesystems.php, logging.php
    │   └── services.php
    ├── database/
    │   ├── migrations/           ← Skema tabel database (+/- 100 file migrasi)
    │   ├── seeders/              ← Data awal
    │   │   ├── DatabaseSeeder.php     ← Seeder utama (admin, tahun ajaran)
    │   │   ├── AngkatanSeeder.php     ← Data angkatan default
    │   │   ├── StudentSeeder.php      ← Akun siswa contoh (siswa123)
    │   │   ├── PointRulesSeeder.php   ← Aturan poin default
    │   │   └── SchoolSeeder.php
    │   └── factories/
    │       └── UserFactory.php
    ├── lang/                     ← File terjemahan
    │   ├── id.json               ← Bahasa Indonesia
    │   ├── en.json               ← Bahasa Inggris
    │   └── ar.json               ← Bahasa Arab
    ├── public/                   ← Aset publik yang dapat diakses langsung
    │   ├── index.php             ← Entry point aplikasi
    │   ├── favicon.ico
    │   ├── ab_acb/               ← Endpoint untuk perangkat scanner absensi
    │   │   ├── config.php
    │   │   ├── fetch_settings.php
    │   │   ├── fetch_students.php
    │   │   └── save_attendance.php
    │   ├── images/               ← Gambar logo, banner, dokumentasi
    │   └── storage/              ← File upload (kartu, kursus, poin)
    │       ├── card-requests/
    │       ├── courses/
    │       └── points/
    ├── resources/
    │   ├── css/
    │   │   └── app.css
    │   ├── js/
    │   │   ├── app.js
    │   │   └── bootstrap.js
    │   └── views/                ← Template Blade (antarmuka)
    │       ├── welcome.blade.php          ← Landing page publik
    │       ├── dashboard.blade.php        ← Dashboard admin
    │       ├── dashboard-guru.blade.php   ← Dashboard guru
    │       ├── layouts/                   ← Layout master
    │       │   ├── app.blade.php          ← Layout admin/guru
    │       │   ├── siswa.blade.php        ← Layout portal siswa
    │       │   ├── parent.blade.php       ← Layout portal orang tua
    │       │   └── auth.blade.php         ← Layout halaman login
    │       ├── auth/                      ← Login admin/guru, lupa/reset password, profile
    │       ├── siswa/                     ← Portal siswa
    │       │   ├── login.blade.php, dashboard.blade.php, profile.blade.php
    │       │   ├── kursus.blade.php, modul.blade.php, baca_modul.blade.php, pengerjaan_soal.blade.php
    │       │   ├── riwayat_absen.blade.php, riwayat_point.blade.php
    │       │   ├── grades.blade.php, grades_print.blade.php
    │       │   ├── pengajuan_ijin.blade.php
    │       │   ├── notifications.blade.php, notification_detail.blade.php
    │       │   ├── complaints/            ← Pengaduan siswa
    │       │   └── finance/               ← Keuangan siswa (kartu, transfer, riwayat)
    │       ├── parent/                    ← Portal orang tua (login, register, dashboard, dll.)
    │       ├── admin/                     ← Halaman admin
    │       │   ├── complaints/            ← Kelola pengaduan
    │       │   ├── courses/               ← Kelola mapel & modul (rekap nilai, progress, soal)
    │       │   ├── exams/                 ← Kelola bank soal & hasil ujian
    │       │   ├── finance/               ← Kelola keuangan (kartu, transaksi, rekap, receipt)
    │       │   ├── notifications/         ← Kelola notifikasi siswa
    │       │   └── permits/               ← Kelola izin siswa
    │       ├── students/                  ← CRUD siswa, cetak kartu, laporan
    │       ├── management-users/          ← CRUD guru/staf
    │       ├── parents/                   ← CRUD orang tua
    │       ├── angkatans/                 ← CRUD angkatan
    │       ├── registrations/             ← Pendaftaran & status (form, cek status, laporan PDF)
    │       ├── attendance/                ← Absensi (index, create, bulk, scan QR, laporan)
    │       ├── points/                    ← Poin & leaderboard
    │       ├── point-rules/               ← Aturan poin
    │       ├── courses/                   ← Tampilan tambahan mapel
    │       ├── exams/                     ← Halaman ujian (take)
    │       ├── raport/                    ← Raport industri
    │       ├── holidays/                  ← Hari libur
    │       ├── settings/                  ← Pengaturan sistem
    │       └── errors/                    ← Halaman error (maintenance)
    ├── routes/
    │   ├── web.php              ← Semua rute web (pendaftaran, portal siswa, portal wali, admin)
    │   └── console.php          ← Routing command Artisan
    ├── storage/                  ← File storage (logs, upload, cache, views)
    ├── tests/
    │   ├── Feature/
    │   │   └── ExampleTest.php
    │   ├── Unit/
    │   │   └── ExampleTest.php
    │   └── TestCase.php
    ├── vendor/                   ← Dependensi Composer
    ├── .env                      ← Konfigurasi environment (database, app key, dll.)
    ├── artisan                   ← CLI Laravel
    ├── composer.json             ← Dependensi PHP (Laravel 12, DomPDF, Excel, QR Code, Google API)
    ├── package.json              ← Dependensi JS (Vite 7, Tailwind CSS 4, Axios)
    └── phpunit.xml               ← Konfigurasi PHPUnit
```

**Folder penting yang perlu diperhatikan:**
- `app/Http/Controllers/` — seluruh logika bisnis tiap fitur berada di sini.
- `app/Models/` — representasi tabel database dan relasinya.
- `resources/views/` — tampilan antarmuka (Blade) yang terbagi per peran: `admin/`, `siswa/`, `parent/`, dan publik.
- `routes/web.php` — definisi semua rute aplikasi (pendaftaran siswa baru, portal siswa, portal wali, admin).
- `database/migrations/` & `database/seeders/` — struktur dan data awal database (admin, siswa contoh, angkatan).
- `public/ab_acb/` — endpoint khusus untuk perangkat scanner absensi (fetch settings, students, save attendance).

---

## 8. Tim Pengembang

| Posisi | Nama |
|--------|------|
| **Nama Tim** | **Smart EduHUb** |
| **Ketua Tim** | Krisna Aditama |
| **Anggota 1** | Najih Banyu Wicaksono |
| **Anggota 2** | Mujahid Muhammad Abul Ghofur |
| **Pembimbing** | Jeri Wilaga |

---

*Dibuat untuk Lomba Direktorat Jenderal Pendidikan Vokasi — Smart EduHUb*
