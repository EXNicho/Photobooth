<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'filename','original_name','mime','size','checksum','local_path','storage_path',
        'qr_token','visibility','status','captured_at','uploaded_at','event_id'
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'uploaded_at' => 'datetime',
    ];

    protected function urlFromDisk(string $disk, string $path, string $prefix): ?string
    {
        return Storage::disk($disk)->exists($path) ? ($prefix . '/' . ltrim($path, '/')) : null;
    }

    public function getPublicUrlAttribute(): ?string
    {
        $path = $this->storage_path;
        return $this->urlFromDisk('uploads', $path, '/uploads')
            ?? $this->urlFromDisk('public', $path, '/storage')
            ?? route('photos.media', ['photo' => $this->id, 'variant' => 'full']);
    }

    public function getThumbUrlAttribute(): ?string
    {
        $path = preg_replace('/(\.[^.]+)$/', '_thumb$1', $this->storage_path);
        return $this->urlFromDisk('uploads', $path, '/uploads')
            ?? $this->urlFromDisk('public', $path, '/storage');
    }

    public function getQrUrlAttribute(): ?string
    {
        $path = 'qrcodes/' . $this->qr_token . '.png';
        return $this->urlFromDisk('uploads', $path, '/uploads')
            ?? $this->urlFromDisk('public', $path, '/storage')
            ?? null;
    }
}
