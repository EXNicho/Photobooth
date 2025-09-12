# Bootstrap Photobooth Laravel App (Windows/Laragon)
# - Creates a fresh Laravel app, merges blueprint, installs deps, and configures basics
# Usage:
#   pwsh -f scripts/setup/bootstrap-photobooth.ps1 -ProjectName photobooth-app -Domain photobooth.test -DbName photobooth -DbUser root -DbPass ""

param(
  [string]$ProjectName = 'photobooth-app',
  [string]$Domain = 'photobooth.test',
  [string]$DbName = 'photobooth',
  [string]$DbUser = 'root',
  [string]$DbPass = ''
)

function Require-Cmd($cmd) {
  $exists = (Get-Command $cmd -ErrorAction SilentlyContinue) -ne $null
  if (-not $exists) { throw "Command not found: $cmd" }
}

function Copy-Tree($src, $dst) {
  if (-not (Test-Path $src)) { throw "Source not found: $src" }
  New-Item -ItemType Directory -Force -Path $dst | Out-Null
  Copy-Item -Path (Join-Path $src '*') -Destination $dst -Recurse -Force
}

function Merge-Blueprint($appPath) {
  $bp = Join-Path $PSScriptRoot '..' | Join-Path -ChildPath '..' | Join-Path -ChildPath 'blueprints/laravel-photobooth'
  if (-not (Test-Path $bp)) { throw "Blueprint not found at $bp" }

  # Migrations
  Copy-Tree (Join-Path $bp 'database/migrations') (Join-Path $appPath 'database/migrations')
  # App code
  Copy-Tree (Join-Path $bp 'app') (Join-Path $appPath 'app')
  # Routes
  Copy-Tree (Join-Path $bp 'routes') (Join-Path $appPath 'routes')
  # Views & JS
  Copy-Tree (Join-Path $bp 'resources/views') (Join-Path $appPath 'resources/views')
  Copy-Tree (Join-Path $bp 'resources/js') (Join-Path $appPath 'resources/js')
  # Config websockets (new file)
  $wsCfg = Join-Path $bp 'config/websockets.php'
  if (Test-Path $wsCfg) { Copy-Item $wsCfg (Join-Path $appPath 'config/websockets.php') -Force }

  # .env template
  $envT = Join-Path $bp '.env.example.photobooth'
  if (Test-Path $envT) {
    $envText = Get-Content $envT -Raw
    $envText = $envText -replace 'APP_URL=.*', "APP_URL=http://$Domain"
    $envText = $envText -replace 'DB_DATABASE=.*', "DB_DATABASE=$DbName"
    $envText = $envText -replace 'DB_USERNAME=.*', "DB_USERNAME=$DbUser"
    $envText = $envText -replace 'DB_PASSWORD=.*', "DB_PASSWORD=$DbPass"
    Set-Content -Path (Join-Path $appPath '.env') -Value $envText -NoNewline
  }
}

function Inject-AgentsMiddleware($kernelPath) {
  $text = Get-Content $kernelPath -Raw
  if ($text -match 'AgentsPolicyMiddleware') { return }
  $pattern = "('web'\s*=>\s*\[)([\s\S]*?)(\])"
  $repl = { param($m) "$($m.Groups[1].Value)`n            \\App\\Http\\Middleware\\AgentsPolicyMiddleware::class,`n$($m.Groups[2].Value)$($m.Groups[3].Value)" }
  $newText = [Regex]::Replace($text, $pattern, $repl, [System.Text.RegularExpressions.RegexOptions]::Multiline)
  Set-Content -Path $kernelPath -Value $newText -NoNewline
}

function Ensure-BroadcastProvider($configAppPath) {
  $text = Get-Content $configAppPath -Raw
  if ($text -match 'App\\Providers\\BroadcastServiceProvider::class') { return }
  $pattern = '(providers\s*=>\s*\[)([\s\S]*?)(\])'
  $repl = { param($m) "$($m.Groups[1].Value)$($m.Groups[2].Value)`n        App\\Providers\\BroadcastServiceProvider::class,`n$($m.Groups[3].Value)" }
  $newText = [Regex]::Replace($text, $pattern, $repl)
  Set-Content -Path $configAppPath -Value $newText -NoNewline
}

