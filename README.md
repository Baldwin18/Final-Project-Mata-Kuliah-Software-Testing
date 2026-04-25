# Sistem Manajemen Gudang

![CI](https://github.com/Baldwin18/Final-Project-Mata-Kuliah-Software-Testing/actions/workflows/ci.yml/badge.svg)
[![codecov](https://codecov.io/gh/Baldwin18/Final-Project-Mata-Kuliah-Software-Testing/branch/main/graph/badge.svg)](https://codecov.io/gh/Baldwin18/Final-Project-Mata-Kuliah-Software-Testing)

Aplikasi web berbasis PHP untuk mengelola data barang, transaksi stok masuk/keluar, dan laporan inventori gudang. Dibangun sebagai Final Project Mata Kuliah Software Testing.

---

## Deskripsi Aplikasi

Sistem Manajemen Gudang adalah aplikasi CRUD sederhana dengan fitur:

- **Autentikasi** — register dan login pengguna dengan password hashing
- **Data Barang** — tambah, lihat, dan hapus barang dengan validasi duplikat nama
- **Transaksi Stok** — catat stok masuk dan keluar beserta riwayatnya
- **Laporan** — ringkasan nilai inventori, barang hampir habis, dan filter transaksi per periode

**Teknologi:** PHP 8.2, MySQL (XAMPP), PHPUnit 11, GitHub Actions

---

## Struktur Repository

```
.
├── .github/
│   └── workflows/
│       └── ci.yml          # GitHub Actions CI pipeline
├── gudang/
│   ├── src/                # Business logic (testable classes)
│   │   ├── Validator.php
│   │   ├── KalkulatorStok.php
│   │   ├── BarangRepository.php
│   │   └── Database.php
│   ├── partials/           # Shared UI components
│   │   ├── sidebar.php
│   │   ├── main_style.php
│   │   └── auth_style.php
│   ├── index.php           # Dashboard
│   ├── barang.php          # Data Barang
│   ├── transaksi.php       # Transaksi
│   ├── laporan.php         # Laporan
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── config.php          # Koneksi database
│   └── gudang.sql          # Schema database
├── tests/
│   ├── Unit/
│   │   ├── ValidatorTest.php
│   │   └── KalkulatorStokTest.php
│   ├── Integration/
│   │   └── BarangRepositoryTest.php
│   └── bootstrap.php
├── composer.json
├── phpunit.xml
└── README.md
```

---

## Cara Menjalankan Aplikasi

### Prasyarat
- XAMPP (Apache + MySQL)
- PHP >= 8.1
- Composer

### Langkah

**1. Clone repository**
```bash
git clone https://github.com/Baldwin18/Final-Project-Mata-Kuliah-Software-Testing.git
cd Final-Project-Mata-Kuliah-Software-Testing
```

**2. Import database**

Buka `http://localhost/phpmyadmin`, pilih tab **Import**, pilih file `gudang/gudang.sql`, klik **Go**.

**3. Sesuaikan konfigurasi database** (jika perlu)

Edit `gudang/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // password MySQL kamu
define('DB_NAME', 'gudang');
```

**4. Copy ke htdocs**
```bash
# Windows
xcopy /E /I /Y . C:\xampp\htdocs\gudang

# Atau drag folder ke C:\xampp\htdocs\
```

**5. Buka di browser**
```
http://localhost/gudang/login.php
```

---

## Cara Menjalankan Test

Test menggunakan **PHPUnit** dengan database **SQLite in-memory** — tidak perlu MySQL berjalan.

**Install dependencies:**
```bash
composer install
```

**Jalankan semua test:**
```bash
vendor/bin/phpunit --testdox
```

**Jalankan dengan laporan coverage:**
```bash
vendor/bin/phpunit --coverage-text
```

**Jalankan hanya unit test:**
```bash
vendor/bin/phpunit --testsuite "Unit Tests" --testdox
```

**Jalankan hanya integration test:**
```bash
vendor/bin/phpunit --testsuite "Integration Tests" --testdox
```

---

## Strategi Pengujian

### Unit Testing (25 test case)

Menguji logika bisnis secara terisolasi tanpa ketergantungan database.

| File | Yang Diuji |
|------|-----------|
| `ValidatorTest.php` | Validasi nama barang, kode, harga, stok, jumlah transaksi, tipe transaksi, username, password |
| `KalkulatorStokTest.php` | Kalkulasi stok masuk/keluar, nilai inventori, barang hampir habis, rata-rata stok |

Contoh kasus yang diuji:
- Stok keluar melebihi stok tersedia → `UnderflowException`
- Nama barang kosong atau terlalu panjang → `false`
- Nilai inventori dihitung benar (stok × harga)

### Integration Testing (7 test case)

Menguji interaksi antar komponen menggunakan SQLite in-memory.

| Test | Skenario |
|------|---------|
| Insert & FindAll | Tambah barang → muncul di daftar |
| FindByNama | Cari barang case-insensitive |
| Update Stok | Update stok → tersimpan di database |
| Delete | Hapus barang → tidak ditemukan lagi |
| Alur transaksi | Masuk 20 + keluar 5 dari stok 10 = 25 |
| Duplikat nama | Insert nama sama → PDOException |
| FindAll kosong | Database kosong → array kosong |

### Coverage Target

Target minimal **60% code coverage** pada folder `gudang/src/`.

---

## CI/CD Pipeline (GitHub Actions)

Pipeline berjalan otomatis pada setiap **push** dan **pull request** ke semua branch.

```
push / pull_request
        │
        ▼
┌─────────────────────────────┐
│ 1. Checkout repository      │
│ 2. Setup PHP 8.2 + Xdebug   │
│ 3. Validate composer.json   │
│ 4. Cache vendor/            │
│ 5. composer install         │
│ 6. PHP lint check           │
│ 7. phpunit --testdox        │
│ 8. Generate coverage report │
│ 9. Upload artifact          │
│ 10. Upload ke Codecov       │
└─────────────────────────────┘
```

Laporan coverage tersedia sebagai **artifact** di tab Actions setiap kali pipeline selesai.
