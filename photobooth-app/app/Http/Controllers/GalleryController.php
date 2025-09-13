<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function home(Request $request)
    {
        $photos = Photo::query()
            ->where('visibility', 'public')
            ->where('status', 'ready')
            ->latest('uploaded_at')
            ->limit(12)
            ->get();
        return view('home', compact('photos'));
    }

    public function index(Request $request)
    {
        $sort = $request->string('sort', 'newest');
        $query = Photo::query()->where('visibility', 'public')->where('status', 'ready');

        if ($token = $request->string('token')->toString()) {
            $query->where('qr_token', 'like', "%$token%");
        }
        if ($event = $request->string('event')->toString()) {
            $query->where('event_id', $event);
        }
        if ($date = $request->string('date')->toString()) {
            $query->whereDate('captured_at', $date);
        }

        $query = $sort === 'oldest' ? $query->oldest('uploaded_at') : $query->latest('uploaded_at');

        $photos = $query->paginate(30)->withQueryString();
        return view('gallery.index', compact('photos', 'sort'));
    }

    public function showByToken(string $token)
    {
        $photo = Photo::where('qr_token', $token)->firstOrFail();
        return view('photo.show', compact('photo'));
    }

    public function signedDownload(Photo $photo)
    {
        $url = URL::temporarySignedRoute('photos.download.stream', now()->addMinutes(10), ['photo' => $photo->id]);
        return redirect($url);
    }

    public function streamDownload(Request $request, Photo $photo)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        return response()->file(storage_path('app/public/' . $photo->storage_path), [
            'Content-Type' => $photo->mime ?? 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$photo->filename.'"'
        ]);
    }

    public function media(Request $request, Photo $photo, string $variant = 'full')
    {
        $rel = $photo->storage_path;
        if ($variant === 'thumb') {
            $rel = preg_replace('/(\.[^.]+)$/', '_thumb$1', $rel);
        }
        $disk = Storage::disk('uploads');
        if ($disk->exists($rel)) {
            return response()->file(public_path('uploads/'.ltrim($rel,'/')));
        }
        $disk = Storage::disk('public');
        if ($disk->exists($rel)) {
            return response()->file(storage_path('app/public/'.ltrim($rel,'/')));
        }
        abort(404);
    }
}
