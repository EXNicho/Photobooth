<?php

namespace App\Services;

use App\Jobs\ProcessPhoto;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoImporter
{
    /**
     * Store a photo from binary content and dispatch processing.
     * Returns 1 if stored successfully.
     */
    public function storeFromContent(string $originalName, ?string $mime, string $binary, ?string $eventId = null, ?string $driveFileId = null): int
    {
        $ym = now()->format('Y/m');
        $dir = "photos/{$ym}";
        if (!Storage::disk('uploads')->exists($dir)) {
            Storage::disk('uploads')->makeDirectory($dir);
        }

        $ext = pathinfo($originalName, PATHINFO_EXTENSION) ?: 'jpg';
        $base = pathinfo($originalName, PATHINFO_FILENAME) ?: 'upload';
        $safeBase = Str::slug($base);
        $filename = $safeBase.'-'.Str::lower(Str::ulid()).'.'.$ext;
        $storagePath = $dir.'/'.$filename;
        Storage::disk('uploads')->put($storagePath, $binary);

        $photo = Photo::create([
            'filename' => $filename,
            'original_name' => $originalName,
            'mime' => $mime,
            'size' => strlen($binary),
            'storage_path' => $storagePath,
            'qr_token' => Str::random(32),
            'visibility' => 'public',
            'status' => 'ready',
            'uploaded_at' => now(),
            'event_id' => $eventId,
            'drive_file_id' => $driveFileId,
            'checksum' => hash('sha256', $binary),
        ]);

        ProcessPhoto::dispatch($photo->id)->onQueue('high');
        return 1;
    }
}

