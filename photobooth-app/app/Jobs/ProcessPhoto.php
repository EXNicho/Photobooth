<?php

namespace App\Jobs;

use App\Events\PhotoCreated;
use App\Models\Photo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProcessPhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [2, 10, 30];

    public function __construct(public string $photoId) {}

    public function handle(): void
    {
        $photo = Photo::findOrFail($this->photoId);

        // Generate thumbnail (prefer uploads disk, fallback to public)
        $disk = Storage::disk('uploads');
        $srcPath = $photo->storage_path;
        if (!$disk->exists($srcPath)) {
            $disk = Storage::disk('public');
        }
        $thumbPath = preg_replace('/(\.[^.]+)$/', '_thumb$1', $srcPath);

        try {
            $img = Image::read($disk->path($srcPath));
            $img->scaleDown(1024, 1024); // max dim
            $img->save($disk->path($thumbPath), 80);
        } catch (\Throwable $e) {
            // Fallback: copy original if driver missing or processing fails
            try {
                if ($disk->exists($srcPath)) {
                    $disk->put($thumbPath, $disk->get($srcPath));
                }
            } catch (\Throwable $ignore) {}
        }

        // Generate QR in the same disk
        $qrDir = 'qrcodes';
        if (!$disk->exists($qrDir)) {
            $disk->makeDirectory($qrDir);
        }
        $qrUrl = url('/p/' . $photo->qr_token);
        $qrFile = $qrDir . '/' . $photo->qr_token . '.png';
        $png = QrCode::format('png')->size(512)->margin(1)->generate($qrUrl);
        $disk->put($qrFile, $png);

        $photo->status = 'ready';
        $photo->save();

        broadcast(new PhotoCreated($photo));
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ProcessPhoto failed: '.$e->getMessage());
        if ($photo = Photo::find($this->photoId)) {
            $photo->status = 'failed';
            $photo->save();
        }
    }
}
