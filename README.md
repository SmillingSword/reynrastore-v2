# Reynra Store - Game Top-Up E-Commerce Platform

Reynra Store adalah platform e-commerce untuk top-up game yang dibangun dengan Laravel dan Vue.js, terinspirasi dari desain Razer Gold dengan skema warna hijau-hitam yang keren.

## 🚀 Fitur Utama

### 🎮 Produk & Kategori
- **Dual Product System**: Produk manual dan otomatis (Diggie API)
- **Dynamic Categories**: Kategori game yang dapat dikelola
- **Form Fields**: Form input dinamis untuk setiap produk
- **Stock Management**: Manajemen stok untuk produk manual
- **Price Management**: Update harga otomatis dari Diggie API

### 💳 Payment Gateway
- **Midtrans Integration**: Payment gateway terintegrasi
- **Multiple Payment Methods**: E-wallet, Bank Transfer, Convenience Store
- **Automatic Processing**: Otomatis proses order setelah pembayaran
- **Payment Notifications**: Webhook handling untuk status pembayaran

### 🤖 Automation
- **Cronjob Price Updates**: Update harga otomatis setiap jam
- **Order Status Monitoring**: Cek status order Diggie setiap 5 menit
- **Auto Processing**: Proses otomatis untuk produk Diggie
- **Manual Processing**: Workflow manual untuk produk custom

### 🎨 Design & UI
- **Razer Gold Inspired**: Desain hijau-hitam yang modern
- **Responsive Design**: Mobile-first approach
- **Vue.js SPA**: Single Page Application yang smooth
- **Admin Panel**: Dashboard admin yang komprehensif

## 🛠️ Tech Stack

### Backend
- **Laravel 12**: PHP Framework
- **SQLite**: Database (dapat diganti ke MySQL/PostgreSQL)
- **Midtrans PHP SDK**: Payment gateway
- **Guzzle HTTP**: API client untuk Diggie
- **Laravel Scheduler**: Cronjob management

### Frontend
- **Vue.js 3**: JavaScript framework
- **Vue Router**: Client-side routing
- **Pinia**: State management
- **Tailwind CSS**: Utility-first CSS framework
- **Vite**: Build tool dan dev server

## 📦 Installation

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- NPM atau Yarn

### Setup Steps

1. **Clone Repository**
```bash
git clone <repository-url>
cd reynrastore-new
```

2. **Install Dependencies**
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

3. **Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

4. **Database Setup**
```bash
# Run migrations
php artisan migrate

# Seed initial data
php artisan db:seed
```

5. **Configuration**
Edit `.env` file dengan konfigurasi berikut:

```env
# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Midtrans Configuration
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

# Diggie API Configuration
DIGGIE_API_URL=https://api.diggie.id
DIGGIE_API_KEY=your_api_key
DIGGIE_USERNAME=your_username
DIGGIE_SIGNATURE=your_signature
```

6. **Start Development Servers**
```bash
# Terminal 1: Laravel backend
php artisan serve

# Terminal 2: Vite frontend
npm run dev
```

## 🔧 Configuration