function Maybe-Enhance-Broadcasting($appPath) {
  $snippet = Join-Path $PSScriptRoot '..' | Join-Path -ChildPath '..' | Join-Path -ChildPath 'blueprints/laravel-photobooth/config/broadcasting.snippet.php'
  $cfgPath = Join-Path $appPath 'config/broadcasting.php'
  if ((Test-Path $snippet) -and (Test-Path $cfgPath)) {
    Write-Host 'Updating config/broadcasting.php to include host/port options for websockets...'
    $cfg = Get-Content $cfgPath -Raw
    if ($cfg -notmatch "'host' => env\('PUSHER_HOST'") {
      # naive inject: replace 'options' array with snippet options
      $cfg = $cfg -replace "'options'\s*=>\s*\[[\s\S]*?\]", "'options' => [\n                'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),\n                'host' => env('PUSHER_HOST', '127.0.0.1'),\n                'port' => env('PUSHER_PORT', 6001),\n                'scheme' => env('PUSHER_SCHEME', 'http'),\n                'useTLS' => false,\n            ]"
      Set-Content -Path $cfgPath -Value $cfg -NoNewline
    }
  }
}

try {
  Require-Cmd 'php'
  Require-Cmd 'composer'
} catch { Write-Error $_; exit 1 }

$root = Get-Location
$appPath = Join-Path $root.Path $ProjectName
if (Test-Path $appPath) { Write-Error "Target exists: $appPath"; exit 1 }

Write-Host "[1/9] Creating Laravel app: $ProjectName" -ForegroundColor Cyan
composer create-project laravel/laravel $ProjectName | Out-Host

Write-Host "[2/9] Installing PHP dependencies" -ForegroundColor Cyan
Push-Location $appPath
composer require laravel/sanctum intervention/image:^3.7 simplesoftwareio/simple-qrcode beyondcode/laravel-websockets spatie/laravel-backup | Out-Host

Write-Host "[3/9] Merging blueprint files" -ForegroundColor Cyan
Pop-Location
Merge-Blueprint -appPath $appPath

Write-Host "[4/9] Publishing vendor assets and migrating" -ForegroundColor Cyan
Push-Location $appPath
php artisan key:generate | Out-Host
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force | Out-Host
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations" --force | Out-Host
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config" --force | Out-Host
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider" --force | Out-Host
php artisan queue:table | Out-Host
php artisan queue:failed-table | Out-Host
php artisan migrate | Out-Host
php artisan storage:link | Out-Host

Write-Host "[5/9] Registering middleware and broadcasting" -ForegroundColor Cyan
Inject-AgentsMiddleware (Join-Path $appPath 'app/Http/Kernel.php')
Ensure-BroadcastProvider (Join-Path $appPath 'config/app.php')
Maybe-Enhance-Broadcasting $appPath

Write-Host "[6/9] NPM install and build (this may take a while)" -ForegroundColor Cyan
if (Test-Path (Join-Path $appPath 'package.json')) {
  npm --prefix $appPath install | Out-Host
  npm --prefix $appPath run build | Out-Host
}

Write-Host "[7/9] Final optimize" -ForegroundColor Cyan
php artisan optimize | Out-Host

Write-Host "[8/9] .env adjustments" -ForegroundColor Cyan
(Get-Content (Join-Path $appPath '.env') -Raw) |
  ForEach-Object { $_ -replace 'APP_URL=.*', "APP_URL=http://$Domain" } |
  Set-Content (Join-Path $appPath '.env') -NoNewline

Write-Host "[9/9] Done." -ForegroundColor Green
Pop-Location

Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "- Create Laragon vhost: $Domain -> $ProjectName/public"
Write-Host "- Start workers: (in $ProjectName)" 
Write-Host "    php artisan queue:work --queue=high,default,low"
Write-Host "    php artisan websockets:serve"
Write-Host "- Generate a Sanctum token for the watcher: php artisan tinker -> User::first()->createToken('watcher')->plainTextToken"
Write-Host "- Start a watcher pointing to your photo output folder."

