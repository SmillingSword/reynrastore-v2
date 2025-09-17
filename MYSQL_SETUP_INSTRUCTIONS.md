# Instruksi Setup MySQL untuk Reynrastore

## Status Setup:
✅ Database MySQL 'reynrastore_db' sudah dibuat
✅ Konfigurasi database.php sudah diubah ke MySQL

## Langkah yang perlu kamu lakukan:

### 1. Update file .env
Buka file `.env` dan ubah bagian database menjadi seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=reynrastore_db
DB_USERNAME=root
DB_PASSWORD=123
```

### 2. Jalankan command berikut setelah update .env:

```bash
# Clear cache konfigurasi
php artisan config:clear

# Jalankan migrasi untuk membuat tabel-tabel
php artisan migrate

# Jalankan seeder untuk data awal (opsional)
php artisan db:seed
```

### 3. Verifikasi koneksi database:
```bash
php artisan migrate:status
```

### 4. Jika ada error, coba command ini untuk reset:
```bash
php artisan migrate:fresh --seed
```

## Informasi Database:
- **Host**: localhost
- **Port**: 3306
- **Database**: reynrastore_db
- **Username**: root
- **Password**: 123
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

## Troubleshooting:
Jika ada masalah koneksi, pastikan:
1. MySQL service sudah running
2. Username dan password benar
3. Database reynrastore_db sudah ada (sudah dibuat otomatis)

Setelah selesai setup, kamu bisa hapus file `create_database.php` dan `MYSQL_SETUP_INSTRUCTIONS.md` ini.