### Midtrans Setup
1. Daftar di [Midtrans](https://midtrans.com)
2. Dapatkan Server Key dan Client Key
3. Set webhook URL: `http://your-domain.com/api/v1/payment/notification`
4. Set redirect URLs:
   - Finish: `http://your-domain.com/payment/finish`
   - Unfinish: `http://your-domain.com/payment/unfinish`
   - Error: `http://your-domain.com/payment/error`

### Diggie API Setup
1. Daftar di [Diggie](https://diggie.id)
2. Dapatkan API credentials
3. Konfigurasi di `.env` file

### Cronjob Setup
Tambahkan ke crontab server:
```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📚 API Documentation

### Public Endpoints

#### Categories
- `GET /api/v1/categories` - List categories
- `GET /api/v1/categories/{id}` - Get category
- `GET /api/v1/categories-with-products` - Categories with products

#### Products
- `GET /api/v1/products` - List products
- `GET /api/v1/products/{id}` - Get product
- `GET /api/v1/products/slug/{slug}` - Get product by slug
- `GET /api/v1/products-featured` - Featured products

#### Orders
- `POST /api/v1/orders` - Create order
- `GET /api/v1/orders/{orderNumber}/track` - Track order

#### Payment
- `POST /api/v1/payment/create` - Create payment
- `POST /api/v1/payment/notification` - Payment webhook
- `POST /api/v1/payment/status` - Check payment status

### Admin Endpoints (Protected)
- `GET /api/v1/admin/orders` - List orders
- `POST /api/v1/admin/orders/{id}/process-items` - Process order items
- `POST /api/v1/admin/orders/{id}/update-status` - Update order status
- `GET /api/v1/admin/settings` - List settings
- `POST /api/v1/admin/products/update-prices` - Update prices

## 🎯 Usage

### Customer Flow
1. **Browse Products**: Lihat kategori dan produk yang tersedia
2. **Add to Cart**: Tambahkan produk ke keranjang
3. **Checkout**: Isi form data dan informasi customer
4. **Payment**: Bayar menggunakan Midtrans
5. **Processing**: Order diproses otomatis/manual
6. **Completion**: Terima notifikasi completion

### Admin Flow
1. **Dashboard**: Monitor orders dan statistics
2. **Product Management**: Kelola produk dan kategori
3. **Order Processing**: Proses order manual
4. **Settings**: Konfigurasi profit margin dan settings

## 🔄 Automation

### Price Updates
```bash
# Manual price update
php artisan diggie:update-prices

# Force update (bypass cache)
php artisan diggie:update-prices --force
```

### Order Status Check
```bash
# Check order status
php artisan diggie:check-status

# Limit orders to check
php artisan diggie:check-status --limit=100
```

## 🎨 Customization

### Theme Colors
Edit `tailwind.config.js` untuk mengubah color scheme:
```javascript
theme: {
  extend: {
    colors: {
      primary: {
        50: '#f0fdf4',
        500: '#22c55e',
        900: '#14532d',
      }
    }
  }
}
```

### Product Types
Tambah product type baru di:
- `app/Models/Product.php` - Model
- `database/migrations/` - Migration
- `app/Services/DiggieService.php` - Processing logic

## 🚀 Deployment

### Production Setup
1. **Server Requirements**
   - PHP 8.2+ dengan extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
   - Web server (Apache/Nginx)
   - Database (MySQL/PostgreSQL)
   - SSL Certificate

2. **Environment**
```env
APP_ENV=production
APP_DEBUG=false
MIDTRANS_IS_PRODUCTION=true
```

3. **Build Assets**
```bash
npm run build
```

4. **Optimize Laravel**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔒 Security

- **CSRF Protection**: Enabled untuk semua forms
- **Rate Limiting**: API rate limiting
- **Input Validation**: Comprehensive validation
- **SQL Injection**: Protected dengan Eloquent ORM
- **XSS Protection**: Output escaping

## 🐛 Troubleshooting

### Common Issues

1. **Payment Webhook Not Working**
   - Check webhook URL di Midtrans dashboard
   - Verify server dapat menerima POST requests
   - Check logs: `storage/logs/laravel.log`

2. **Diggie API Errors**
   - Verify API credentials
   - Check network connectivity
   - Monitor rate limits

3. **Cronjob Not Running**
   - Verify crontab setup
   - Check PHP path: `which php`
   - Test manually: `php artisan schedule:run`

## 📝 Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🙏 Credits

- **Laravel**: PHP Framework
- **Vue.js**: JavaScript Framework
- **Tailwind CSS**: CSS Framework
- **Midtrans**: Payment Gateway
- **Diggie**: Game Top-up API
- **Razer Gold**: Design inspiration

## 📞 Support

Untuk support dan pertanyaan:
- Email: admin@reynrastore.com
- Documentation: [Link to docs]
- Issues: [GitHub Issues]

---

**Reynra Store** - Your trusted game top-up partner! 🎮✨
