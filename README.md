# Central Client Service

Layanan ini merupakan client service yang terintegrasi dengan Central Authentication System. Service ini berfungsi sebagai template dasar untuk mengimplementasikan sistem autentikasi terpusat menggunakan token dari Central Auth.

## Prasyarat

- PHP 8.2 atau lebih tinggi
- PostgreSQL
- Composer
- Node.js & NPM (untuk development)
- Redis (opsional, untuk caching)

## Instalasi

1. Clone repository ini
2. Copy file `.env.example` ke `.env`
```bash
cp .env.example .env
```
3. Install dependensi PHP menggunakan Composer
```bash
composer install
```
4. Generate application key
```bash
php artisan key:generate
```
5. Sesuaikan konfigurasi database di file `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nama_database
DB_USERNAME=username_database
DB_PASSWORD=password_database
```

6. Jalankan migrasi database
```bash
php artisan migrate
```

## Konfigurasi Central Auth

Untuk mengintegrasikan dengan Central Auth, pastikan mengisi konfigurasi berikut di file `.env`:

```env
CENTRAL_AUTH_URL="https://central.universitaspertamina.ac.id"
CENTRAL_API_URL="https://centralapi.universitaspertamina.ac.id"
CENTRAL_APP_ID="your-app-id"
CENTRAL_COOKIE_NAME="central_access_token"
```

Keterangan:
- `CENTRAL_AUTH_URL`: URL dari aplikasi Central Auth
- `CENTRAL_API_URL`: URL dari API Central Auth
- `CENTRAL_APP_ID`: ID aplikasi yang didapatkan dari Central Auth
- `CENTRAL_COOKIE_NAME`: Nama cookie untuk menyimpan access token

## Cara Kerja Autentikasi

1. Sistem menggunakan middleware `central.auth` untuk memvalidasi request API
2. Middleware akan mencari token dari:
   - Header `Authorization: Bearer [token]`
   - Cookie dengan nama sesuai `CENTRAL_COOKIE_NAME`
3. Token akan divalidasi ke Central API
4. Jika valid, sistem akan mencari user berdasarkan `code` yang diterima dari Central API
5. User akan di-set ke auth session untuk digunakan di aplikasi

## Penggunaan API

### Mengamankan Route

Untuk mengamankan route API, gunakan middleware `central.auth`:

```php
Route::group(['middleware' => ['central.auth']], function () {
    Route::get('me', [UserController::class, 'me']);
});
```

### Response Format

Sistem menggunakan trait `ApiResponse` yang menyediakan format response standar:

Sukses:
```json
{
    "success": true,
    "message": "Pesan sukses",
    "url": "URL request",
    "method": "HTTP method",
    "timestamp": "Waktu response",
    "total_data": 1,
    "data": {}
}
```

Error:
```json
{
    "success": false,
    "message": "Pesan error",
    "url": "URL request",
    "method": "HTTP method",
    "timestamp": "Waktu response"
}
```

## Model User

Pastikan model `User` memiliki kolom `code` yang sesuai dengan field identifikasi user di Central Auth (biasanya NIP/NIK).

## Development

Untuk menjalankan aplikasi dalam mode development:

```bash
php artisan serve
```

Atau menggunakan script yang sudah disediakan di composer.json:

```bash
composer run dev
```

Script ini akan menjalankan:
- PHP Server
- Queue Worker
- Log Watcher
- Vite (untuk asset compilation)

## Bantuan

Jika menemui masalah dalam implementasi, silakan hubungi tim Central Auth atau buat issue di repository ini.

## Catatan Penting

1. Selalu gunakan HTTPS di production untuk keamanan
2. Jangan share `CENTRAL_APP_ID` ke publik
3. Validasi semua input dari user
4. Gunakan error handling yang sudah disediakan di `ApiResponse` trait
5. Pastikan konfigurasi `session` dan `cache` sudah sesuai dengan kebutuhan aplikasi
6. Lakukan backup database secara berkala

## Troubleshooting

### Token Tidak Valid
- Pastikan `CENTRAL_APP_ID` sudah benar dan terdaftar di Central Auth
- Cek apakah token sudah expired
- Verifikasi format token di header/cookie sudah sesuai

### User Tidak Ditemukan
- Pastikan field `code` di tabel users sesuai dengan data di Central Auth
- Cek apakah user sudah terdaftar di database lokal
- Verifikasi response dari Central API

### Error 500 pada Response
- Cek log aplikasi di `storage/logs`
- Pastikan konfigurasi database dan koneksi sudah benar
- Verifikasi semua required services (Redis, Database) berjalan dengan baik