# 📝 PERTANYAAN UJIAN & PRESENTASI SISTEM PARKIR

Dokumen ini berisi pertanyaan-pertanyaan yang kemungkinan besar ditanyakan oleh penguji saat ujian akhir/presentasi project Sistem Parkir. Beserta jawaban singkat dan jelas.

---

## 📖 DAFTAR ISI

1. [Pertanyaan Umum Project](#pertanyaan-umum-project)
2. [Pertanyaan Database](#pertanyaan-database)
3. [Pertanyaan Backend/Code](#pertanyaan-backend-code)
4. [Pertanyaan Frontend/UI](#pertanyaan-frontend-ui)
5. [Pertanyaan Fitur Spesifik](#pertanyaan-fitur-spesifik)
6. [Pertanyaan Problem Solving](#pertanyaan-problem-solving)
7. [Pertanyaan Troubleshooting](#pertanyaan-troubleshooting)

---

## Pertanyaan Umum Project

### Q1: Jelaskan tujuan dari aplikasi Sistem Parkir ini!

**Jawab:**

Tujuan aplikasi Sistem Parkir adalah untuk mengotomasi dan mengelola operasional parkir kendaraan dengan lebih efisien. Fungsi utamanya:

1. **Mencatat Transaksi** - Mencatat kendaraan apa saja yang masuk dan keluar parkir
2. **Menghitung Biaya** - Otomatis menghitung biaya parkir berdasarkan durasi dan tipe kendaraan
3. **Manajemen Kapasitas** - Mengelola kapasitas area parkir agar tidak over capacity
4. **Member Management** - Mengelola member dengan berbagai level (Gold, Platinum, dll) yang bisa mendapat diskon
5. **Laporan Pendapatan** - Membuat laporan harian untuk analisis pendapatan
6. **Audit Trail** - Mencatat semua aktivitas user untuk keamanan dan audit

**Manfaat Bisnis:**
- ✓ Menghemat waktu (tidak perlu hitung manual)
- ✓ Mengurangi kesalahan (otomatis)
- ✓ Meningkatkan transparansi
- ✓ Mempermudah analisis bisnis

---

### Q2: Siapa saja pengguna/user dari sistem ini dan apa peran mereka?

**Jawab:**

Ada 3 jenis user dengan peran berbeda:

| Role | Peran | Bisa Akses |
|------|-------|-----------|
| **Petugas Parkir** | Input transaksi masuk-keluar kendaraan | - Input transaksi masuk/keluar<br>- Lihat data parkir & member |
| **Admin** | Kelola master data dan user | - Kelola user<br>- Kelola master data (tipe kendaraan, area, tarif, dll)<br>- Lihat laporan & activity log |
| **Owner** | Pemilik parkir, lihat laporan & statistik | - Lihat dashboard & statistik<br>- Lihat laporan pendapatan<br>- Lihat activity log |

**Kontrol Akses:**
- Setiap role hanya bisa akses fitur yang sesuai dengan perannya (menggunakan middleware `CheckRole`)
- User tidak aktif tidak bisa login
- Password di-encrypt untuk keamanan

---

### Q3: Teknologi apa saja yang digunakan dalam project ini?

**Jawab:**

| Kategori | Teknologi | Versi | Fungsi |
|----------|-----------|-------|--------|
| **Backend** | Laravel | 12.0 | Web framework untuk membuat REST API dan backend |
| **Database** | MySQL/MariaDB | - | Database relational untuk menyimpan data |
| **Frontend** | Blade Template | - | Template engine untuk render HTML dinamis |
| **Styling** | Tailwind CSS | 4.0 | Framework CSS untuk design responsive |
| **Build Tool** | Vite | 7.0 | Compiler moderen untuk optimasi asset |
| **Server** | PHP | 8.2+ | Bahasa pemrograman server-side |
| **ORM** | Eloquent | - | Laravel's ORM untuk interact dengan database |
| **QR Code** | endroid/qr-code | 6.0 | Library untuk generate QR code tiket |
| **Auth** | Laravel Auth | - | Built-in authentication system |

**Why Laravel?**
- ✓ Framework modern & mudah dipelajari
- ✓ Built-in auth, migration, ORM
- ✓ MVC pattern yang terstruktur
- ✓ Security features bagus
- ✓ Dokumentasi lengkap
- ✓ Cocok untuk project skala menengah

---

### Q4: Jelaskan struktur folder project secara singkat!

**Jawab:**

```
Parkir/
├── app/                    # Kode aplikasi utama
│   ├── Http/Controllers/   # File logic aplikasi
│   ├── Http/Middleware/    # Penjaga akses
│   └── Models/             # Blueprint database
├── config/                 # File konfigurasi (database, auth, dll)
├── database/               # Migration & seeder
│   ├── migrations/         # Buat tabel database
│   └── seeders/            # Isi data default
├── resources/views/        # Template HTML
├── routes/web.php          # Definisi URL
├── public/                 # File publik (akses dari browser)
└── storage/                # Cache & log files
```

**Penjelasan Penting:**
- `app/Models/` = Blueprint table (User, Kendaraan, TransaksiParkir, dll)
- `app/Http/Controllers/` = Logic aplikasi (login, CRUD, dll)
- `routes/web.php` = Daftar URL yang bisa diakses
- `resources/views/` = Tampilan/UI yang dikirim ke browser
- `database/migrations/` = Script pembuatan tabel (versionable)

---

### Q5: Apa itu Laravel? Jelaskan keunggulannya!

**Jawab:**

Laravel adalah PHP web framework modern yang dirancang untuk membuat web application dengan cepat dan mudah. 

**Keunggulan Laravel:**

1. **MVC Pattern** - Kode terstruktur rapi (Model, View, Controller)
2. **Eloquent ORM** - Query database jadi mudah & elegant
3. **Migration System** - Kelola database schema dengan code (tidak manual SQL)
4. **Built-in Auth** - Login system sudah built-in
5. **Middleware** - Kontrol akses easy
6. **Artisan CLI** - Command line tool untuk generate code
7. **Blade Template** - Template engine yang powerful & intuitive
8. **Security** - Built-in protection: CSRF, SQL Injection, XSS
9. **Testing** - Easy setup untuk unit test & feature test
10. **Documentation** - Dokumentasi lengkap & community besar

**Analogi Sederhana:**
- PHP biasa = Membuat rumah dari nol, repot
- Laravel = Membuat rumah dari blueprintnya sudah ada, tinggal arrange

---

### Q6: Apa bedanya Model, Controller, dan View?

**Jawab:**

| Komponen | Fungsi | Contoh |
|----------|--------|--------|
| **Model** | Blueprint database, representasi table | `User.php` = representasi table `users` |
| **Controller** | Logic aplikasi, handle request | `UserController.php` = handle aksi user CRUD |
| **View** | Tampilan/UI yang dikirim ke browser | `user/index.blade.php` = tampilan list user |

**Alur MVC:**

```
User akses URL
    ↓
Router cek ke controller mana
    ↓
Controller jalankan logic:
    - Query database pakai model
    - Proses data
    ↓
Controller kirim data ke view
    ↓
View render HTML + data
    ↓
HTML ditampilkan ke browser
```

**Contoh Konkret:**

1. User klik "Daftar User Baru" → URL /user/create
2. Router route ke UserController::create()
3. Controller tampilkan form (view create.blade.php)
4. User isi form & submit
5. Router route ke UserController::store()
6. Controller:
   - Validasi input
   - Buat record baru pakai User model
   - Model save ke database
7. Redirect ke user.index
8. Browser tampilkan list user (view index.blade.php)

---

## Pertanyaan Database

### Q7: Jelaskan relasi antar table di database!

**Jawab:**

**Jenis Relasi dalam Project:**

1. **One-to-Many (1:N)**
   ```
   Role (1) ─── hasMany ───→ User (N)
   Satu role bisa punya banyak user
   
   User (1) ─── hasMany ───→ TransaksiParkir (N)
   Satu user bisa input banyak transaksi
   
   Member (1) ─── hasMany ───→ TransaksiParkir (N)
   Satu member bisa parkir berkali-kali
   ```

2. **Many-to-One (N:1)**
   ```
   TransaksiParkir (N) ─── belongsTo ───→ Kendaraan (1)
   Banyak transaksi milik satu kendaraan
   
   Kendaraan (N) ─── belongsTo ───→ Pemilik (1)
   Banyak kendaraan milik satu pemilik
   ```

3. **Many-to-Many (N:N)**
   ```
   (Tidak ada di project ini)
   ```

**Tabel Utama & Relasi:**

```
users ──┐
        ├─── roles (foreign key: id_role)
        │
        └─── transaksi_parkir (foreign key: id_user)
             │
             ├─── kendaraan (foreign key: id_kendaraan)
             │    │
             │    ├─── pemilik (foreign key: id_pemilik)
             │    └─── tipe_kendaraan (foreign key: id_tipe)
             │
             ├─── area_parkir (foreign key: id_area)
             │
             ├─── member (foreign key: id_member)
             │    │
             │    ├─── pemilik (foreign key: id_pemilik)
             │    └─── member_level (foreign key: id_level)
             │
             ├─── tarif_parkir (foreign key: id_tarif)
             │
             └─── metode_pembayaran (foreign key: id_metode)
```

---

### Q8: Apa itu Foreign Key dan mengapa penting?

**Jawab:**

**Foreign Key** adalah kolom di table yang mereferensi primary key di table lain. Gunanya untuk menghubungkan relasi antar table.

**Contoh:**
```sql
-- Table kendaraan punya foreign key id_pemilik
CREATE TABLE kendaraan (
    id_kendaraan INT PRIMARY KEY,
    plat_nomor VARCHAR(13),
    id_pemilik INT,
    FOREIGN KEY (id_pemilik) REFERENCES pemilik(id_pemilik)
);

-- Artinya: id_pemilik di table kendaraan HARUS ada di table pemilik
```

**Mengapa Penting?**

1. **Data Integrity** - Tidak boleh simpan id_pemilik yang tidak ada
2. **Relasi Terstruktur** - Jelas hubungan antar table
3. **Cascading Delete/Update** - Otomatis update/delete related records
4. **Query Mudah** - Bisa join antar table dengan jelas
5. **Avoid Orphan Records** - Tidak ada data yang "terlantar"

**Contoh Praktis:**
```
Tanpa FK: Bisa simpan kendaraan dengan id_pemilik = 999 (padahal pemilik tidak ada)
Dengan FK: Sistem auto reject, karena id_pemilik = 999 tidak ada di table pemilik
```

---

### Q9: Jelaskan apa itu Soft Delete dan kapan menggunakannya!

**Jawab:**

**Soft Delete** adalah teknik "hapus" data dengan cara menandai field `deleted_at` (bukan benar-benar dikosong dari database).

**Contoh:**

```sql
-- Sebelum soft delete
UPDATE users SET deleted_at = NULL WHERE id_user = 1;

-- Sesudah soft delete
UPDATE users SET deleted_at = '2026-02-06 10:30:00' WHERE id_user = 1;
-- Data masih ada, tapi ditandai sebagai deleted
```

**Kapan Menggunakan?**

| Kasus | Gunakan? | Alasan |
|------|---------|--------|
| Data penting (User, Transaksi) | ✅ | Biar bisa restore kalau salah hapus |
| Data kecil (Metode Pembayaran) | ❌ | Tidak penting untuk di-restore |
| Data dengan FK (Pemilik) | ✅ | Jaga data yang mereferensinya |

**Di Project Ini:**
Model yang pakai soft delete: User, Role, Kendaraan, Pemilik, Member, AreaParkir, TarifParkir, DetailParkir, dll.

**Keuntungan Soft Delete:**
- ✓ Bisa restore data kalau salah
- ✓ Audit trail jelas (kapan dihapus)
- ✓ Dashboard trash untuk recovery
- ✓ Data statistics tidak berubah

**Cara Implementasi:**

```php
// Di migration
Schema::create('users', function (Blueprint $table) {
    $table->softDeletes(); // Menambah kolom deleted_at
});

// Di model
use SoftDeletes;
class User extends Model {
    use SoftDeletes;
}

// Query otomatis exclude soft deleted
$users = User::all(); // Tidak termasuk yang sudah dihapus

// Query yang sudah dihapus
$users = User::onlyTrashed()->get();

// Restore data
User::where('id_user', 1)->restore();

// Permanent delete
User::where('id_user', 1)->forceDelete();
```

---

### Q10: Apa perbedaan antara Delete dan Soft Delete?

**Jawab:**

| Aspek | DELETE | SOFT DELETE |
|-------|--------|------------|
| **Method** | Benar-benar dihapus dari DB | Hanya tandai dengan deleted_at |
| **Data** | Hilang permanen | Masih ada, bisa restore |
| **Recovery** | Tidak bisa recover | Bisa restore kapan saja |
| **Query** | Tidak bisa query | Bisa query dengan onlyTrashed() |
| **Permanent** | Ya | Bisa dipermanenkan dengan forceDelete() |
| **Saat Pakai** | Data kecil/tidak penting | Data penting/critical |

**Visualisasi:**

```
DELETE:
id | name              | deleted_at
1  | John              | NULL
2  | Jane              | NULL
3  | Bob               | NULL
↓ DELETE id=3
1  | John              | NULL
2  | Jane              | NULL
(Bob hilang permanen - tidak bisa recover!)

SOFT DELETE:
id | name              | deleted_at
1  | John              | NULL
2  | Jane              | NULL
3  | Bob               | NULL
↓ SOFT DELETE id=3
id | name              | deleted_at
1  | John              | NULL
2  | Jane              | NULL
3  | Bob               | 2026-02-06 10:30:00
(Bob masih ada, bisa restore!)
```

---

## Pertanyaan Backend/Code

### Q11: Jelaskan alur login user dari request sampai redirect!

**Jawab:**

**Step-by-Step Alur Login:**

```
1. USER AKSES /login (GET REQUEST)
   ↓
   AuthController::showLoginForm()
   - Cek apakah sudah login pakai Auth::check()
   - Jika sudah login, redirect ke dashboard
   - Jika belum, return view('login')

2. USER LIHAT FORM LOGIN
   ↓
   Tampilan form dengan input:
   - Username
   - Password
   - Tombol Submit

3. USER INPUT USERNAME & PASSWORD, KLIK TOMBOL SUBMIT
   ↓
   Form submit dengan method POST ke /login

4. POST /login (POST REQUEST)
   ↓
   AuthController::login($request)
   
   a. Validasi input:
      - username harus ada
      - password harus ada
      ├─ Jika tidak valid → return back()->with('error', 'msg')
      
   b. Cari user di database:
      - $user = User::where('username', 'xxx')->first()
      ├─ Jika tidak ada → error "Username tidak ditemukan"
      
   c. Cek status user:
      - Jika status != 'aktif' → error "Akun tidak aktif"
      
   d. Cek password pakai Auth::attempt():
      ├─ Jika salah → error "Password salah"
      
   e. Jika semua benar:
      - Set session dengan $request->session()->regenerate()
      - Log activity: ActivityLog::log('login', 'User berhasil login')
      - Return redirect()->route('dashboard.index')

5. USER REDIRECT KE DASHBOARD
   ↓
   GET /dashboard
   ↓
   DashboardController::index()
   - Query statistik dari database
   - Return view dashboard dengan data statistik
   ↓
   Tampilan dashboard

SELESAI ✓
```

**Kode Relevant:**

```php
// AuthController.php
public function login(Request $request) {
    // Validasi
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('username', 'password');
    $user = User::where('username', $credentials['username'])->first();

    if (!$user) {
        return back()->with('error', 'Username tidak ditemukan');
    }

    if ($user->status !== 'aktif') {
        return back()->with('error', 'Akun tidak aktif');
    }

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        ActivityLog::log('login', 'User berhasil login');
        return redirect()->route('dashboard.index');
    }

    return back()->with('error', 'Password salah');
}
```

---

### Q12: Jelaskan alur transaksi masuk kendaraan!

**Jawab:**

**Step-by-Step Alur Transaksi Masuk:**

```
1. PETUGAS AKSES /parkir/masuk
   ↓
   TransaksiMasukController::index()
   - Query area kapasitas dan tipe kendaraan
   - Return view('pages.parkir.masuk')

2. PETUGAS LIHAT FORM MASUK
   ↓
   Tampilan form dengan:
   - Input plat nomor (dengan autocomplete)
   - Select tipe kendaraan
   - Tombol submit

3. PETUGAS INPUT PLAT NOMOR (AUTOCOMPLETE)
   ↓
   Saat petugas ketik, trigger AJAX:
   GET /parkir/autocomplete-plat?q=DK+1234
   ↓
   TransaksiMasukController::autocompletePlat()
   - Trim & uppercase keyword
   - Query kendaraan yang match: WHERE plat_nomor LIKE '%DK+1234%'
   - Return JSON array hasil
   ↓
   Browser tampilkan dropdown autocomplete
   ↓
   Petugas pilih plat dari dropdown
   ↓
   Tipe kendaraan auto-filled

4. PETUGAS KLIK SUBMIT
   ↓
   POST /parkir/masuk

5. TransaksiMasukController::store()
   
   a. Validasi input:
      - plat_nomor required & max 13
      - id_tipe required & exists
      ├─ Jika error, return back()->withErrors()
      
   b. Normalisasi plat nomor:
      - $platNomor = strtoupper(trim($request->plat_nomor))
      
   c. Cek apakah kendaraan sedang parkir:
      - Query WHERE kendaraan.plat_nomor = $platNomor AND status = 'in'
      ├─ Jika ada → Error "Kendaraan masih parkir"
      
   d. Cek/buat kendaraan:
      - Query kendaraan dengan plat yang sama
      - Jika ada:
        * Cek tipe harus sama
        * ├─ Jika tidak sama → Error "Plat terdaftar sebagai tipe lain"
        * Gunakan kendaraan yang ada
      - Jika tidak ada:
        * Buat kendaraan baru dengan create()
      
   e. Cari slot parkir:
      - Query AreaKapasitas WHERE id_tipe = $tipenya AND kapasitas > 0
      - Gunakan lockForUpdate() agar tidak double book
      ├─ Jika tidak ada → Error "Kapasitas penuh"
      
   f. Generate kode tiket:
      - Format: TKT-YYYYMMDD-NNN
      - Contoh: TKT-20260206-001
      
   g. Generate QR code:
      - Library endroid/qr-code
      - QR code berisi kode tiket
      
   h. Buat transaksi masuk:
      - INSERT ke transaksi_parkir dengan:
        * kode_tiket = generated
        * id_kendaraan = kendaraan yang dipilih
        * id_area = area yang punya slot
        * waktu_masuk = now()
        * status = 'in'
        * And lainnya
      
   i. Update kapasitas:
      - Kurangi kapasitas area: kapasitas -= 1
      
   j. Log activity:
      - ActivityLog::log('transaksi_masuk', 'Kendaraan masuk: DK 1234')
      
   k. Return view tiket masuk dengan QR code

6. BROWSER TAMPILKAN TIKET MASUK
   ↓
   View pages/parkir/tiket-masuk.blade.php
   - QR code
   - Kode tiket
   - Waktu masuk
   - Plat nomor
   - Tipe kendaraan
   - Area parkir
   - Tombol print/close

SELESAI ✓
```

**Validasi Key:**
- Cek kendaraan tidak sedang parkir
- Cek slot tersedia
- Cek tipe kendaraan match

**Database Lock:**
```php
// Gunakan lockForUpdate() untuk mencegah race condition
// Jika 2 petugas input bersamaan ke area yang sama
$kapasitas = AreaKapasitas::lockForUpdate()
    ->where('id_tipe', $tipenya)
    ->where('kapasitas', '>', 0)
    ->first();
```

---

### Q13: Jelaskan alur transaksi keluar kendaraan!

**Jawab:**

**Step-by-Step Alur Transaksi Keluar:**

```
1. PETUGAS AKSES /parkir/keluar
   ↓
   TransaksiKeluarController::index()
   - Return view('pages.parkir.keluar')

2. PETUGAS LIHAT FORM KELUAR
   ↓
   Tampilan form dengan:
   - Input kode tiket / plat nomor
   - Tombol cari

3. PETUGAS INPUT KODE TIKET / PLAT & KLIK CARI
   ↓
   AJAX GET /parkir/cari-transaksi?param=xxx
   ↓
   TransaksiKeluarController::cariTransaksi()
   - Query transaksi WHERE status = 'in' (masih parkir)
   - Filter by kode_tiket OR kendaraan.plat_nomor
   ├─ Jika tidak ada → Return null/empty
   └─ Jika ada → Return JSON data transaksi

4. JIKA TRANSAKSI DITEMUKAN
   ↓
   Browser tampilkan data transaksi:
   - Plat nomor
   - Waktu masuk
   - Durasi parkir (estimated)
   - Tipe kendaraan
   - Form metode pembayaran
   - Tombol "Proses Keluar"

5. PETUGAS PILIH METODE PEMBAYARAN & KLIK SUBMIT
   ↓
   POST /parkir/keluar

6. TransaksiKeluarController::store()
   
   a. Validasi input:
      - kode_tiket atau id_kendaraan required
      
   b. Ambil transaksi masuk:
      - Query WHERE status = 'in'
      ├─ Jika tidak ada → Error "Transaksi tidak ditemukan"
      
   c. Hitung durasi parkir:
      - $durasi = Carbon::now()->diffInHours($transaksi->waktu_masuk)
      - Jika kurang dari 1 jam, bulatkan ke 1 jam (minimum charging)
      
   d. Ambil tarif parkir:
      - Query TarifParkir WHERE id_tipe = tipenya
      - $tarif = tarif per jam
      
   e. Hitung biaya parkir:
      - $biayaParkir = durasi * tarif
      
   f. Hitung diskon (jika member):
      - Cek apakah ada member & tidak expired
      - Jika ada:
        * $diskon = (biayaParkir * memberLevel->diskon) / 100
      - Jika tidak ada:
        * $diskon = 0
      
   g. Hitung total bayar:
      - $totalBayar = biayaParkir - diskon
      
   h. Update transaksi:
      - UPDATE transaksi_parkir SET:
        * waktu_keluar = now()
        * durasi_jam = $durasi
        * diskon = $diskon
        * total_bayar = $totalBayar
        * status = 'out'
        * id_metode = $metodenya
      
   i. Kembalikan kapasitas area:
      - UPDATE area_kapasitas SET kapasitas += 1
      
   j. Generate QR code tiket keluar
      
   k. Log activity:
      - ActivityLog::log('transaksi_keluar', 'Kendaraan keluar...')
      
   l. Return view tiket keluar dengan struk pembayaran

7. BROWSER TAMPILKAN TIKET KELUAR & STRUK
   ↓
   View pages/parkir/tiket-keluar.blade.php
   - QR code
   - Kode tiket
   - Waktu masuk & keluar
   - Durasi
   - Biaya parkir
   - Diskon member (jika ada)
   - Total bayar
   - Metode pembayaran
   - Tombol print/close
   - Pesan "Terima kasih!"

SELESAI ✓
```

**Logic Penting:**

```php
// Hitung tarif dengan member discount
$tarifBase = $tarif->tarif * $durasi;

if ($transaksi->member && !$transaksi->member->isExpired()) {
    $diskon = ($tarifBase * $transaksi->member->level->diskon) / 100;
} else {
    $diskon = 0;
}

$totalBayar = $tarifBase - $diskon;
```

**Minimum Charging:**
```php
// Jika parkir kurang dari 1 jam, tetap charge 1 jam
$durasi = max(1, Carbon::now()->diffInHours($waktuMasuk));
```

---

### Q14: Apa itu Controller action (method) dalam Laravel?

**Jawab:**

**Controller Action** adalah method/fungsi dalam controller class yang menangani request tertentu.

**Struktur CRUD Controller Actions:**

```php
class UserController extends Controller {
    
    // 1. INDEX - Tampilkan list semua data
    public function index() {
        $users = User::all();
        return view('user.index', compact('users'));
    }
    
    // 2. CREATE - Tampilkan form tambah
    public function create() {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }
    
    // 3. STORE - Simpan data baru (POST dari form)
    public function store(Request $request) {
        $request->validate(['username' => 'required|unique:users']);
        User::create($request->validated());
        return redirect()->route('user.index')->with('success', 'User dibuat!');
    }
    
    // 4. SHOW - Tampilkan detail 1 user (optional)
    public function show(User $user) {
        return view('user.show', compact('user'));
    }
    
    // 5. EDIT - Tampilkan form edit
    public function edit(User $user) {
        $roles = Role::all();
        return view('user.edit', compact('user', 'roles'));
    }
    
    // 6. UPDATE - Update data (PUT dari form)
    public function update(Request $request, User $user) {
        $request->validate(['username' => 'required']);
        $user->update($request->validated());
        return redirect()->route('user.index')->with('success', 'User updated!');
    }
    
    // 7. DESTROY - Hapus data (DELETE)
    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted!');
    }
    
    // 8. TRASH (CUSTOM) - Tampilkan data yang dihapus
    public function trash() {
        $users = User::onlyTrashed()->get();
        return view('user.trash', compact('users'));
    }
    
    // 9. RESTORE (CUSTOM) - Kembalikan dari trash
    public function restore($id) {
        User::withTrashed()->find($id)->restore();
        return redirect()->route('user.trash')->with('success', 'Restored!');
    }
}
```

**HTTP Method Mapping:**

| HTTP Method | Action | Route | Fungsi |
|-----------|--------|-------|--------|
| GET | index | `/user` | Tampilkan list |
| GET | create | `/user/create` | Tampilkan form tambah |
| POST | store | `/user` | Simpan data baru |
| GET | show | `/user/{id}` | Tampilkan detail (optional) |
| GET | edit | `/user/{id}/edit` | Tampilkan form edit |
| PUT/PATCH | update | `/user/{id}` | Update data |
| DELETE | destroy | `/user/{id}` | Hapus data |

**RESTful Resource Route:**

```php
// Satu baris generate semua route di atas
Route::resource('user', UserController::class);

// Sama dengan:
Route::get('user', [UserController::class, 'index'])->name('user.index');
Route::get('user/create', [UserController::class, 'create'])->name('user.create');
Route::post('user', [UserController::class, 'store'])->name('user.store');
Route::get('user/{user}', [UserController::class, 'show'])->name('user.show');
Route::get('user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');
Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
```

---

### Q15: Jelaskan apa itu Middleware dan bagaimana cara kerjanya!

**Jawab:**

**Middleware** adalah "penjaga gerbang" yang memeriksa request sebelum sampai ke controller. Jika tidak lolos, request ditolak.

**Analogi:**
```
Request user
    ↓
Middleware 1 (cek login)
    ├─ Jika tidak login → reject (redirect ke login)
    └─ Jika login → lanjut
    ↓
Middleware 2 (cek role)
    ├─ Jika bukan admin → reject (403 error)
    └─ Jika admin → lanjut
    ↓
Controller jalankan logic
    ↓
Response balik ke user
```

**Middleware di Project:**

1. **auth** (Built-in)
   - Cek apakah user sudah login
   - Jika tidak, redirect ke login

2. **CheckRole** (Custom)
   - Cek apakah user punya role yang diizinkan
   - Jika tidak, tampilkan error 403

3. **CheckPermission** (Custom)
   - Cek apakah role punya permission
   - Jika tidak, tampilkan error 403

4. **nocache** (Custom)
   - Prevent browser caching
   - Untuk security (jang ada cached login page)

5. **guest** (Built-in)
   - Kebalikan dari auth
   - Hanya untuk user yang belum login

**Contoh Penggunaan Middleware:**

```php
// Hanya user yang login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// User login + admin role
Route::middleware(['auth', 'check.role:admin'])->group(function () {
    Route::resource('user', UserController::class);
});

// User login + admin/owner role
Route::middleware(['auth', 'check.role:admin,owner'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index']);
});

// Hanya user yang belum login
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm']);
});
```

**Cara Membuat Custom Middleware:**

```bash
# Generate middleware baru
php artisan make:middleware NamaMiddleware
```

```php
// File: app/Http/Middleware/CheckAdmin.php
<?php
namespace App\Http\Middleware;
use Closure;

class CheckAdmin {
    public function handle($request, Closure $next) {
        // Cek kondisi
        if (Auth::user()->role->role_user !== 'admin') {
            return response()->view('403', [], 403); // reject
        }
        
        return $next($request); // lanjut ke controller
    }
}
```

**Register Middleware:**

```php
// Di app/Http/Kernel.php
protected $routeMiddleware = [
    'admin' => \App\Http\Middleware\CheckAdmin::class,
];
```

**Gunakan di Route:**

```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Routes untuk admin saja
});
```

---

## Pertanyaan Frontend/UI

### Q16: Jelaskan struktur halaman Blade template secara umum!

**Jawab:**

**Struktur Umum Blade Template:**

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Parkir' }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
    
    {{-- HEADER/NAVBAR --}}
    @include('Layout.navbar')
    
    {{-- MAIN CONTENT --}}
    <div class="container mx-auto py-4">
        
        {{-- FLASH MESSAGE --}}
        @if($message = Session::get('success'))
            <div class="alert alert-success">{{ $message }}</div>
        @endif
        
        @if($message = Session::get('error'))
            <div class="alert alert-error">{{ $message }}</div>
        @endif
        
        {{-- PAGE CONTENT --}}
        {{-- Using @yield untuk layout extension --}}
        @yield('content')
        
    </div>
    
    {{-- FOOTER --}}
    <footer class="mt-10 py-4 text-center text-gray-600">
        <p>&copy; 2026 Sistem Parkir. All rights reserved.</p>
    </footer>
    
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
    
</body>
</html>
```

**Konsep Blade:**

1. **@yield** - Placeholder untuk konten dinamis
2. **@section** - Define section yang bisa di-yield
3. **@include** - Include partial template
4. **@extends** - Extend dari base layout
5. **{{ }}** - Echo variable
6. **{{ $var ?? 'default' }}** - Null coalescing
7. **@if, @else, @endif** - Kondisional
8. **@foreach** - Loop
9. **@auth, @guest** - Auth check
10. **{{ asset('path') }}** - Generate asset path

**Layout vs Page:**

```blade
{{-- layouts/app.blade.php (BASE LAYOUT) --}}
<html>
    <head>...</head>
    <body>
        <nav>...</nav>
        @yield('content')
        <footer>...</footer>
    </body>
</html>

{{-- pages/dashboard.blade.php (CHILD PAGE) --}}
@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>
    <p>Selamat datang!</p>
@endsection
```

---

### Q17: Apa saja fitur Tailwind CSS yang digunakan?

**Jawab:**

**Tailwind CSS** adalah utility-first CSS framework yang membuat design responsive lebih mudah dengan class-class siap pakai.

**Class Tailwind yang Sering Digunakan:**

| Kategori | Class | Fungsi |
|----------|-------|--------|
| **Spacing** | `p-4`, `m-4` | Padding & margin |
| **Sizing** | `w-32`, `h-auto` | Width & height |
| **Color** | `bg-blue-500`, `text-red-600` | Background & text color |
| **Flexbox** | `flex`, `justify-center`, `items-center` | Layout flexbox |
| **Grid** | `grid`, `grid-cols-3` | Grid layout |
| **Text** | `text-sm`, `font-bold`, `text-center` | Text styling |
| **Border** | `border`, `rounded-lg`, `border-gray-200` | Border styling |
| **Shadow** | `shadow-md`, `shadow-lg` | Box shadow |
| **Responsive** | `md:flex-col`, `lg:w-96` | Responsive design |
| **Hover** | `hover:bg-blue-600` | Hover state |
| **Display** | `hidden`, `block`, `inline-block` | Display property |
| **Positioning** | `relative`, `absolute`, `top-0` | Positioning |

**Contoh Penggunaan:**

```blade
{{-- Button dengan Tailwind --}}
<button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
    Klik saya
</button>

{{-- Table dengan Tailwind --}}
<table class="w-full border-collapse">
    <thead class="bg-gray-200">
        <tr>
            <th class="border px-4 py-2 text-left">Nama</th>
            <th class="border px-4 py-2 text-left">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr class="hover:bg-gray-100">
                <td class="border px-4 py-2">{{ $user->name }}</td>
                <td class="border px-4 py-2">
                    <a href="#" class="text-blue-500 hover:underline">Edit</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Responsive Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded-lg shadow">Item 1</div>
    <div class="bg-white p-4 rounded-lg shadow">Item 2</div>
    <div class="bg-white p-4 rounded-lg shadow">Item 3</div>
    <div class="bg-white p-4 rounded-lg shadow">Item 4</div>
</div>
```

**Keuntungan Tailwind:**
- ✓ Cepat membuat design tanpa CSS custom
- ✓ Responsive design mudah
- ✓ Consistent spacing & colors
- ✓ Bisa customize via config
- ✓ Dark mode support
- ✓ File size kecil (hanya class yang dipakai)

---

### Q18: Apa itu AJAX dan bagaimana penggunaannya dalam project ini?

**Jawab:**

**AJAX** = Asynchronous JavaScript And XML. Teknik untuk kirim/terima data dari server tanpa reload halaman.

**Contoh AJAX di Project:**

1. **Autocomplete Plat Nomor**

```javascript
// Saat user ketik di input plat nomor
$('#plat_nomor').on('keyup', function() {
    let keyword = $(this).val();
    
    // AJAX request
    $.ajax({
        url: '/parkir/autocomplete-plat',
        type: 'GET',
        data: { q: keyword },
        success: function(data) {
            // data = JSON array dari server
            // ['DK 1234', 'DK 5678', ...]
            // Tampilkan di dropdown
            showDropdown(data);
        }
    });
});
```

```php
// Di controller
public function autocompletePlat(Request $request) {
    $keyword = strtoupper($request->q);
    $data = Kendaraan::where('plat_nomor', 'like', "%$keyword%")
        ->limit(10)
        ->get();
    return response()->json($data);
}
```

2. **Cari Transaksi Keluar**

```javascript
// Form cari dengan input kode tiket
$('#form-cari').on('submit', function(e) {
    e.preventDefault();
    
    let kodeTiket = $('#kode_tiket').val();
    
    $.ajax({
        url: '/parkir/cari-transaksi',
        type: 'GET',
        data: { kode_tiket: kodeTiket },
        success: function(transaksi) {
            // transaksi = object dari server
            // { id: 1, plat: 'DK 1234', waktu_masuk: '2026-02-06 10:00:00', ... }
            
            // Tampilkan data di form
            $('#plat_display').text(transaksi.plat_nomor);
            $('#jenis_display').text(transaksi.tipe);
            $('#waktu_masuk_display').text(transaksi.waktu_masuk);
            
            // Show form bayar
            $('#form-bayar').show();
        },
        error: function() {
            alert('Transaksi tidak ditemukan');
        }
    });
});
```

```php
// Di controller
public function cariTransaksi(Request $request) {
    $transaksi = TransaksiParkir::where('status', 'in')
        ->where('kode_tiket', $request->kode_tiket)
        ->with(['kendaraan', 'areaParkir'])
        ->first();
    
    return response()->json($transaksi);
}
```

**Keuntungan AJAX:**
- ✓ User experience lebih smooth (tidak reload)
- ✓ Autocomplete & search real-time
- ✓ Form validation tanpa submit
- ✓ Loading data dinamis
- ✓ Aplikasi terasa lebih responsif

---

## Pertanyaan Fitur Spesifik

### Q19: Jelaskan cara kerja sistem member dan diskon!

**Jawab:**

**Member System:**

Member adalah feature untuk pelanggan setia yang mendapat potongan harga (diskon) saat parkir.

**Data Member:**

```
Member punya:
- id_member
- id_pemilik (pemilik kendaraan)
- id_level (level membership: Gold, Platinum, Silver)
- berlaku_mulai (tanggal mulai)
- berlaku_hingga (tanggal akhir/expired)
- status (aktif/expired)

MemberLevel punya:
- id_level
- level (nama: Gold, Platinum, dll)
- diskon (persentase: 10, 20, 30%)
- harga (harga paket per bulan)
- durasi_hari (lama membership)
```

**Contoh Data:**

```
Member:
- John membeli member Gold
  - Berlaku mulai: 2025-01-01
  - Berlaku hingga: 2025-02-01
  - Status: aktif

MemberLevel Gold:
  - Diskon: 20%
  - Harga: 100,000
  - Durasi: 30 hari

Jadi John dapat diskon 20% untuk setiap parkir selama 30 hari
```

**Alur Diskon:**

```
Kendaraan John (member Gold) keluar parkir
    ↓
Hitung biaya parkir normal:
    durasi = 2 jam
    tarif = 5000/jam
    biaya_normal = 2 * 5000 = 10,000
    ↓
Cek apakah punya member:
    ├─ Tidak punya member → diskon = 0
    └─ Punya member:
        ├─ Cek apakah expired:
        │   ├─ Jika expired → diskon = 0, status = 'expired'
        │   └─ Jika aktif → ambil diskon punya member
        │       diskon = (10,000 * 20%) / 100 = 2,000
    ↓
Total bayar = 10,000 - 2,000 = 8,000
```

**Kode di Controller:**

```php
// Hitung tarif
$tarifBase = $tarif->tarif * $durasi;

// Cek member & diskon
$diskon = 0;
if ($transaksi->member && !$transaksi->member->isExpired()) {
    $diskon = ($tarifBase * $transaksi->member->level->diskon) / 100;
}

// Total bayar
$totalBayar = $tarifBase - $diskon;
```

**Fitur Auto-Expire:**

Member otomatis berubah status ke 'expired' saat diakses jika sudah lewat tanggal berlaku_hingga.

```php
// Di model Member.php
protected static function booted() {
    static::retrieved(function ($member) {
        if (
            $member->status === 'aktif' &&
            now()->gt($member->berlaku_hingga)
        ) {
            $member->updateQuietly([
                'status' => 'expired'
            ]);
        }
    });
}
```

---

### Q20: Bagaimana cara generate QR code di project ini?

**Jawab:**

**Library QR Code:**

Project menggunakan library `endroid/qr-code` untuk generate QR code.

```php
// Composer.json
"require": {
    "endroid/qr-code": "^6.0"
}
```

**Cara Membuat QR Code:**

```php
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;

// Generate QR code dari kode tiket
$kodetiket = 'TKT-20260206-001';

$qrCode = new QrCode(
    data: $kodeTiket,
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 300,
    margin: 10,
    foregroundColor: new Color(0, 0, 0),      // Hitam
    backgroundColor: new Color(255, 255, 255) // Putih
);

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Simpan file
$result->saveToFile(public_path('qr/' . $kodeTiket . '.png'));

// Atau tampilkan langsung
return $result->getDataUri(); // Base64 image
```

**Penggunaan di Controller:**

```php
// TransaksiMasukController::store()
$kodetiket = $this->generateUniqueTiketCode();

// Generate QR code
$qrCode = new QrCode(
    data: $kodeTiket,
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::High,
    size: 300,
    margin: 10
);

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Simpan ke storage
$qrPath = 'qr/tiket-' . $kodeTiket . '.png';
$result->saveToFile(public_path($qrPath));

// Return view dengan path QR
return view('pages.parkir.tiket-masuk', [
    'kodeTiket' => $kodeTiket,
    'qrPath' => $qrPath,
    'transaksi' => $transaksi
]);
```

**Tampilkan di Blade:**

```blade
{{-- pages/parkir/tiket-masuk.blade.php --}}
<div class="tiket">
    <h2>Tiket Masuk Parkir</h2>
    
    {{-- QR Code --}}
    <div class="qr-code">
        <img src="{{ asset($qrPath) }}" alt="QR Code">
    </div>
    
    {{-- Kode Tiket --}}
    <p><strong>Kode Tiket:</strong> {{ $kodeTiket }}</p>
    
    {{-- Info lainnya --}}
    <p><strong>Waktu Masuk:</strong> {{ $transaksi->waktu_masuk }}</p>
    <p><strong>Plat Nomor:</strong> {{ $transaksi->kendaraan->plat_nomor }}</p>
    
    {{-- Button print --}}
    <button onclick="window.print()" class="btn btn-primary">Print</button>
</div>
```

**Kode Tiket Unique:**

```php
// Method untuk generate kode tiket unik
private function generateUniqueTiketCode() {
    $date = now()->format('Ymd');
    $lastTransaksi = TransaksiParkir::whereDate('created_at', today())
        ->latest('id_transaksi')
        ->first();
    
    $number = $lastTransaksi ? (int)substr($lastTransaksi->kode_tiket, -3) + 1 : 1;
    $number = str_pad($number, 3, '0', STR_PAD_LEFT);
    
    return "TKT-{$date}-{$number}";
    // Contoh: TKT-20260206-001, TKT-20260206-002, dll
}
```

---

### Q21: Jelaskan sistem logging/activity log dalam project!

**Jawab:**

**Activity Log** adalah fitur untuk mencatat setiap aktivitas user (login, logout, CRUD data, etc) untuk keperluan audit.

**Data Activity Log:**

```
Tabel: activity_logs
Kolom:
- id_log (primary key)
- id_user (user yang melakukan aktivitas)
- action (jenis aktivitas: login, logout, tambah_user, edit_user, dll)
- description (deskripsi detail: "User berhasil login")
- id_transaksi (referensi ke transaksi jika ada)
- metadata (data json tambahan)
- ip_address (IP address user)
- user_agent (device/browser info)
- created_at (waktu aktivitas)
```

**Cara Logging/Mencatat Aktivitas:**

**1. Static Method (Simple Logging):**

```php
// Di controller
use App\Models\ActivityLog;

// Login
ActivityLog::log('login', 'User berhasil login');

// Logout
ActivityLog::log('logout', 'User logout');

// Export
ActivityLog::log('export', 'Export laporan harian');
```

```php
// Di model ActivityLog
public static function log(
    string $action,
    string $description,
    $idTransaksi = null,
    array $metadata = []
) {
    return self::create([
        'id_user' => Auth::id(),
        'action' => $action,
        'description' => $description,
        'id_transaksi' => $idTransaksi,
        'metadata' => $metadata,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}
```

**2. Trait ActivityLogger (CRUD Logging):**

```php
// Di controller
use App\Traits\ActivityLogger;

class UserController extends Controller {
    use ActivityLogger; // Include trait
    
    public function store(Request $request) {
        $user = User::create($request->validated());
        
        // Log create
        $this->logCreate($user, 'User', [
            'role' => $user->role->role_user
        ]);
        
        return redirect()->route('user.index');
    }
    
    public function update(Request $request, User $user) {
        $original = $user->toArray();
        $user->update($request->validated());
        
        // Log update dengan mencatat perubahan
        $this->logUpdate($user, 'User', $original);
        
        return redirect()->route('user.index');
    }
    
    public function destroy(User $user) {
        $this->logDelete($user, 'User');
        $user->delete();
        return redirect()->route('user.index');
    }
}
```

```php
// Trait ActivityLogger.php
trait ActivityLogger {
    
    protected function logCreate(
        Model $model,
        string $modelName,
        array $additionalData = []
    ) {
        ActivityLog::log(
            'tambah_' . strtolower(str_replace(' ', '_', $modelName)),
            "Menambah {$modelName}: {$identifier}",
            null,
            array_merge([
                'model' => get_class($model),
                'data' => $model->toArray(),
            ], $additionalData)
        );
    }
    
    protected function logUpdate(
        Model $model,
        string $modelName,
        array $originalData = []
    ) {
        // Deteksi perubahan field
        $changes = [];
        foreach ($model->getAttributes() as $key => $value) {
            if ($originalData[$key] != $value) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $value,
                ];
            }
        }
        
        ActivityLog::log(
            'edit_' . strtolower(str_replace(' ', '_', $modelName)),
            "Mengedit {$modelName}: {$identifier}",
            null,
            ['changes' => $changes]
        );
    }
    
    protected function logDelete(Model $model, string $modelName) {
        ActivityLog::log(
            'hapus_' . strtolower(str_replace(' ', '_', $modelName)),
            "Menghapus {$modelName}: {$identifier}",
            null,
            ['deleted_data' => $model->toArray()]
        );
    }
}
```

**Contoh Activity Log yang Tercatat:**

| User | Action | Description | IP Address | Time |
|------|--------|-------------|-----------|------|
| admin1 | login | User berhasil login | 192.168.1.100 | 2026-02-06 10:00:00 |
| admin1 | tambah_user | Menambah User: john | 192.168.1.100 | 2026-02-06 10:05:00 |
| admin1 | edit_user | Mengedit User: john | 192.168.1.100 | 2026-02-06 10:10:00 |
| petugas1 | transaksi_masuk | Kendaraan masuk: DK 1234 | 192.168.1.101 | 2026-02-06 10:15:00 |
| petugas1 | transaksi_keluar | Kendaraan keluar: DK 1234 | 192.168.1.101 | 2026-02-06 12:30:00 |
| admin1 | export | Export laporan harian | 192.168.1.100 | 2026-02-06 15:00:00 |
| admin1 | logout | User logout | 192.168.1.100 | 2026-02-06 16:00:00 |

**Lihat Activity Log:**

```php
// ActivityLogController::index()
public function index() {
    $logs = ActivityLog::with('user')
        ->latest('created_at')
        ->paginate(50);
    
    return view('activity-log.index', compact('logs'));
}
```

```blade
{{-- activity-log/index.blade.php --}}
<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Action</th>
            <th>Description</th>
            <th>IP</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
            <tr>
                <td>{{ $log->user->username }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
```

**Keuntungan Activity Log:**
- ✓ Audit trail (catat siapa yang berbuat apa)
- ✓ Troubleshooting (lihat history kejadian)
- ✓ Security (detect aktivitas mencurigakan)
- ✓ Compliance (laporan untuk audit pemerintah)
- ✓ Data recovery (tahu siapa menghapus data)

---

## Pertanyaan Problem Solving

### Q22: Bagaimana mengatasi race condition saat banyak transaksi masuk bersamaan?

**Jawab:**

**Race Condition** terjadi saat 2 petugas input transaksi masuk ke area yang sama bersamaan. Bisa terjadi overbooking (kapasitas terlampaui).

**Contoh Masalah:**

```
Area A memiliki 2 slot parkir
Slot tersisa = 2

Petugas 1 & 2 input transaksi bersamaan:

Petugas 1:
- Query: SELECT kapasitas FROM area_kapasitas WHERE id_area=1
- Hasil: kapasitas = 2 ✓ (tersedia)
- (delay/waiting)

Petugas 2:
- Query: SELECT kapasitas FROM area_kapasitas WHERE id_area=1
- Hasil: kapasitas = 2 ✓ (tersedia)
- Proses transaksi, kurangi kapasitas jadi 1

Petugas 1:
- Lanjut proses transaksi, kurangi kapasitas jadi 1

MASALAH: Seharusnya kapasitas jadi 0, tapi jadi 1!
         Area overbooking 1 kendaraan!
```

**Solusi: Database Lock (Pessimistic Locking)**

Gunakan `lockForUpdate()` untuk lock row saat query area kapasitas.

```php
// TransaksiMasukController::store()

// Cari slot parkir dengan LOCK
$kapasitas = AreaKapasitas::lockForUpdate()  // <-- LOCK!
    ->where('id_tipe', $request->id_tipe)
    ->where('kapasitas', '>', 0)
    ->orderBy('kapasitas', 'desc')
    ->first();

if (!$kapasitas) {
    DB::rollBack();
    return back()->with('error', 'Kapasitas penuh');
}

// Lock ini prevent petugas lain query area yang sama
// sampe selesai transaksi ini

// ... lanjut proses transaksi ...

// Kurangi kapasitas
$kapasitas->decrement('kapasitas');

// Lock dilepas otomatis saat commit/rollback
```

**Cara Kerja Lock:**

```
Petugas 1:
- Query dengan lockForUpdate() kapasitas area A
- ✓ Lock didapat (kapasitas = 2)
- Proses transaksi
- Update kapasitas = 1
- Commit (lock dilepas)

Petugas 2 (bersamaan):
- Query dengan lockForUpdate() kapasitas area A
- ⏷ Menunggu... (lock belum dilepas)
- (Menit kemarin Petugas 1 selesai dan commit lock)
- Query lagi: kapasitas = 1 ✓ (benar!)
- Proses transaksi
- Update kapasitas = 0
- Commit

RESULT: Kapasitas benar = 0 ✓
```

**Best Practice:**

```php
// Selalu gunakan transaction + lock untuk operasi kritis
DB::beginTransaction();

try {
    // Lock resource yang critical
    $kapasitas = AreaKapasitas::lockForUpdate()
        ->where('id_tipe', $tipenya)
        ->where('kapasitas', '>', 0)
        ->firstOrFail();
    
    // Proses transaksi (singkat, jangan lama)
    $transaksi = TransaksiParkir::create([...]);
    $kapasitas->decrement('kapasitas');
    
    DB::commit();
    
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

---

### Q23: Bagaimana mengatasi jika user lupa password?

**Jawab:**

**Current System:**

Belum ada fitur reset password via email. Admin harus reset manual.

**Solusi Jangka Pendek (Current):**

```php
// UserController::editPassword()
public function editPassword(User $user) {
    return view('user.password', compact('user'));
}

// Admin input password baru untuk user
public function updatePassword(Request $request, User $user) {
    $request->validate(['password' => 'required|min:6']);
    
    $user->update([
        'password' => Hash::make($request->password)
    ]);
    
    ActivityLog::log('reset_password', 'Admin reset password user: ' . $user->username);
    
    return redirect()->route('user.index')->with('success', 'Password reset!');
}
```

**Solusi Jangka Panjang (Future):**

Implementasi "Forgot Password" feature dengan email:

```php
// 1. User request reset password
Route::post('/password/email', [PasswordResetController::class, 'sendResetEmail']);

// 2. Laravel kirim email dengan reset link
// Email berisi link: /password/reset/{token}

// 3. User klik link & input password baru
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);

// 4. Token otomatis expire setelah 60 menit
```

**Untuk implementasi:**

```bash
# Generate migration untuk password_resets table
php artisan migrate

# Generate controller
php artisan make:controller PasswordResetController

# Generate email notification
php artisan make:mail ResetPasswordMail
```

---

### Q24: Bagaimana mengatasi kendaraan yang plat nomornya sama tapi tipe berbeda?

**Jawab:**

**Masalah:**

User mencoba input kendaraan dengan plat "DK 1234" tapi:
- Database sudah ada DK 1234 sebagai Motor
- User coba input sebagai Mobil

**Solusi yang Diimplementasi:**

```php
// TransaksiMasukController::store()

// Normalisasi plat
$platNomor = strtoupper(trim($request->plat_nomor));

// Cek kendaraan existing
$kendaraan = Kendaraan::where('plat_nomor', $platNomor)->first();

if ($kendaraan) {
    // Kendaraan sudah ada - VALIDASI TIPE
    if ($kendaraan->id_tipe != $request->id_tipe) {
        // TOLAK jika tipe berbeda
        return back()
            ->withInput()
            ->with('error', 'Plat ' . $platNomor . ' terdaftar sebagai ' . 
                           $kendaraan->tipe->tipe_kendaraan . 
                           ', bukan ' . $tipe->tipe_kendaraan);
    }
    // Jika tipe sama, gunakan existing
} else {
    // Kendaraan baru - BUAT
    $kendaraan = Kendaraan::create([
        'plat_nomor' => $platNomor,
        'id_tipe' => $request->id_tipe,
        'status' => 'aktif'
    ]);
}
```

**Contoh Error Message:**

```
Petugas input:
- Plat: DK 1234
- Tipe: Mobil

❌ Error: "Plat DK 1234 terdaftar sebagai Motor, bukan Mobil"

Petugas harus:
1. Cek kendaraan di master data
2. Correction data plat nomor atau tipe
3. Atau hubungi admin
```

**Best Practice:**

Sebaiknya ada validasi di frontend juga saat user input plat:

```javascript
// Saat autocomplete dipilih, automatis fill tipe kendaraan
// Jika user ubah tipe secara manual, tampilkan warning
```

---

### Q25: Bagaimana mengatasi keadaan di mana kapasitas terlewat?

**Jawab:**

**Masalah:**

Area A kapasitas 2 slot untuk motor. Tapi somehow ada 3 motor parkir.

**Penyebab:**

1. Bug race condition (explained sebelumnya)
2. Update kapasitas manual yang salah
3. Data corruption
4. Transaksi keluar tidak tercatat (motor kabur)

**Deteksi & Solusi:**

**1. Dashboard Alert:**

```blade
{{-- Dashboard menampilkan peringatan jika terjadi overbooking --}}
@php
    $motorMasuk = TransaksiParkir::where('status', 'in')
        ->whereHas('kendaraan', fn($q) => $q->where('id_tipe', 1)) // Motor
        ->count();
    
    $kapasitasMotor = AreaKapasitas::where('id_tipe', 1)->sum('kapasitas');
    
    $overbooking = $motorMasuk > (10 - $kapasitasMotor);
@endphp

@if($overbooking)
    <div class="alert alert-warning">
        ⚠️ PERINGATAN: Ada indikasi overbooking motor!
        Motor parkir: {{ $motorMasuk }}, Kapasitas total: 10
        <button class="btn-info">Cek Detail</button>
    </div>
@endif
```

**2. Correction Page:**

Buat page untuk admin correction:

```php
// Route
Route::get('/admin/kapasitas-correction', [AdminController::class, 'kapasitasCorrection']);

// Controller
public function kapasitasCorrection() {
    $areas = AreaKapasitas::with(['area', 'tipe'])->get();
    
    foreach ($areas as $area) {
        // Real count: berapa kendaraan yang parkir
        $realCount = TransaksiParkir::where('status', 'in')
            ->where('id_area', $area->id_area)
            ->where('id_tipe', $area->id_tipe)
            ->count();
        
        // Expected count
        $expectedTotal = 10; // contoh total 10
        $expectedAvailable = $expectedTotal - $realCount;
        
        // Flag jika tidak match
        $area->kapasitas_real = $realCount;
        $area->kapasitas_expected = $expectedAvailable;
        $area->mismatch = $area->kapasitas != $expectedAvailable;
    }
    
    return view('admin.kapasitas-correction', compact('areas'));
}
```

```blade
{{-- View untuk correction --}}
<table>
    <thead>
        <tr>
            <th>Area</th>
            <th>Tipe</th>
            <th>Kapasitas DB</th>
            <th>Kendaraan Parkir</th>
            <th>Expected</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($areas as $area)
            <tr class="@if($area->mismatch) bg-red-100 @endif">
                <td>{{ $area->area->lokasi }}</td>
                <td>{{ $area->tipe->tipe_kendaraan }}</td>
                <td>{{ $area->kapasitas }}</td>
                <td>{{ $area->kapasitas_real }}</td>
                <td>{{ $area->kapasitas_expected }}</td>
                <td>
                    @if($area->mismatch)
                        ❌ Tidak Match
                    @else
                        ✓ OK
                    @endif
                </td>
                <td>
                    @if($area->mismatch)
                        <button onclick="correctKapasitas({{ $area->id_area_kapasitas }}, {{ $area->kapasitas_expected }})" 
                                class="btn-warning">
                            Auto Correct
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

```php
// Controller - auto correction
public function autoCorrectKapasitas(Request $request) {
    $areaKapasitas = AreaKapasitas::find($request->id);
    
    // Hitung ulang kapasitas
    $realCount = TransaksiParkir::where('status', 'in')
        ->where('id_area', $areaKapasitas->id_area)
        ->where('id_tipe', $areaKapasitas->id_tipe)
        ->count();
    
    $expectedCapacity = 10 - $realCount; // assumption total 10
    
    // Update
    $areaKapasitas->update(['kapasitas' => $expectedCapacity]);
    
    ActivityLog::log(
        'correction_kapasitas',
        'Correction kapasitas area: ' . $areaKapasitas->area->lokasi,
        null,
        ['old_value' => $areaKapasitas->kapasitas, 'new_value' => $expectedCapacity]
    );
    
    return response()->json(['success' => true]);
}
```

---

## Pertanyaan Troubleshooting

### Q26: Aplikasi tidak bisa connect ke database, apa yang dilakukan?

**Jawab:**

**Langkah Troubleshooting:**

**1. Cek file `.env` di root project:**

```bash
# Buka file .env
# Pastikan pengaturan database benar

DB_CONNECTION=mysql      # Database driver yang dipakai
DB_HOST=127.0.0.1        # Host database (biasanya localhost)
DB_PORT=3306             # Port MySQL (default 3306)
DB_DATABASE=parkir       # Nama database
DB_USERNAME=root         # Username database
DB_PASSWORD=             # Password database
```

**2. Pastikan database sudah dibuat:**

```bash
# Di MySQL client atau phpMyAdmin
CREATE DATABASE parkir;
```

**3. Jalankan migration:**

```bash
php artisan migrate
php artisan migrate:fresh  # Jika seeders ada
php artisan db:seed
```

**4. Test connection:**

```bash
# Cek artisan tinker
php artisan tinker

# Di dalam tinker
>>> DB::connection()->getPdo()
>>> User::count()  // Jika return angka, connection OK
```

**5. Cek error log:**

```bash
# Lihat file log
tail -f storage/logs/laravel.log

# Jika ada error, akan terlihat di sini
```

**6. Restart services:**

```bash
# Windows (Laragon)
# Klik server > Restart

# Linux/Mac
sudo systemctl restart mysql
sudo systemctl restart apache2
```

**7. Verify database driver:**

```bash
php artisan config:show database
# Pastikan driver mysql sudah enable
```

---

### Q27: Migration gagal, bagaimana cara fix?

**Jawab:**

**Kasus: Migration gagal karena foreign key error**

```bash
$ php artisan migrate

Migration failed:
ERROR: Can't create table `parkir`.`kendaraan` 
       with Foreign key `fk_pemilik`
```

**Penyebab:**

1. Parent table (pemilik) belum ada
2. Foreign key constraint salah
3. Data type tidak match antara parent & child

**Solusi:**

**1. Cek urutan migration:**

Migration harus dijalankan dalam urutan:
1. pemilik (parent)
2. kendaraan (child, punya FK ke pemilik)

```php
// Jika urutan salah, rename file migration dengan timestamp yang benar

// SALAH: 2026_01_20_create_kendaraan (jalankan duluan)
// BENAR: 2026_01_18_create_pemilik (jalankan duluan)
```

**2. Cek parent table ada:**

```php
// Di migration kendaraan, pastikan pemilik sudah ada
Schema::create('kendaraan', function (Blueprint $table) {
    $table->id('id_kendaraan');
    $table->string('plat_nomor', 13)->unique();
    $table->unsignedBigInteger('id_pemilik');
    
    // Foreign key - pastikan column & type match dengan parent
    $table->foreign('id_pemilik')
        ->references('id_pemilik')  // pastikan ini primary key pemilik
        ->on('pemilik')
        ->onDelete('cascade');  // Jika pemilik dihapus, kendaraan ikut dihapus
});
```

**3. Jika sudah buat table dengan error:**

Reset & ulangi:

```bash
# Rollback semua migration
php artisan migrate:reset

# Jika ada error saat rollback
php artisan migrate:reset --force

# Fresh (drop semua table & migrate dari awal)
php artisan migrate:fresh

# Dengan seed data
php artisan migrate:fresh --seed
```

**4. Cek foreign key constraints:**

```bash
# Di MySQL
SHOW CREATE TABLE kendaraan;

# Verifikasi FK syntax benar
# Contoh:
# CONSTRAINT `fk_kendaraan_pemilik` FOREIGN KEY (`id_pemilik`) 
# REFERENCES `pemilik` (`id_pemilik`)
```

**5. Manual create table jika stuck:**

```bash
# Connect ke MySQL
mysql -u root -p parkir

# Drop table yang error
DROP TABLE IF EXISTS kendaraan;

# Jalankan ulang migration
php artisan migrate
```

---

### Q28: Form validation gagal, bagaimana menampilkan error message?

**Jawab:**

**Validation Error Handling:**

```php
// Controller
public function store(Request $request) {
    $request->validate([
        'username' => 'required|unique:users,username',
        'password' => 'required|min:6',
        'id_role' => 'required|exists:roles,id_role',
    ]);
    
    // Jika validation gagal, otomatis redirect back dengan errors
    // ($errors variable otomatis di-inject ke view)
}
```

```blade
{{-- Blade template --}}

{{-- Cara 1: Tampilkan error per field --}}
@if($errors->has('username'))
    <span class="text-red-500">{{ $errors->first('username') }}</span>
@endif

{{-- Cara 2: Tampilkan semua error --}}
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Input dengan error highlighting --}}
<input type="text" 
       name="username" 
       value="{{ old('username') }}"
       class="@error('username') border-red-500 @enderror">

{{-- Tampilkan error message --}}
@error('username')
    <span class="text-red-500 text-sm">{{ $message }}</span>
@enderror

{{-- Cara 3: Blade @error shorthand --}}
<div>
    @error('username')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror
</div>
```

**Custom Validation Message:**

```php
// Controller
$request->validate(
    [
        'username' => 'required|unique:users',
        'password' => 'required|min:6',
    ],
    [
        'username.required' => 'Username harus diisi!',
        'username.unique' => 'Username sudah terdaftar!',
        'password.required' => 'Password tidak boleh kosong!',
        'password.min' => 'Password minimal 6 karakter!',
    ]
);
```

**Flash Message (Success/Error):**

```php
// Controller
return redirect()->route('user.index')
    ->with('success', 'User berhasil ditambahkan!');
    // atau
    ->with('error', 'Gagal menambah user!');
```

```blade
{{-- Blade --}}

{{-- Success message --}}
@if($message = Session::get('success'))
    <div class="alert alert-success">{{ $message }}</div>
@endif

{{-- Error message --}}
@if($message = Session::get('error'))
    <div class="alert alert-error">{{ $message }}</div>
@endif

{{-- Atau pakai session helper --}}
@session('success')
    <div class="alert alert-success">{{ $value }}</div>
@endsession
```

---

### Q29: Halaman loading sangat lambat, apa yang bisa dioptimasi?

**Jawab:**

**Performance Optimization:**

**1. Database Query Optimization:**

```php
// BURUK - N+1 Query Problem (loading 100 users = 101 queries)
$users = User::all();
foreach ($users as $user) {
    echo $user->role->role_user; // Query role untuk setiap user
}

// BAIK - Eager Loading (hanya 2 queries)
$users = User::with('role')->get();
foreach ($users as $user) {
    echo $user->role->role_user; // Data sudah di-load
}

// BAIK - Multiple relations
$users = User::with(['role', 'transaksiParkir'])->get();

// BAIK - Nested relations
$transaksi = TransaksiParkir::with([
    'kendaraan.pemilik',
    'kendaraan.tipe',
    'member.level'
])->get();
```

**2. Pagination untuk list besar:**

```php
// BURUK - Load semua data
$users = User::all();  // 10,000 records = slow!

// BAIK - Paginate
$users = User::paginate(50);  // Load 50 per page
```

```blade
{{-- Blade --}}
{{ $users->links() }}  {{-- Tampilkan pagination links --}}
```

**3. DB Query Caching:**

```php
// Cache result selama 1 jam
$roles = Cache::remember('all_roles', 3600, function () {
    return Role::all();
});
```

**4. Database Indexing:**

```php
// Di migration, add index untuk column yang sering di-query
Schema::create('transaksi_parkir', function (Blueprint $table) {
    $table->id();
    $table->foreignId('id_kendaraan')->indexed();  // Add index
    $table->foreignId('id_area')->indexed();
    $table->timestamp('waktu_masuk')->indexed();
    $table->timestamp('waktu_keluar')->nullable()->indexed();
    $table->enum('status', ['in', 'out'])->indexed();
});
```

**5. Frontend Optimization:**

```blade
{{-- Lazy load images --}}
<img src="{{ asset($image) }}" loading="lazy">

{{-- Minify CSS/JS --}}
<link href="{{ asset('css/app.css') }}">  {{-- Already minified by Vite --}}

{{-- Async load JS --}}
<script src="app.js" async></script>
```

**6. Caching Strategy:**

```php
// Cache hasil laporan yang heavy computation
Cache::put('daily_report_2026-02-06', $reportData, now()->addDay());

// Get cached data
$reportData = Cache::get('daily_report_2026-02-06');
```

**7. Query Optimization:**

```php
// BURUK
$transaksi = TransaksiParkir::all();
$total = $transaksi->sum('total_bayar');

// BAIK - Database menghitung langsung
$total = TransaksiParkir::sum('total_bayar');

// BAIK - Group by
$breakdown = TransaksiParkir::selectRaw('id_tipe, COUNT(*) as count, SUM(total_bayar) as total')
    ->groupBy('id_tipe')
    .get();
```

---

### Q30: Bagaimana cara debug/troubleshoot aplikasi?

**Jawab:**

**Debug Tools & Techniques:**

**1. Artisan Tinker:**

```bash
# Launch tinker (interactive shell)
php artisan tinker

# Di dalam tinker
>>> $users = User::all()
>>> $users->count()
>>> $user = User::find(1)
>>> $user->role
>>> dd($user)  # Dump & die
```

**2. dd() dan dump():**

```php
// Dump & die (hentikan eksekusi)
dd($variable);

// Hanya dump (lanjut eksekusi)
dump($variable);
dump('Message'); dump($data); dump($more);
```

**3. Log debugging:**

```php
// Write ke log file
Log::debug('User login attempt', ['username' => $username]);
Log::info('Data saved', ['id' => $user->id]);
Log::error('Connection failed', ['error' => $e->getMessage()]);

// Lihat log
tail -f storage/logs/laravel.log
```

**4. MySQL Query Log:**

```php
// Enable query logging
DB::enableQueryLog();

// Do queries
$users = User::all();
$roles = Role::all();

// Check queries yang dijalankan
dd(DB::getQueryLog());

// Output:
// [
//     ['query' => 'SELECT * FROM users', 'bindings' => []],
//     ['query' => 'SELECT * FROM roles', 'bindings' => []],
// ]
```

**5. Browser Developer Tools:**

```javascript
// Check network requests
// F12 > Network tab
// Lihat request/response dari server

// Check console errors
// F12 > Console tab
```

**6. Laravel Debugbar (optional installation):**

```bash
composer require barryvdh/laravel-debugbar --dev
```

Akan tampilkan:
- Execution time
- Database queries
- Request/Response info
- Config values

**7. Print debugging:**

```php
public function store(Request $request) {
    echo "DEBUG: Start store method";
    
    $validated = $request->validate([...]);
    echo "DEBUG: Validation passed";
    
    $user = User::create($validated);
    echo "DEBUG: User created with ID: " . $user->id;
    
    return redirect();
}
```

**8. Testing dengan test cases:**

```php
// tests/Feature/UserTest.php
public function test_create_user() {
    $response = $this->post('/user', [
        'username' => 'john',
        'password' => 'password',
        'id_role' => 1,
    ]);
    
    // Assertions
    $response->assertRedirect();
    $this->assertDatabaseHas('users', ['username' => 'john']);
}
```

```bash
# Run tests
php artisan test
```

---

## Penutup

Demikianlah 30 pertanyaan ujian yang kemungkinan besar ditanyakan oleh penguji saat presentasi project Sistem Parkir.

**Tips Saat Presentasi:**

✅ **Persiapan:**
- Pastikan aplikasi berjalan lancar sebelum presentasi
- Siapkan sample data di database
- Test fitur-fitur penting
- Siapkan "demo scenario" (misal: user login → transaksi masuk → transaksi keluar)

✅ **Saat Dijawab Pertanyaan:**
- Jelaskan dengan bahasa sederhana dan analogis
- Berikan contoh konkret dari code
- Jangan takut bilang "tidak tahu" jika ditanya diluar scope
- Tawarin solusi alternatif jika ada kemungkinan improvement

✅ **Hindari:**
- ❌ Memorize jawaban (jelaskan dengan pemahaman)
- ❌ Terlalu technical tanpa penjelasan
- ❌ Claim fitur yang belum diimplementasi
- ❌ Lambaian tangan tanpa arah (lebih baik show kode)

✅ **Saat Demo:**
- Demo dari "happy path" (scenario normal)
- Demo error handling
- Demo fitur-fitur penting
- Jangan terburu-buru, jelaskan step by step

**Semoga Sukses Ujian! 🎉**

---

**Dibuat untuk:** Siswa SMK Program Keahlian Teknik Komputer dan Informatika
**Tanggal:** 6 Februari 2026
**Total Pertanyaan:** 30 questions with answers ✓
