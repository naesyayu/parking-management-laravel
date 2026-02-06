# 📚 DOKUMENTASI LENGKAP SISTEM PARKIR

Dokumentasi lengkap ini menjelaskan setiap bagian dari Sistem Informasi Parkir yang dibangun dengan Laravel. Dibuat dengan bahasa sederhana agar mudah dipahami untuk pelajar SMK.

---

## 📖 DAFTAR ISI

1. [Pendahuluan](#pendahuluan)
2. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
3. [Struktur Folder Project](#struktur-folder-project)
4. [Database & Model](#database--model)
5. [Controllers & Fungsinya](#controllers--fungsinya)
6. [Routes (Rute Web)](#routes-rute-web)
7. [Middleware (Penjaga Akses)](#middleware-penjaga-akses)
8. [Blade Templates (Tampilan)](#blade-templates-tampilan)
9. [Traits & Helper](#traits--helper)
10. [Alur Kerja Sistem](#alur-kerja-sistem)

---

## Pendahuluan

### Apa itu Sistem Parkir?

Sistem Parkir adalah aplikasi web yang digunakan untuk mengelola parkir kendaraan. Aplikasi ini membantu:
- Mencatat kendaraan masuk dan keluar parkir
- Menghitung biaya parkir otomatis
- Mengelola member dengan berbagai level
- Membuat laporan pendapatan harian
- Mengadministrasi data master (data tipe kendaraan, tarif, area, dll)

### Siapa saja yang bisa menggunakan?

1. **Owner** - Pemilik parkir, bisa lihat laporan dan statistik
2. **Admin** - Mengatur master data dan user
3. **Petugas Parkir** - Input transaksi masuk-keluar kendaraan

---

## Teknologi yang Digunakan

| Bagian | Teknologi | Versi | Fungsi |
|--------|-----------|-------|--------|
| Backend | Laravel | 12.0 | Framework PHP untuk membuat web |
| Database | MySQL/MariaDB | - | Menyimpan data |
| Frontend | Blade Template | - | Template untuk tampilan HTML |
| Styling | Tailwind CSS | 4.0 | Framework CSS untuk desain |
| Build Tool | Vite | 7.0 | Kompiler moderen untuk Asset |
| PHP | PHP | 8.2+ | Bahasa pemrograman backend |
| QR Code | endroid/qr-code | 6.0 | Generate QR code tiket parkir |

---

## Struktur Folder Project

```
Parkir/
├── app/                          # Folder utama aplikasi
│   ├── Http/
│   │   ├── Controllers/          # File pengontrol logika aplikasi
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── TransaksiMasukController.php
│   │   │   ├── TransaksiKeluarController.php
│   │   │   └── ... (controller lainnya)
│   │   └── Middleware/           # Penjaga rute & akses
│   │       ├── CheckRole.php
│   │       ├── CheckPermission.php
│   │       └── NoCache.php
│   ├── Models/                   # Blueprint database (Eloquent ORM)
│   │   ├── User.php
│   │   ├── Kendaraan.php
│   │   ├── TransaksiParkir.php
│   │   ├── Member.php
│   │   └── ... (model lainnya)
│   ├── Traits/                   # Helper untuk reusable code
│   │   └── ActivityLogger.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/                    # File bootstrap aplikasi
│   ├── app.php
│   └── providers.php
├── config/                       # File konfigurasi aplikasi
│   ├── app.php                  # Konfigurasi aplikasi
│   ├── database.php             # Konfigurasi database
│   ├── auth.php                 # Konfigurasi autentikasi
│   └── ... (config lainnya)
├── database/
│   ├── migrations/              # File untuk membuat tabel database
│   │   ├── 2026_01_18_091155_create_users_table.php
│   │   ├── 2026_01_18_115347_create_pemilik_table.php
│   │   └── ... (migrations lainnya)
│   ├── seeders/                 # File untuk isi data default
│   │   ├── DatabaseSeeder.php
│   │   ├── UsersSeeder.php
│   │   └── ... (seeder lainnya)
│   └── factories/
│       └── UserFactory.php
├── resources/
│   ├── css/                     # File CSS styling
│   │   └── app.css
│   ├── js/                      # File JavaScript
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/                   # File tampilan (Blade Template)
│       ├── login.blade.php
│       ├── pages/
│       │   ├── dashboard/
│       │   ├── parkir/
│       │   └── laporan/
│       ├── user/
│       ├── area-parkir/
│       └── ... (tampilan lainnya)
├── routes/
│   ├── web.php                  # Definisi semua route (URL) aplikasi
│   └── console.php
├── public/                       # Folder public (akses dari browser)
│   ├── index.php                # Entry point aplikasi
│   └── storage/                 # Tempat menyimpan file upload
├── storage/                      # Cache & log
│   ├── app/
│   ├── framework/
│   └── logs/
├── composer.json                # Daftar library PHP yang digunakan
├── package.json                 # Daftar library JavaScript yang digunakan
├── phpunit.xml                  # Konfigurasi testing
├── vite.config.js               # Konfigurasi Vite
├── artisan                       # CLI Laravel (command line interface)
└── .env                          # File konfigurasi environment (credential sensitif)
```

---

## Database & Model

### Apa itu Model?

Model adalah representasi dari tabel database dalam bentuk class PHP. Setiap model berhubungan dengan satu tabel database, sehingga memudahkan kita untuk query data.

### Model-Model yang Ada

#### 1. **User** (`app/Models/User.php`)

**Fungsi**: Menyimpan data user/pengguna sistem parkir

**Tabel Database**: `users`

**Atribut/Kolom**:
- `id_user` - ID user (primary key/kunci utama)
- `username` - Nama login user
- `password` - Password user (di-hash/dienkripsi)
- `id_role` - ID role/peran user (foreign key)
- `status` - Status user (aktif/tidak aktif)

**Method/Fungsi**:
```php
public function role()
    // Relasi ke model Role
    // Untuk mendapatkan role dari user

public function transaksiParkir()
    // Relasi ke model TransaksiParkir
    // Untuk mendapatkan semua transaksi yang dibuat user
```

**Contoh Penggunaan**:
```php
$user = Auth::user();                    // Ambil user yang login sekarang
$role = $user->role;                     // Ambil role user
$transaksi = $user->transaksiParkir;    // Ambil transaksi user
```

---

#### 2. **Role** (`app/Models/Role.php`)

**Fungsi**: Menyimpan data peran/role user (owner, admin, petugas parkir)

**Tabel Database**: `roles`

**Atribut/Kolom**:
- `id_role` - ID role (primary key)
- `role_user` - Nama role (owner, admin, petugas parkir)
- `permissions` - Permission/izin dalam bentuk JSON
- `deleted_at` - Waktu soft delete

**Method/Fungsi**:
```php
public function isOwner(): bool
    // Cek apakah role ini owner

public function isAdmin(): bool
    // Cek apakah role ini admin

public function isPetugas(): bool
    // Cek apakah role ini petugas parkir

public function hasPermission(string $permission): bool
    // Cek apakah role ini punya izin tertentu
    // Contoh: hasPermission('user_management')

public function getPermissions(): array
    // Ambil semua permission dalam bentuk array
```

**Contoh Penggunaan**:
```php
$role = Role::find(1);                   // Ambil role dengan id 1
if ($role->isAdmin()) {
    // Jika role adalah admin, jalankan kode ini
}
$permissions = $role->getPermissions();   // Ambil semua permission
```

---

#### 3. **Kendaraan** (`app/Models/Kendaraan.php`)

**Fungsi**: Menyimpan data kendaraan yang parkir

**Tabel Database**: `kendaraan`

**Atribut/Kolom**:
- `id_kendaraan` - ID kendaraan (primary key)
- `plat_nomor` - Nomor plat kendaraan (misal: DK 1234 AB)
- `id_pemilik` - ID pemilik kendaraan
- `id_tipe` - ID tipe kendaraan (mobil, motor, dll)
- `status` - Status kendaraan (aktif/tidak aktif)
- `deleted_at` - Waktu soft delete

**Method/Fungsi**:
```php
public function pemilik()
    // Relasi ke model Pemilik - ambil data pemilik kendaraan

public function tipe()
    // Relasi ke model TipeKendaraan - ambil tipe kendaraan

public function transaksiParkir()
    // Relasi ke model TransaksiParkir - ambil semua transaksi kendaraan
```

---

#### 4. **TransaksiParkir** (`app/Models/TransaksiParkir.php`)

**Fungsi**: Menyimpan data transaksi parkir (masuk-keluar kendaraan)

**Tabel Database**: `transaksi_parkir`

**Atribut/Kolom**:
- `id_transaksi` - ID transaksi (primary key)
- `kode_tiket` - Kode tiket parkir (misal: TKT-20260206-001)
- `id_kendaraan` - ID kendaraan yang parkir
- `id_area` - ID area parkir
- `waktu_masuk` - Waktu kendaraan masuk
- `waktu_keluar` - Waktu kendaraan keluar
- `durasi_jam` - Durasi parkir dalam jam
- `id_tarif` - ID tarif yang dipakai
- `diskon` - Diskon (jika ada)
- `total_bayar` - Total biaya yang harus dibayar
- `id_user` - ID user yang input transaksi
- `id_member` - ID member (jika ada)
- `id_metode` - ID metode pembayaran
- `status` - Status transaksi (in/out)

**Method/Fungsi**:
```php
public function kendaraan()
    // Ambil data kendaraan

public function areaParkir()
    // Ambil data area parkir

public function tarifParkir()
    // Ambil data tarif yang dipakai

public function user()
    // Ambil user yang input transaksi

public function member()
    // Ambil data member (jika transaksi untuk member)

public function metodePembayaran()
    // Ambil data metode pembayaran

public function activityLogs()
    // Ambil log aktivitas transaksi ini

public function scopeToday()
    // Scope untuk filter transaksi hari ini
```

---

#### 5. **Member** (`app/Models/Member.php`)

**Fungsi**: Menyimpan data member parkir dengan cicilan/langganan

**Tabel Database**: `member`

**Atribut/Kolom**:
- `id_member` - ID member (primary key)
- `id_pemilik` - ID pemilik/pembuat member
- `id_level` - ID level membership (misal: platinum, gold)
- `berlaku_mulai` - Tanggal mulai berlaku membership
- `berlaku_hingga` - Tanggal akhir membership (expired date)
- `status` - Status membership (aktif/expired)

**Method/Fungsi**:
```php
public function pemilik()
    // Ambil data pemilik member

public function level()
    // Ambil data level membership

public function transaksiParkir()
    // Ambil semua transaksi member

public function isExpired(): bool
    // Cek apakah membership sudah expired
    // Return true jika hari ini sudah melebihi berlaku_hingga
```

**Fitur Spesial**: Membership otomatis diupdate ke status "expired" saat diakses jika sudah lewat tanggal berlaku_hingga.

---

#### 6. **AreaParkir** (`app/Models/AreaParkir.php`)

**Fungsi**: Menyimpan data area/lokasi parkir

**Tabel Database**: `area_parkir`

**Atribut/Kolom**:
- `id_area` - ID area (primary key)
- `kode_area` - Kode area (misal: A1, B2)
- `lokasi` - Nama/deskripsi lokasi parkir
- `foto_lokasi` - Foto lokasi parkir (path file)

**Method/Fungsi**:
```php
public function kapasitas()
    // Ambil data kapasitas area untuk setiap tipe kendaraan

public function transaksiParkir()
    // Ambil semua transaksi di area ini
```

---

#### 7. **AreaKapasitas** (`app/Models/AreaKapasitas.php`)

**Fungsi**: Menyimpan kapasitas area parkir per tipe kendaraan

**Tabel Database**: `area_kapasitas`

**Atribut/Kolom**:
- `id_area_kapasitas` - ID kapasitas (primary key)
- `id_area` - ID area parkir
- `id_tipe` - ID tipe kendaraan
- `kapasitas` - Jumlah slot yang tersedia

**Contoh Data**:
- Area A1, Tipe Motor, Kapasitas 50
- Area A1, Tipe Mobil, Kapasitas 20

---

#### 8. **TipeKendaraan** (`app/Models/TipeKendaraan.php`)

**Fungsi**: Menyimpan data jenis/tipe kendaraan

**Tabel Database**: `tipe_kendaraan`

**Atribut/Kolom**:
- `id_tipe` - ID tipe (primary key)
- `tipe_kendaraan` - Nama tipe (motor, mobil, bis, dll)
- `kode` - Kode tipe (MTO, MOB, BIS, dll)
- `deskripsi` - Deskripsi tipe

---

#### 9. **TarifParkir** (`app/Models/TarifParkir.php`)

**Fungsi**: Menyimpan data tarif/harga parkir

**Tabel Database**: `tarif_parkir`

**Atribut/Kolom**:
- `id_tarif` - ID tarif (primary key)
- `id_tarif_detail` - ID detail tarif
- `id_tipe` - ID tipe kendaraan
- `tarif` - Besarnya tarif (misal: 5000)

**Contoh Data**:
- Tipe Motor, Detail Normal, Tarif 5000 per jam
- Tipe Mobil, Detail Normal, Tarif 10000 per jam
- Tipe Motor, Detail Member, Tarif 3000 per jam

---

#### 10. **DetailParkir** (`app/Models/DetailParkir.php`)

**Fungsi**: Menyimpan detail/kategori parkir (normal, member, dll)

**Tabel Database**: `detail_parkir`

**Atribut/Kolom**:
- `id_tarif_detail` - ID detail (primary key)
- `detail` - Nama detail (Normal, Member Premium, Gratis, dll)

---

#### 11. **MetodePembayaran** (`app/Models/MetodePembayaran.php`)

**Fungsi**: Menyimpan data metode pembayaran

**Tabel Database**: `metode_pembayaran`

**Atribut/Kolom**:
- `id_metode` - ID metode (primary key)
- `metode` - Nama metode pembayaran (Tunai, Transfer, Kartu, dll)

---

#### 12. **MemberLevel** (`app/Models/MemberLevel.php`)

**Fungsi**: Menyimpan data level/paket membership

**Tabel Database**: `member_level`

**Atribut/Kolom**:
- `id_level` - ID level (primary key)
- `level` - Nama level (Gold, Platinum, Silver, dll)
- `diskon` - Persentase diskon (misal: 20 = 20%)
- `harga` - Harga paket per bulan
- `durasi_hari` - Durasi membership dalam hari

---

#### 13. **Pemilik** (`app/Models/Pemilik.php`)

**Fungsi**: Menyimpan data pemilik/owner kendaraan

**Tabel Database**: `pemilik`

**Atribut/Kolom**:
- `id_pemilik` - ID pemilik (primary key)
- `nama_pemilik` - Nama lengkap pemilik
- `no_identitas` - KTP/SIM/Identitas lainnya
- `no_telepon` - Nomor telepon
- `alamat` - Alamat pemilik
- `created_at` - Waktu dibuat
- `updated_at` - Waktu diupdate terakhir

---

#### 14. **ActivityLog** (`app/Models/ActivityLog.php`)

**Fungsi**: Menyimpan log/catatan aktivitas user di sistem

**Tabel Database**: `activity_logs`

**Atribut/Kolom**:
- `id_log` - ID log (primary key)
- `id_user` - User yang melakukan aktivitas
- `action` - Jenis aktivitas (login, logout, tambah_user, dll)
- `description` - Deskripsi aktivitas (misal: "User berhasil login")
- `id_transaksi` - ID transaksi (jika ada)
- `metadata` - Data tambahan dalam JSON
- `ip_address` - IP address user
- `user_agent` - Device/browser user
- `created_at` - Waktu aktivitas terjadi

---

#### 15. **LaporanHarian** (`app/Models/LaporanHarian.php`)

**Fungsi**: Menyimpan data laporan pendapatan harian

**Tabel Database**: `laporan_harian`

**Atribut/Kolom**:
- `id_laporan` - ID laporan (primary key)
- `tanggal` - Tanggal laporan
- `total_transaksi` - Jumlah transaksi hari itu
- `total_pendapatan` - Total pendapatan
- `total_diskon` - Total diskon yang diberikan

---

## Controllers & Fungsinya

### Apa itu Controller?

Controller adalah file PHP yang berisi logika/aturan bisnis aplikasi. Controller menghubungkan:
- **Input dari User** (melalui form atau request)
- **Database/Model** (untuk query data)
- **View/Template** (untuk menampilkan output ke user)

Biasanya satu controller menangani satu resource/bagian (misal: UserController menangani user CRUD).

### Structure CRUD Controller

CRUD = Create (Tambah), Read (Lihat), Update (Edit), Delete (Hapus)

Standar method dalam CRUD controller:
```php
public function index()        // Tampilkan daftar semua data
public function create()       // Tampilkan form tambah
public function store()        // Simpan data baru ke database
public function show($id)      // Tampilkan detail 1 data
public function edit($id)      // Tampilkan form edit
public function update()       // Update data di database
public function destroy($id)   // Hapus data dari database
public function trash()        // Tampilkan data yang dihapus (soft delete)
public function restore($id)   // Kembalikan data dari trash
```

---

### AuthController (`app/Http/Controllers/AuthController.php`)

**Fungsi**: Menangani autentikasi/login-logout user

**Method**:

#### `showLoginForm()`
- **Fungsi**: Tampilkan halaman login
- **Logic**:
  1. Cek apakah user sudah login
  2. Jika sudah login, redirect ke dashboard
  3. Jika belum, tampilkan tampilan login
- **Return**: View login.blade.php

#### `login(Request $request)`
- **Fungsi**: Proses login user
- **Logic**:
  1. Validasi input username dan password
  2. Cek apakah user ada di database
  3. Cek apakah status user aktif
  4. Cek password (menggunakan Auth::attempt)
  5. Jika benar, set session dan log activity
  6. Jika salah, tampilkan error
- **Return**: Redirect ke dashboard atau kembali ke login dengan error

#### `logout(Request $request)`
- **Fungsi**: Keluar dari sistem
- **Logic**:
  1. Log activity logout
  2. Hapus session user
  3. Redirect ke login
- **Return**: Redirect ke login dengan pesan sukses

---

### DashboardController (`app/Http/Controllers/DashboardController.php`)

**Fungsi**: Menampilkan halaman dashboard/beranda utama

**Method**:

#### `index()`
- **Fungsi**: Tampilkan dashboard dengan statistik
- **Logic**:
  1. Get user yang login dan rolenya
  2. Kumpulkan data statistik:
     - Transaksi masuk hari ini
     - Transaksi keluar hari ini
     - Kendaraan yang parkir sekarang
     - Pendapatan hari ini
     - Diskon hari ini
     - Transaksi bulan ini
     - Dan statistik lainnya
  3. Buat breakdown per tipe kendaraan
  4. Tampilkan di view dashboard
- **Return**: View dashboard dengan data statistik

---

### UserController (`app/Http/Controllers/UserController.php`)

**Fungsi**: Mengelola data user (tambah, edit, lihat, hapus)

**Method**:

#### `index()`
- Tampilkan daftar semua user
- Return: View user/index dengan list user

#### `create()`
- Tampilkan form tambah user baru
- Return: View user/create dengan daftar role

#### `store(Request $request)`
- Simpan user baru ke database
- Validasi: username harus unik, password min 6 karakter
- Log activity: "Menambah user"
- Return: Redirect ke user.index dengan pesan sukses

#### `edit(User $user)`
- Tampilkan form edit user
- Return: View user/edit dengan data user

#### `update(Request $request, User $user)`
- Update data user di database
- Validasi: username harus unik untuk user lain
- Log activity: "Mengedit user" dengan mencatat perubahan
- Return: Redirect ke user.index dengan pesan sukses

#### `destroy(User $user)`
- Soft delete user (tandai sebagai deleted_at)
- Log activity: "Menghapus user"
- Return: Redirect dengan pesan sukses

#### `trash()`
- Tampilkan daftar user yang telah dihapus
- Return: View user/trash dengan list user yang dihapus

#### `restore($id)`
- Kembalikan user dari trash
- Return: Redirect dengan pesan sukses

---

### TransaksiMasukController (`app/Http/Controllers/TransaksiMasukController.php`)

**Fungsi**: Menangani transaksi kendaraan masuk parkir

**Method**:

#### `index()`
- Tampilkan halaman input transaksi masuk
- Get data area kapasitas dan tipe kendaraan
- Return: View pages/parkir/masuk

#### `autocompletePlat(Request $request)`
- **Fungsi**: Autocomplete untuk input plat nomor
- **Logic**:
  1. Ambil keyword dari request (minimal 1 karakter)
  2. Cari kendaraan dengan plat yang match
  3. Return hasil dalam format JSON
- **Return**: JSON array kendaraan yang match

#### `store(Request $request)`
- **Fungsi**: Simpan transaksi masuk baru
- **Logic Kompleks**:
  1. Validasi input plat_nomor dan id_tipe
  2. Normalisasi plat nomor (uppercase)
  3. Cek apakah kendaraan sedang parkir
     - Jika ya, jangan boleh masuk lagi
  4. Cek/buat kendaraan:
     - Jika kendaraan sudah ada, gunakan existing
     - Jika belum ada, buat kendaraan baru
     - Pastikan tipe kendaraan sesuai
  5. Cari slot parkir yang tersedia (AreaKapasitas)
     - Gunakan lock agar tidak double book
  6. Generate kode tiket unik (misal: TKT-20260206-001)
  7. Generate QR code dari kode tiket
  8. Simpan transaksi ke database
  9. Kurangi kapasitas area
  10. Log activity
  11. Return tiket masuk (dengan QR code)
- **Return**: View tiket masuk dengan QR code

---

### TransaksiKeluarController (`app/Http/Controllers/TransaksiKeluarController.php`)

**Fungsi**: Menangani transaksi kendaraan keluar parkir

**Method**:

#### `index()`
- Tampilkan halaman scan/cari transaksi keluar
- Return: View pages/parkir/keluar

#### `cariTransaksi(Request $request)`
- Cari transaksi berdasarkan kode tiket atau plat nomor
- Hanya menampilkan transaksi dengan status 'in' (masih parkir)
- Return: JSON data transaksi

#### `store(Request $request)`
- **Fungsi**: Simpan transaksi keluar
- **Logic Kompleks**:
  1. Validasi input (kode_tiket atau id_kendaraan)
  2. Ambil data transaksi masuk yang belum keluar
  3. Hitung durasi parkir (dari waktu_masuk ke sekarang)
  4. Hitung tarif berdasarkan:
     - Tipe kendaraan
     - Durasi parkir
     - Member level (jika ada)
  5. Hitung diskon:
     - Jika member, hitung diskon sesuai level
  6. Hitung total_bayar = (durasi * tarif) - diskon
  7. Update transaksi dengan:
     - waktu_keluar = now()
     - durasi_jam
     - total_bayar
     - diskon
     - status = 'out'
     - id_metode (metode pembayaran)
  8. Kembalikan kapasitas area
  9. Generate QR code tiket keluar
  10. Log activity
  11. Return tiket keluar

---

### MasterDataController (`app/Http/Controllers/MasterDataController.php`)

**Fungsi**: Menampilkan view data master

**Method**:

#### `parkir()`
- Tampilkan data parkir (area, kapasitas, tipe kendaraan)
- Return: View pages/master-data/data-parkir

#### `memberKendaraan()`
- Tampilkan data member dan kendaraan
- Return: View pages/master-data/data-member-kendaraan

#### `riwayatTransaksi()`
- Tampilkan riwayat/histori transaksi parkir
- Return: View pages/master-data/riwayat-transaksi

---

### LaporanHarianController (`app/Http/Controllers/LaporanHarianController.php`)

**Fungsi**: Menampilkan laporan pendapatan

**Method**:

#### `index()`
- Tampilkan laporan dengan filter harian/mingguan/bulanan
- Total transaksi, pendapatan, diskon per periode
- Return: View laporan index

#### `breakdown(Request $request)`
- Tampilkan breakdown (rincian) pendapatan per tipe kendaraan
- Dengan chart/grafik visualisasi
- Return: View laporan breakdown

#### `detailTransaksi(Request $request)`
- Tampilkan detail tiap transaksi dalam range tanggal
- Untuk analisis lebih dalam
- Return: View laporan detail-transaksi

---

### RoleController (`app/Http/Controllers/RoleController.php`)

**Fungsi**: Mengelola data role/peran user

**Method**: Standard CRUD (index, create, store, edit, update, destroy)

---

### Kontroller Lainnya (Standard CRUD)

Berikut controller dengan structure CRUD standard:
- **PemilikController** - Kelola data pemilik kendaraan
- **TipeKendaraanController** - Kelola tipe kendaraan
- **KendaraanController** - Kelola data kendaraan
- **AreaParkirController** - Kelola area parkir
- **AreaKapasitasController** - Kelola kapasitas area
- **MemberController** - Kelola member parkir
- **MemberLevelController** - Kelola level membership
- **TarifParkirController** - Kelola tarif parkir
- **MetodePembayaranController** - Kelola metode pembayaran
- **DetailParkirController** - Kelola detail parkir
- **ActivityLogController** - Lihat activity log
- **ChangePasswordController** - Ubah password user

---

## Routes (Rute Web)

### Apa itu Routes?

Routes adalah URL/alamat yang bisa diakses di aplikasi. Setiap route menghubungkan:
- URL (misal: `/dashboard`)
- HTTP Method (GET, POST, PUT, DELETE)
- Controller & Method yang menangani

### File Routes

File routes ada di `routes/web.php`

### Struktur Routes

```php
// Rute GET - untuk menampilkan halaman/form
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Rute POST - untuk submit form
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rute dengan parameter
Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');

// Rute bersarang di middleware
Route::middleware(['auth'])->group(function () {
    // Routes di sini hanya bisa diakses user yang sudah login
});
```

### Pengelompokan Routes

Routes dikelompokkan berdasarkan middleware/kebutuhan:

#### 1. **Authentication Routes** (Publik)
```
GET   /login          -> Tampilkan halaman login
POST  /login          -> Proses login
POST  /logout         -> Logout (perlu auth)
```

#### 2. **Dashboard Routes** (Perlu Auth)
```
GET   /dashboard      -> Halaman dashboard
GET   /change-password -> Form ganti password
POST  /change-password/update -> Proses ganti password
```

#### 3. **Master Data Routes** (Perlu Auth)
```
GET   /master-data                      -> Data parkir
GET   /master-data/riwayat-transaksi    -> Riwayat transaksi
GET   /master-data/member-kendaraan     -> Data member & kendaraan
```

#### 4. **Activity Log Routes** (Perlu Auth + Role: admin/owner)
```
GET   /activity-log              -> Daftar activity log
GET   /activity-log/{id}         -> Detail activity log
GET   /activity-log-export       -> Export activity log
```

#### 5. **User Management Routes** (Perlu Auth + Role: admin)
```
GET    /user                -> Daftar user
GET    /user/create         -> Form tambah user
POST   /user                -> Simpan user baru
GET    /user/{user}/edit    -> Form edit user
PUT    /user/{user}         -> Update user
DELETE /user/{user}         -> Hapus user
GET    /user/trash          -> Daftar user dihapus
PUT    /user/{user}/restore -> Kembalikan user
```

#### 6. **Master Data CRUD Routes** (Perlu Auth + Role: admin)
```
GET    /tipe-kendaraan              -> Daftar tipe kendaraan
POST   /tipe-kendaraan              -> Tambah tipe kendaraan
PUT    /tipe-kendaraan/{id}         -> Edit tipe kendaraan
DELETE /tipe-kendaraan/{id}         -> Hapus tipe kendaraan

// Begitu juga untuk:
// /area-parkir, /pemilik, /kendaraan, /member, /tarif-parkir, dll
```

#### 7. **Transaksi Parkir Routes** (Perlu Auth + Role: petugas)
```
GET    /parkir/masuk                -> Halaman input transaksi masuk
POST   /parkir/masuk                -> Simpan transaksi masuk
GET    /parkir/keluar               -> Halaman cari transaksi keluar
POST   /parkir/keluar               -> Simpan transaksi keluar
GET    /parkir/autocomplete-plat    -> Autocomplete plat nomor
```

---

## Middleware (Penjaga Akses)

### Apa itu Middleware?

Middleware adalah "penjaga gerbang" yang mengecek kondisi tertentu sebelum request sampai ke controller. Jika tidak memenuhi kondisi, user akan ditolak.

Ada di folder: `app/Http/Middleware/`

### Middleware yang Ada

#### 1. **CheckRole** Middleware (`CheckRole.php`)

**Fungsi**: Mengecek apakah user memiliki role yang diizinkan

**Cara Kerja**:
1. Cek apakah user sudah login (auth middleware)
2. Ambil role user
3. Normalisasi nama role (lowercase, hapus spasi)
4. Cek apakah role user ada di list role yang diizinkan
5. Jika ya, lanjut ke controller
6. Jika tidak, tampilkan error 403 (Forbidden)

**Contoh Penggunaan**:
```php
// Hanya admin yang bisa akses
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::resource('user', UserController::class);
});

// Admin atau owner yang bisa akses
Route::middleware(['auth', 'check.role:admin,owner'])->group(function () {
    Route::get('/activity-log', [ActivityLogController::class, 'index']);
});
```

**Role yang Ada**:
- `owner` - Pemilik parkir
- `admin` - Administrator
- `petugas parkir` - Petugas input transaksi

---

#### 2. **CheckPermission** Middleware (`CheckPermission.php`)

**Fungsi**: Mengecek apakah role user punya permission tertentu

**Cara Kerja**:
1. Ambil permission yang diperlukan dari parameter
2. Ambil role user
3. Cek apakah role punya permission tersebut
4. Jika ya, lanjut ke controller
5. Jika tidak, tampilkan error 403

**Contoh Penggunaan**:
```php
// Hanya role yang punya permission user_management
Route::middleware(['auth', 'check.permission:user_management'])->group(function () {
    Route::resource('user', UserController::class);
});
```

---

#### 3. **NoCache** Middleware (`NoCache.php`)

**Fungsi**: Mencegah browser meng-cache halaman (penting untuk security)

**Cara Kerja**: Menambahkan header `Cache-Control: no-store, no-cache` dan `Pragma: no-cache`

**Gunanya**: Jika user logout, halaman lama tidak akan di-cache browser sehingga tidak bisa diakses lagi dari back button.

---

## Blade Templates (Tampilan)

### Apa itu Blade?

Blade adalah template engine Laravel. File Blade memiliki ekstensi `.blade.php` dan berisi HTML + PHP yang menciptakan tampilan dinamis.

Folder templates: `resources/views/`

### Struktur Template

#### 1. **Layout Template** (`Layout/`)

**header.blade.php** - Header halaman (logo, navigasi)

**navbar.blade.php** - Navbar dengan menu berdasarkan role

---

#### 2. **Login Template** (`login.blade.php`)

Tampilan halaman login dengan form username & password

---

#### 3. **Dashboard Template** (`pages/dashboard/index.blade.php`)

Tampilan dashboard dengan statistik:
- Transaksi masuk hari ini
- Transaksi keluar hari ini
- Kendaraan parkir sekarang
- Pendapatan hari ini
- Chart & grafik

---

#### 4. **Transaksi Parkir Templates** (`pages/parkir/`)

**masuk.blade.php** - Form input transaksi masuk
- Form untuk input plat nomor (dengan autocomplete)
- Form untuk pilih tipe kendaraan
- Tombol submit untuk generate tiket masuk

**keluar.blade.php** - Form input transaksi keluar
- Form untuk scan/input kode tiket atau plat nomor
- Tombol cari untuk mencari transaksi
- Form untuk pilih metode pembayaran setelah data ditemukan

**tiket-masuk.blade.php** - Tampilan tiket masuk
- Menampilkan QR code
- Kode tiket
- Informasi parkir

**tiket-keluar.blade.php** - Tampilan tiket keluar
- Menampilkan QR code
- Durasi parkir
- Biaya & diskon
- Total bayar

---

#### 5. **Laporan Templates** (`pages/laporan/`)

**breakdown.blade.php** - Laporan breakdown per tipe kendaraan

**detail-transaksi.blade.php** - Detail setiap transaksi

---

#### 6. **Master Data Templates** (`pages/master-data/`)

**data-parkir.blade.php** - Tabel area parkir, kapasitas, tipe kendaraan

**data-member-kendaraan.blade.php** - Tabel member dan kendaraan

**riwayat-transaksi.blade.php** - Tabel riwayat transaksi dengan filter

---

#### 7. **CRUD Templates** (Folder tiap resource)

Masing-masing resource punya folder dengan template:

**index.blade.php** - Tampilan daftar data
- Tabel dengan kolom data
- Tombol edit, hapus, lihat detail
- Fitur search, filter, sort

**create.blade.php** - Tampilan form tambah
- Form dengan input sesuai model

**edit.blade.php** - Tampilan form edit
- Form pra-isi dengan data existing

**trash.blade.php** - Tampilan data yang dihapus
- Tabel data soft delete
- Tombol restore/restore permanent

Folder templates CRUD:
- `area-kapasitas/`
- `area-parkir/`
- `data-kendaraan/` (Kendaraan)
- `detail-parkir/`
- `member/`
- `metode-pembayaran/`
- `pemilik/`
- `roles/`
- `tarif-parkir/`
- `tipe-kendaraan/`
- `user/`

---

### Sintaks Blade Dasar

```blade
{{-- Komentar Blade --}}

{{ $variable }}              {{-- Tampilkan variable --}}

{{ $user->name }}            {{-- Tampilkan property object --}}

@if($condition)
    {{-- Kode jika kondisi benar --}}
@else
    {{-- Kode jika kondisi salah --}}
@endif

@foreach($items as $item)
    {{ $item->name }}    {{-- Loop/perulangan --}}
@endforeach

@for($i = 0; $i < 10; $i++)
    {{ $i }}
@endfor

@while($condition)
    {{-- Kode yang diulang --}}
@endwhile

@switch($value)
    @case('value1')
        {{-- Kode jika $value = 'value1' --}}
        @break
    @case('value2')
        {{-- Kode jika $value = 'value2' --}}
        @break
    @default
        {{-- Kode default --}}
@endswitch

{{ $variable ?? 'default' }}  {{-- Tampilkan atau default --}}

@auth
    {{-- Kode jika user sudah login --}}
@endauth

@guest
    {{-- Kode jika user belum login --}}
@endguest

@can('permission')
    {{-- Kode jika user punya permission --}}
@endcan
```

---

## Traits & Helper

### Apa itu Trait?

Trait adalah class yang berisi fungsi yang bisa digunakan ulang di multiple class lain (tanpa harus extend/inherit).

Ada di folder: `app/Traits/`

### ActivityLogger Trait (`ActivityLogger.php`)

**Fungsi**: Meng-log setiap aktivitas CRUD user

**Method yang Disediakan**:

#### `logCreate(Model $model, string $modelName, array $additionalData = [])`

- **Fungsi**: Log aktivitas menambah data baru
- **Cara Penggunaan**:
```php
// Di dalam controller
$user = User::create(['username' => 'john', ...]);
$this->logCreate($user, 'User');
```
- **Output**: Mencatat aktivitas "Menambah User: john" ke database

#### `logUpdate(Model $model, string $modelName, array $originalData = [], array $additionalData = [])`

- **Fungsi**: Log aktivitas edit data (mencatat perubahan)
- **Cara Penggunaan**:
```php
$originalData = $user->toArray();
$user->update(['status' => 'tidak aktif']);
$this->logUpdate($user, 'User', $originalData);
```
- **Output**: Mencatat aktivitas "Mengedit User: john" dengan mencatat perubahan field

#### `logDelete(Model $model, string $modelName, array $additionalData = [])`

- **Fungsi**: Log aktivitas hapus data
- **Cara Penggunaan**:
```php
$user = User::find(1);
$this->logDelete($user, 'User');
$user->delete();
```
- **Output**: Mencatat aktivitas "Menghapus User: john"

---

### Helper Functions

#### `ActivityLog::log()` (Static Method)

**Fungsi**: Log aktivitas umum (tidak hanya CRUD)

**Cara Penggunaan**:
```php
ActivityLog::log('login', 'User berhasil login');
ActivityLog::log('logout', 'User logout');
ActivityLog::log('export', 'Export laporanr');
```

**Parameter**:
- `action` - Jenis aktivitas (string)
- `description` - Deskripsi aktivitas (string)
- `id_transaksi` - ID transaksi (optional)
- `metadata` - Data tambahan (optional array)

---

## Alur Kerja Sistem

Berikut adalah alur kerja lengkap sistem parkir dari awal hingga akhir:

### 1. **Login User**

```
User buka aplikasi
    ↓
Akses /login (AuthController::showLoginForm)
    ↓
Tampilkan halaman login (login.blade.php)
    ↓
User input username & password
    ↓
Submit form POST /login (AuthController::login)
    ↓
Cek user di database
    ├─ Jika tidak ada → Error "Username tidak ditemukan"
    └─ Jika ada, cek status
        ├─ Jika tidak aktif → Error "Akun tidak aktif"
        └─ Jika aktif, cek password
            ├─ Jika salah → Error "Password salah"
            └─ Jika benar
                ├─ Set session (login)
                ├─ Log activity "login"
                └─ Redirect ke /dashboard ✓
```

---

### 2. **View Dashboard**

```
User akses /dashboard (DashboardController::index)
    ↓
Get user yang login dan rolenya
    ↓
Query statistik dari database:
    - Hitung transaksi hari ini
    - Hitung pendapatan hari ini
    - Hitung kendaraan yang sedang parkir
    - Dll
    ↓
Tampilkan dashboard dengan statistik (pages/dashboard/index.blade.php) ✓
```

---

### 3. **Petugas Input Transaksi Masuk**

```
Petugas parkir akses /parkir/masuk (TransaksiMasukController::index)
    ↓
Load halaman form masuk (pages/parkir/masuk.blade.php)
    ↓
Petugas input plat nomor (dengan autocomplete dari autocompletePlat method)
    ↓
Form otomatis isi tipe kendaraan (dari autocomplete)
    ↓
Petugas submit form
    ↓
POST /parkir/masuk (TransaksiMasukController::store)
    ├─ Normalisasi plat nomor
    ├─ Cek apakah kendaraan sedang parkir
    │   └─ Jika ya → Error ❌
    ├─ Cek/buat kendaraan record
    ├─ Cari slot parkir yang tersedia
    │   └─ Jika tidak ada → Error "Kapasitas penuh" ❌
    ├─ Generate kode tiket unik (TKT-20260206-001)
    ├─ Generate QR code dari kode tiket
    ├─ Simpan transaksi masuk ke database dengan status = 'in'
    ├─ Kurangi kapasitas area
    ├─ Log activity "Transaksi masuk"
    └─ Tampilkan tiket masuk dengan QR code (pages/parkir/tiket-masuk.blade.php) ✓
```

---

### 4. **Petugas Input Transaksi Keluar**

```
Petugas akses /parkir/keluar (TransaksiKeluarController::index)
    ↓
Tampilkan halaman form keluar (pages/parkir/keluar.blade.php)
    ↓
Petugas scan/input kode tiket atau plat nomor
    ↓
AJAX GET /parkir/cari-transaksi (mencari transaksi masuk)
    ├─ Cari transaksi dengan status = 'in' yang match
    └─ Return data transaksi dalam JSON
    ↓
Tampilkan data transaksi yang ditemukan
    ↓
Petugas pilih metode pembayaran
    ↓
Submit form
    ↓
POST /parkir/keluar (TransaksiKeluarController::store)
    ├─ Ambil data transaksi masuk
    ├─ Hitung durasi = waktu_sekarang - waktu_masuk
    ├─ Hitung tarif berdasarkan:
    │   ├─ Tipe kendaraan
    │   └─ Durasi parkir
    ├─ Cek apakah punya member (dan tidak expired)
    │   └─ Jika ya, hitung diskon dari level member
    ├─ Hitung total_bayar = (durasi * tarif) - diskon
    ├─ Update transaksi dengan:
    │   ├─ waktu_keluar = now()
    │   ├─ durasi_jam
    │   ├─ total_bayar
    │   ├─ diskon
    │   ├─ status = 'out'
    │   └─ id_metode (metode pembayaran)
    ├─ Kembalikan kapasitas area
    ├─ Generate QR code tiket keluar
    ├─ Log activity "Transaksi keluar"
    └─ Tampilkan tiket keluar dengan struk (pages/parkir/tiket-keluar.blade.php) ✓
```

---

### 5. **Admin Kelola Master Data**

```
Admin akses /tipe-kendaraan (TipeKendaraanController::index)
    ↓
Tampilkan list tipe kendaraan (view index.blade.php)
    ├─ Tombol "Tambah"
    ├─ Tombol "Edit" tiap row
    └─ Tombol "Hapus" tiap row

USER KLIK "TAMBAH":
    ↓
GET /tipe-kendaraan/create → Tampilin form (create.blade.php)
    ↓
Input data & submit
    ↓
POST /tipe-kendaraan → Simpan ke database (TipeKendaraanController::store)
    ├─ Validasi input
    ├─ Buat record baru
    ├─ Log activity
    └─ Redirect ke index dengan pesan sukses ✓

USER KLIK "EDIT":
    ↓
GET /tipe-kendaraan/{id}/edit → Tampilkan form (edit.blade.php)
    ↓
Form sudah pra-isi dengan data lama
    ↓
Edit data & submit
    ↓
PUT /tipe-kendaraan/{id} → Update di database (TipeKendaraanController::update)
    ├─ Validasi input
    ├─ Update record
    ├─ Log activity dengan mencatat perubahan
    └─ Redirect ke index dengan pesan sukses ✓

USER KLIK "HAPUS":
    ↓
DELETE /tipe-kendaraan/{id} → Soft delete (TipeKendaraanController::destroy)
    ├─ Soft delete (set deleted_at)
    ├─ Log activity
    └─ Redirect dengan pesan sukses ✓

USER KLIK "LIHAT TRASH":
    ↓
GET /tipe-kendaraan/trash → Tampilkan data yang dihapus (view trash.blade.php)
    ├─ Tombol "Restore" untuk kembalikan
    └─ Tombol "Delete Permanent" untuk hapus permanen
```

Begitu juga untuk kelola:
- Area Parkir
- Pemilik
- Kendaraan
- Member
- Member Level
- Tarif Parkir
- Detail Parkir
- Metode Pembayaran
- Role
- User

---

### 6. **Owner/Admin Lihat Laporan**

```
Owner akses /laporan (LaporanHarianController::index)
    ↓
Tampilkan laporan dengan filter (view laporan/index.blade.php)
    ├─ Filter tanggal/minggu/bulan
    └─ Tombol "Detail" & "Breakdown"
    ↓
Query transaksi sesuai filter:
    ├─ Total transaksi
    ├─ Total pendapatan
    ├─ Total diskon
    └─ Per tipe kendaraan
    ↓
Tampilkan laporan dengan chart/grafik ✓

KLIK "BREAKDOWN":
    ↓
GET /laporan/breakdown → Tampilkan rincian per tipe (view laporan/breakdown.blade.php)
    ├─ Tabel breakdown per tipe kendaraan
    └─ Chart visualisasi
    ↓

KLIK "DETAIL TRANSAKSI":
    ↓
GET /laporan/detail-transaksi → Tampilkan rincian tiap transaksi
    ├─ Tabel detail transaksi
    ├─ Kolom: waktu, plat, durasi, tarif, total, member, dll
    └─ Export ke CSV/Excel
```

---

### 7. **Lihat Activity Log**

```
Admin/Owner akses /activity-log (ActivityLogController::index)
    ↓
Query semua activity logs dari database
    ↓
Tampilkan tabel activity dengan kolom:
    ├─ User (siapa melakukan)
    ├─ Action (apa yang dilakukan)
    ├─ Description (deskripsi)
    ├─ Waktu
    └─ Tombol "Lihat Detail"
    ↓
User klik "Lihat Detail":
    ↓
GET /activity-log/{id} → Tampilkan detail activity (view activity-log/show.blade.php)
    ├─ User
    ├─ Action & description
    ├─ Metadata (data tambahan dalam JSON)
    ├─ IP Address
    ├─ User Agent (device/browser)
    └─ Waktu
```

---

### 8. **Logout**

```
User klik tombol "Logout"
    ↓
POST /logout (AuthController::logout)
    ├─ Log activity "logout"
    ├─ Hapus session
    └─ Redirect ke /login ✓
```

---

## Penutup

Demikianlah dokumentasi lengkap Sistem Informasi Parkir. Aplikasi ini mendemonstrasikan konsep-konsep penting dalam web development:

✅ **Authentication & Authorization** - Sistem login & kontrol akses berdasarkan role
✅ **Database Design** - Relasi antar table dengan foreign key
✅ **CRUD Operations** - Menambah, mengubah, menghapus data
✅ **Business Logic** - Hitung tarif, diskon, durasi parkir
✅ **Logging & Audit** - Mencatat setiap aktivitas user
✅ **Responsive Design** - Template yang bisa diakses dari berbagai device
✅ **Error Handling** - Validasi input & error messages

Untuk pengembangan lebih lanjut, bisa ditambah fitur:
- Real-time notification
- Mobile app
- Payment gateway integration
- QR code scanner
- Analytics dashboard yang lebih advanced
- Multi-location support

Semoga dokumentasi ini membantu Anda memahami sistem parkir ini! 🚗🏍️

---

**Dibuat untuk:** Siswa SMK Program Keahlian Teknik Komputer dan Informatika
**Du=Tanggal:** 6 Februari 2026
**Status:** Dokumentasi Lengkap ✓
