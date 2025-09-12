Blueprint File Map (salin ke proyek Laravel Anda)

- database/migrations/2025_01_01_000000_create_photos_table.php -> database/migrations/
- app/Models/Photo.php -> app/Models/
- app/Http/Controllers/Api/PhotoIngestController.php -> app/Http/Controllers/Api/
- app/Jobs/ProcessPhoto.php -> app/Jobs/
- app/Events/PhotoCreated.php -> app/Events/
- app/Http/Controllers/GalleryController.php -> app/Http/Controllers/
- app/Http/Controllers/Admin/PhotoAdminController.php -> app/Http/Controllers/Admin/
- app/Http/Middleware/AgentsPolicyMiddleware.php -> app/Http/Middleware/
- routes/api.php -> routes/api.php (merge rute sesuai proyek Anda)
- routes/web.php -> routes/web.php (merge)
- resources/views/layouts/app.blade.php -> resources/views/layouts/
- resources/views/home.blade.php -> resources/views/
- resources/views/gallery/index.blade.php -> resources/views/gallery/
- resources/views/photo/show.blade.php -> resources/views/photo/
- resources/views/admin/photos/index.blade.php -> resources/views/admin/photos/
- resources/js/app.js -> resources/js/app.js (merge jika sudah ada)
- app/Providers/RouteServiceProvider.snippet.php -> tambahkan RateLimiter di RouteServiceProvider::configureRateLimiting()

Registrasi Middleware AgentsPolicyMiddleware
- app/Http/Kernel.php -> pada $middleware atau group 'web', tambahkan: \App\Http\Middleware\AgentsPolicyMiddleware::class

Broadcasting
- config/broadcasting.php -> gunakan pusher dan websockets
- jalankan: php artisan websockets:serve

Sanctum
- Ikuti langkah instalasi Sanctum (vendor:publish + migrate) dan gunakan token personal untuk watcher

Queue
- Gunakan database driver, jalankan php artisan queue:work --queue=high,default,low

