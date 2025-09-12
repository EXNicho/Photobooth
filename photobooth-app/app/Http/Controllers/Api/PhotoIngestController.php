<?php

namespace App\Http\Controllers\Api;

use App\Events\PhotoCreated;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessPhoto;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File as FileRule;

class PhotoIngestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', FileRule::image()->max(25 * 1024)],
            'original_name' => ['nullable','string','max:255'],
            'mime' => ['nullable','string','max:100'],
            'size' => ['nullable','integer','min:0'],
            'checksum' => ['nullable','string','max:128'],
            'captured_at' => ['nullable','date'],
            'event_id' => ['nullable','string','max:64'],
            'visibility' => ['nullable','in:public,private,unlisted'],
        ]);

        $file = $data['file'];
        $serverChecksum = hash_file('sha256', $file->getPathname());
        $checksum = $data['checksum'] ?? $serverChecksum;

        // Idempotensi berdasarkan checksum
        $existing = Photo::where('checksum', $checksum)->first();
        if ($existing) {
            return response()->json(array_merge($existing->toArray(), ['existing' => true]));
        }

        $ulid = (string) Str::ulid();
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $ym = now()->format('y/m');
        $filename = $ulid . '.' . $ext;
        $storagePath = 'photos/' . $ym . '/' . $filename;

        // Simpan file ke disk public (storage/app/public)
        Storage::disk('public')->putFileAs('photos/' . $ym, $file, $filename);

        $photo = new Photo();
        $photo->id = $ulid;
        $photo->filename = $filename;
        $photo->original_name = $data['original_name'] ?? $file->getClientOriginalName();
        $photo->mime = $data['mime'] ?? $file->getMimeType();
        $photo->size = $data['size'] ?? $file->getSize();
        $photo->checksum = $checksum;
        $photo->local_path = null;
        $photo->storage_path = $storagePath;
        $photo->qr_token = Str::random(32);
        $photo->visibility = $data['visibility'] ?? 'public';
        $photo->status = 'pending';
        $photo->captured_at = $data['captured_at'] ?? null;
        $photo->uploaded_at = now();
        $photo->event_id = $data['event_id'] ?? null;
        $photo->save();

        // Dispatch job untuk thumbnail + QR
        ProcessPhoto::dispatch($photo->id)->onQueue('high');

        // Broadcast event realtime (opsional saat pending)
        try {
            broadcast(new PhotoCreated($photo));
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed: '.$e->getMessage());
        }

        return response()->json($photo, 201);
    }
}

