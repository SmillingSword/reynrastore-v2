# DigiFlazz Setup Instructions

## 1. Update .env File

Tambahkan konfigurasi DigiFlazz berikut ke file `.env` Anda:

```env
# DigiFlazz Configuration
DIGGIE_API_URL=https://api.digiflazz.com/v1
DIGGIE_USERNAME=mokaveau.li.d6W
DIGGIE_API_KEY=fa9b6fcd-79ea-5bc9-b32c-042df0defe9b
DIGGIE_DEV_KEY=dev-a863fdd0-37de-11f0-821d-59dabea8bfa9
DIGGIE_PRODUCTION=true
DIGGIE_TIMEOUT=30
DIGGIE_DEFAULT_PROFIT=15
```

## 2. Test DigiFlazz Connection

Jalankan command berikut untuk test koneksi ke DigiFlazz:

```bash
# Test sync produk (limit 10 untuk testing)
php artisan diggie:sync-products --limit=10

# Sync semua produk
php artisan diggie:sync-products

# Update harga produk yang sudah ada
php artisan diggie:update-prices

# Check status order
php artisan diggie:check-orders
```

## 3. Cronjob Setup

Tambahkan cronjob berikut untuk update otomatis:

```bash
# Update harga setiap 6 jam
0 */6 * * * cd /path/to/your/project && php artisan diggie:update-prices

# Check order status setiap 5 menit
*/5 * * * * cd /path/to/your/project && php artisan diggie:check-orders
```

## 4. Admin Panel

Setelah sync produk berhasil, Anda dapat:

1. Login ke admin panel: `http://localhost:8000/admin`
2. Credentials: `admin@reynrastore.com` / `admin123`
3. Kelola produk di menu "Kelola Produk"
4. Set profit percentage di menu "Pengaturan"

## 5. Monitoring

- Check logs di `storage/logs/laravel.log`
- Monitor API calls dan errors
- Verify product sync results

## 6. Production Notes

- Pastikan `DIGGIE_PRODUCTION=true` untuk production
- Set `DIGGIE_PRODUCTION=false` untuk development/testing
- Monitor API rate limits
- Backup database sebelum sync besar-besaran
