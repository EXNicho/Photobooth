# Photobooth Watcher (PowerShell)
# Amati folder dan kirim foto baru ke API Laravel via multipart/form-data
# Konfigurasi:
#   $env:WATCH_DIR = 'C:\Photobooth\out'
#   $env:API_URL   = 'http://photobooth.test/api/photos'
#   $env:API_TOKEN = '<token>'
#   $env:EVENT_ID  = 'event-01'

param()

$WATCH_DIR = $env:WATCH_DIR
if (-not $WATCH_DIR) { $WATCH_DIR = 'C:\Photobooth\out' }
$API_URL = $env:API_URL
if (-not $API_URL) { $API_URL = 'http://photobooth.test/api/photos' }
$API_TOKEN = $env:API_TOKEN
if (-not $API_TOKEN) { Write-Error 'API_TOKEN kosong. Set $env:API_TOKEN'; exit 1 }
$EVENT_ID = $env:EVENT_ID

function Get-Sha256Hex([string]$Path) {
  (Get-FileHash -Path $Path -Algorithm SHA256).Hash.ToLower()
}

function Send-Photo([string]$Path) {
  try {
    $checksum = Get-Sha256Hex -Path $Path
    $fileName = [System.IO.Path]::GetFileName($Path)
    $fs = [System.IO.File]::OpenRead($Path)
    $content = [System.Net.Http.MultipartFormDataContent]::new()
    $fileContent = [System.Net.Http.StreamContent]::new($fs)
    $content.Add($fileContent, 'file', $fileName)
    $content.Add([System.Net.Http.StringContent]::new($fileName), 'original_name')
    $content.Add([System.Net.Http.StringContent]::new((Get-Item $Path).Length.ToString()), 'size')
    $content.Add([System.Net.Http.StringContent]::new($checksum), 'checksum')
    if ($EVENT_ID) { $content.Add([System.Net.Http.StringContent]::new($EVENT_ID), 'event_id') }

    $client = [System.Net.Http.HttpClient]::new()
    $client.Timeout = [TimeSpan]::FromSeconds(60)
    $client.DefaultRequestHeaders.Authorization = [System.Net.Http.Headers.AuthenticationHeaderValue]::new('Bearer', $API_TOKEN)

    for ($i=1; $i -le 5; $i++) {
      $resp = $client.PostAsync($API_URL, $content).GetAwaiter().GetResult()
      if ($resp.IsSuccessStatusCode) {
        Write-Host "[OK] $fileName -> $($resp.StatusCode)"
        break
      } else {
        Write-Warning "[Retry $i] $fileName -> $($resp.StatusCode)"
        Start-Sleep -Seconds (1 * $i)
      }
    }
  } catch {
    Write-Error $_
  } finally {
    if ($fs) { $fs.Dispose() }
    if ($client) { $client.Dispose() }
  }
}

Write-Host "Watching: $WATCH_DIR"
$fsw = New-Object System.IO.FileSystemWatcher $WATCH_DIR -Property @{ IncludeSubdirectories = $false; EnableRaisingEvents = $true; Filter='*.*' }
Register-ObjectEvent $fsw Created -Action {
  $path = $Event.SourceEventArgs.FullPath
  if ($path -notmatch '\.(jpg|jpeg|png|webp|heic)$') { return }
  Start-Sleep -Milliseconds 800
  Send-Photo -Path $path
} | Out-Null

while ($true) { Start-Sleep -Seconds 1 }

