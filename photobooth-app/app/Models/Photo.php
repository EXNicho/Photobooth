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

    public function getPublicUrlAttribute(): ?string
    {
        return Storage::disk('public')->url($this->storage_path);
    }

    public function getThumbUrlAttribute(): ?string
    {
        $path = preg_replace('/(\.[^.]+)$/', '_thumb$1', $this->storage_path);
        return Storage::disk('public')->exists($path) ? Storage::disk('public')->url($path) : null;
    }

    public function getQrUrlAttribute(): ?string
    {
        $path = 'qrcodes/' . $this->qr_token . '.png';
        return Storage::disk('public')->exists($path) ? Storage::disk('public')->url($path) : null;
    }
}

