<?php

namespace App\Console\Commands;

use App\Services\PhotoImporter;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;

class SyncGDrive extends Command
{
    protected $signature = 'photos:sync-gdrive {--folder-id=} {--event=}';

    protected $description = 'Sync images from a Google Drive folder into the app';

    public function handle(PhotoImporter $importer): int
    {
        // Rate limiting - sync at most once every 5 minutes to avoid Google API limits
        $lastSyncKey = 'gdrive_last_sync';
        $lastSync = Cache::get($lastSyncKey, 0);
        $minInterval = 5 * 60; // 5 minutes in seconds

        if (time() - $lastSync < $minInterval) {
            $this->info('Skipping sync - rate limited (last sync was less than 5 minutes ago)');
            return self::SUCCESS;
        }

        $folderId = $this->option('folder-id') ?: env('GDRIVE_FOLDER_ID') ?: \App\Models\Setting::get('gdrive_folder_id');
        if (!$folderId) {
            $this->error('GDRIVE_FOLDER_ID not set and --folder-id not provided.');
            return self::FAILURE;
        }

        $credentials = env('GDRIVE_CREDENTIALS_JSON') ?: \App\Models\Setting::get('gdrive_credentials_path');
        if (!$credentials || !file_exists($credentials)) {
            // Fallback: look at the 'local' disk root (may be storage/app/private)
            try {
                $candidate = \Illuminate\Support\Facades\Storage::disk('local')->path('secrets/google.json');
            } catch (\Throwable $e) { $candidate = null; }
            if ($candidate && file_exists($candidate)) {
                $credentials = $candidate;
                \App\Models\Setting::put('gdrive_credentials_path', $credentials);
            }
        }
        if (!$credentials || !file_exists($credentials)) {
            $this->error('GDRIVE_CREDENTIALS_JSON path invalid or missing.');
            return self::FAILURE;
        }

        // Update last sync time
        Cache::put($lastSyncKey, time(), now()->addHours(24));

        $client = new GoogleClient();
        $client->setAuthConfig($credentials);
        $client->setScopes([GoogleDrive::DRIVE_READONLY]);
        $client->setAccessType('offline');
        $verify = filter_var(env('GDRIVE_VERIFY_SSL', app()->environment('local') ? 'false' : 'true'), FILTER_VALIDATE_BOOLEAN);
        $client->setHttpClient(new GuzzleClient(['verify' => $verify]));
        $service = new GoogleDrive($client);

        $total = 0;
        $eventIdDefault = $this->option('event') ?: \App\Models\Setting::get('gdrive_default_event');

        $processFolder = function(string $fid) use ($service, $importer, &$total, $eventIdDefault, &$processFolder) {
            $pageToken = null;
            do {
                $params = [
                    'q' => sprintf("'%s' in parents and trashed = false", $fid),
                    'pageSize' => 100,
                    'supportsAllDrives' => true,
                    'includeItemsFromAllDrives' => true,
                    'fields' => 'nextPageToken, files(id, name, mimeType, size, modifiedTime, parents)'
                ];
                if ($pageToken) $params['pageToken'] = $pageToken;
                $list = $service->files->listFiles($params);
                foreach ($list->getFiles() as $file) {
                    $mime = $file->getMimeType();
                    if ($mime === 'application/vnd.google-apps.folder') {
                        // Recurse into subfolder
                        $processFolder($file->getId());
                        continue;
                    }
                    if (strpos((string)$mime, 'image/') !== 0) {
                        continue; // only images
                    }
                    try {
                        $exists = \App\Models\Photo::where('drive_file_id', $file->getId())->exists();
                        if ($exists) continue;

                        // Rate limit API calls - wait 100ms between downloads
                        usleep(100000);

                        $content = $service->files->get($file->getId(), ['alt' => 'media']);
                        $binary = $content->getBody()->getContents();
                        $importer->storeFromContent($file->getName(), $mime, $binary, $eventIdDefault, $file->getId());
                        $total++;
                        $this->info("Imported: {$file->getName()} ({$file->getId()})");
                    } catch (\Throwable $e) {
                        Log::warning('GDrive import failed: '.$e->getMessage());
                        $this->warn('Skip file due to error: '.$file->getName());
                    }
                }
                $pageToken = $list->getNextPageToken();
            } while ($pageToken);
        };

        $processFolder($folderId);

        $this->info("Done. Imported {$total} new photos.");
        return self::SUCCESS;
    }
}
