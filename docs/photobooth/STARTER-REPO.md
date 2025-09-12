Photobooth Starter – One-Command Bootstrap (Windows)

Quick Start
- Open PowerShell in the folder where you want the app.
- Run:
  pwsh -f scripts/setup/bootstrap-photobooth.ps1 -ProjectName photobooth-app -Domain photobooth.test -DbName photobooth -DbUser root -DbPass ""

What it does
- composer create-project laravel/laravel {ProjectName}
- Installs deps: Sanctum, Intervention Image, Simple QrCode, Laravel WebSockets, Spatie Backup
- Merges blueprint code (models, controllers, routes, views, jobs, events)
- Publishes assets, creates queue tables, migrates, storage:link
- Registers AGENTS middleware, enables Broadcast provider, adds Pusher host/port
- Builds frontend (Echo + lightbox)

Manual steps after
- Create Laragon vhost: photobooth.test -> {ProjectName}/public
- Start workers in project folder:
  - php artisan queue:work --queue=high,default,low
  - php artisan websockets:serve
- Create Sanctum token for the watcher:
  - php artisan tinker
  - $u=App\Models\User::first(); $u->createToken('watcher')->plainTextToken;
- Start the watcher (choose one):
  - Node: set WATCH_DIR, API_URL, API_TOKEN and run scripts/watcher-node/photobooth-watcher.js
  - PowerShell: set envs and run scripts/watcher-powershell/PhotoboothWatcher.ps1

Notes
- Ensure MySQL/MariaDB DB exists and .env DB_* matches (Laragon default root user).
- If realtime doesn’t connect, check config/broadcasting.php has host/port options and env matches.
- For production, switch queue/cache to Redis and run workers as services.

