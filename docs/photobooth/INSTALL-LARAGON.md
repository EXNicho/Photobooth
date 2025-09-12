Photobooth Laravel – Instalasi di Laragon (Windows)

Prasyarat
- Laragon (PHP 8.2+, MySQL/MariaDB) terpasang dan berjalan
- Composer terpasang dan di PATH
- Node.js 18+ untuk watcher Node (opsional, di luar PowerShell watcher)

1) Buat Proyek Laravel
- composer create-project laravel/laravel photobooth
- cd photobooth
- php artisan key:generate

2) Tambah Dependencies
- composer require laravel/sanctum
- composer require intervention/image:^3.7
- composer require simplesoftwareio/simple-qrcode
- composer require beyondcode/laravel-websockets
- composer require spatie/laravel-backup

3) Konfigurasi .env
- DB_CONNECTION=mysql
- DB_HOST=127.0.0.1
- DB_PORT=3306
- DB_DATABASE=photobooth
- DB_USERNAME=root
- DB_PASSWORD=
- APP_URL=http://photobooth.test
- SESSION_DOMAIN=photobooth.test
- SANCTUM_STATEFUL_DOMAINS=photobooth.test
- QUEUE_CONNECTION=database
- CACHE_STORE=file  (disarankan: redis jika tersedia)
- BROADCAST_DRIVER=pusher
- PUSHER_APP_ID=local
- PUSHER_APP_KEY=local
- PUSHER_APP_SECRET=local
- PUSHER_HOST=127.0.0.1
- PUSHER_PORT=6001
- PUSHER_SCHEME=http
- PUSHER_APP_CLUSTER=mt1

4) Sanctum
- php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
- php artisan migrate

5) Queue + Cache
- php artisan queue:table && php artisan queue:failed-table
- php artisan migrate
- Jalankan worker: php artisan queue:work --queue=high,default,low

6) Storage
- php artisan storage:link
- Foto akan disimpan ke storage/app/public/photos/YY/MM
- Thumbnail ke storage/app/public/photos/YY/MM/{id}_thumb.jpg
- QR code ke storage/app/public/qrcodes/{qr_token}.png

7) WebSockets (BeyondCode)
- php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations"
- php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
- php artisan migrate
- Jalankan server: php artisan websockets:serve

8) Konfigurasi Broadcasting & Echo
- resources/js/bootstrap.js: aktifkan Echo + Pusher (lihat blueprint di folder blueprints/laravel-photobooth)
- npm install && npm run build (atau npm run dev)

9) Rate Limit & CORS
- Tambahkan RateLimiter 'photobooth' (lihat snippet RouteServiceProvider di blueprint)
- Jika perlu CORS, gunakan fruitcake/laravel-cors atau baris bawaan Laravel 10+

10) Backup
- php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
- Jadwalkan backup (task scheduler Windows) untuk php artisan backup:run

11) Token API untuk Watcher
- Buat user admin/operator, lalu buat token: 
  php artisan tinker
  >>> $u=App\Models\User::first();
  >>> $u->createToken('watcher')->plainTextToken;
- Simpan token di .env watcher/skrip (jangan commit)

12) Pasang Blueprint Kode
- Salin file dari blueprints/laravel-photobooth ke struktur Laravel Anda (jalankan diff dahulu agar aman)
- Jalankan: php artisan migrate, php artisan optimize, php artisan queue:work, php artisan websockets:serve

13) Laragon Virtual Host
- Menu Laragon > Web > Create Virtual Host > photobooth.test
- Akses http://photobooth.test

Catatan Kinerja
- Gunakan Redis untuk queue+cache jika memungkinkan
- Pastikan queue worker dan websockets sebagai service (NSSM/laragon auto-start)
- Thumbnail/QR dibuat async (job), UI akan real-time menerima event foto baru

