<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPhoto;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoAdminController extends Controller
{
    public function index()
    {
        $photos = Photo::query()->latest()->paginate(50);
        return view('admin.photos.index', compact('photos'));
    }

    public function retry(Photo $photo)
    {
        ProcessPhoto::dispatch($photo->id)->onQueue('high');
        return back()->with('status', 'Retry dispatched');
    }

    public function regenerateQr(Photo $photo)
    {
        // memicu ulang job agar QR di-generate ulang
        ProcessPhoto::dispatch($photo->id)->onQueue('high');
        return back()->with('status', 'Regenerate QR/thumbnail dispatched');
    }
}

