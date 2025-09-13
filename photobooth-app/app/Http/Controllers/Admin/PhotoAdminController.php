<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessPhoto;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File as FileRule;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // kept for photo QR in jobs
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

use App\Services\PhotoImporter;

class PhotoAdminController extends Controller
{
    public function __construct(private readonly PhotoImporter $importer)
    {
    }
    public function index()
    {
        $photos = Photo::query()->latest()->paginate(50);
        return view('admin.photos.index', compact('photos'));
    }

    public function create()
    {
        // Get available events from photos table
        $availableEvents = DB::table('photos')
            ->select('event_id')
            ->whereNotNull('event_id')
            ->distinct()
            ->orderBy('event_id')
            ->pluck('event_id')
            ->toArray();

        return view('admin.photos.create', compact('availableEvents'));
    }

    public function upload(Request $request)
    {
        $data = $request->validate([
            'event' => ['required','string','max:120'],
            'custom_event' => ['nullable','string','max:120'],
            'files' => ['required','array'],
            'files.*' => [FileRule::image()->max(10 * 1024)], // up to 10MB each (server limit may still apply)
        ]);

        // Handle custom event input
        if ($data['event'] === '__custom__' && !empty($data['custom_event'])) {
            $data['event'] = trim($data['custom_event']);
        } elseif ($data['event'] === '__custom__') {
            return back()->withErrors(['event' => 'Silakan masukkan Event ID atau pilih dari daftar.'])->withInput();
        }

        $files = (array) $request->file('files', []);
        if (empty($files)) {
            return back()->withErrors(['files' => 'Pilih minimal satu gambar.'])->withInput();
        }

        $errors = [];
        $stored = 0;
        foreach ($files as $index => $file) {
            if (!$file->isValid()) {
                $code = $file->getError();
                $map = [
                    UPLOAD_ERR_INI_SIZE => 'Ukuran melebihi upload_max_filesize di PHP.',
                    UPLOAD_ERR_FORM_SIZE => 'Ukuran melebihi batas form (MAX_FILE_SIZE).',
                    UPLOAD_ERR_PARTIAL => 'Berkas terunggah sebagian.',
                    UPLOAD_ERR_NO_FILE => 'Tidak ada berkas yang diunggah.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara tidak ada.',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menulis berkas ke disk.',
                    UPLOAD_ERR_EXTENSION => 'Ekstensi PHP menghentikan unggahan.',
                ];
                $reason = $map[$code] ?? 'Gagal mengunggah berkas.';
                $errors['files.'.$index] = "Gagal mengunggah file: $reason";
                continue;
            }
            $content = $file->get();
            $mime = $file->getMimeType();
            $original = $file->getClientOriginalName();
            $stored += $this->importer->storeFromContent($original, $mime, $content, $data['event']);
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        return back()->with('status', $stored . ' foto diunggah.');
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

    public function approve(Photo $photo)
    {
        $photo->status = 'ready';
        if (!$photo->uploaded_at) {
            $photo->uploaded_at = now();
        }
        $photo->save();
        // Opsional: tetap jalankan proses untuk memastikan thumbnail/QR ada
        ProcessPhoto::dispatch($photo->id)->onQueue('high');
        return back()->with('status', 'Foto diset menjadi ready dan akan diproses.');
    }

    public function reject(Photo $photo)
    {
        $photo->status = 'rejected';
        $photo->save();
        return back()->with('status', 'Foto ditandai sebagai rejected.');
    }

    public function markBest(Request $request, Photo $photo)
    {
        $value = $request->boolean('value', null);
        if (is_null($value)) {
            $photo->is_best = !$photo->is_best;
        } else {
            $photo->is_best = $value;
        }
        $photo->save();
        return back()->with('status', 'Status "Terbaik" diperbarui.');
    }

    public function markFeatured(Request $request, Photo $photo)
    {
        $value = $request->has('value') ? $request->boolean('value') : !$photo->is_featured;
        $photo->is_featured = $value;
        if ($value) {
            // Pastikan langsung terlihat di Home
            $photo->status = 'ready';
            $photo->visibility = 'public';
            if (!$photo->uploaded_at) {
                $photo->uploaded_at = now();
            }
        }
        $photo->save();
        if ($value) {
            // Pastikan thumbnail/QR tersedia, namun UI tetap fallback ke gambar asli bila belum ada
            ProcessPhoto::dispatch($photo->id)->onQueue('high');
        }
        return back()->with('status', 'Status "Unggulan" diperbarui.');
    }

    public function destroy(Photo $photo)
    {
        // Hapus file di kedua disk jika ada
        $paths = [
            $photo->storage_path,
            preg_replace('/(\.[^.]+)$/', '_thumb$1', $photo->storage_path),
            'qrcodes/'.$photo->qr_token.'.png',
        ];
        foreach (['uploads','public'] as $disk) {
            foreach ($paths as $rel) {
                try {
                    if (Storage::disk($disk)->exists($rel)) {
                        Storage::disk($disk)->delete($rel);
                    }
                } catch (\Throwable $e) { /* ignore */ }
            }
        }

        $photo->delete();
        return back()->with('status', 'Foto berhasil dihapus.');
    }

    public function events()
    {
        // Collect events from photos table
        $dbEvents = DB::table('photos')
            ->select('event_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('event_id')
            ->groupBy('event_id')
            ->pluck('total', 'event_id')
            ->toArray();

        // Collect events from generated QR metadata (supports events without photos yet)
        $metaEvents = [];
        $qrMetaByEvent = [];
        try {
            foreach (Storage::disk('uploads')->files('eventqrs') as $file) {
                if (!str_ends_with($file, '.json')) continue;
                $json = json_decode(Storage::disk('uploads')->get($file), true);
                if (!is_array($json)) continue;
                if (empty($json['event']) || empty($json['token'])) continue;
                $eid = (string) $json['event'];
                $token = (string) $json['token'];
                $metaEvents[$eid] = $metaEvents[$eid] ?? 0; // merged later
                $qrMetaByEvent[$eid] = [
                    'token' => $token,
                    'url' => url('/uploads/eventqrs/'.$token.'.svg'),
                ];
            }
        } catch (\Throwable $e) { /* ignore */ }

        // Merge metadata events with db counts
        $all = [];
        foreach (array_unique(array_merge(array_keys($dbEvents), array_keys($metaEvents))) as $eid) {
            $qr = $qrMetaByEvent[$eid] ?? null;
            $all[] = (object) [
                'event_id' => $eid,
                'total' => $dbEvents[$eid] ?? 0,
                'has_qr' => (bool) $qr,
                'qr_token' => $qr['token'] ?? null,
                'qr_url' => $qr['url'] ?? null,
            ];
        }
        // Sort by event_id asc
        usort($all, fn($a,$b) => strcmp($a->event_id, $b->event_id));

        return view('admin.events.index', ['events' => $all]);
    }

    public function deleteEvent(Request $request)
    {
        $data = $request->validate([
            'event' => ['required','string'],
        ]);
        $event = $data['event'];

        // Delete photos & files for this event
        $photos = \App\Models\Photo::where('event_id', $event)->get();
        $deleted = 0;
        foreach ($photos as $photo) {
            $paths = [
                $photo->storage_path,
                preg_replace('/(\.[^.]+)$/', '_thumb$1', $photo->storage_path),
                'qrcodes/'.$photo->qr_token.'.png',
            ];
            foreach (['uploads','public'] as $disk) {
                foreach ($paths as $rel) {
                    try { if (Storage::disk($disk)->exists($rel)) Storage::disk($disk)->delete($rel); } catch (\Throwable $e) {}
                }
            }
            $photo->delete();
            $deleted++;
        }

        // Delete event QR assets (find meta JSON by event)
        try {
            foreach (Storage::disk('uploads')->files('eventqrs') as $file) {
                if (!str_ends_with($file, '.json')) continue;
                $json = json_decode(Storage::disk('uploads')->get($file), true);
                if (($json['event'] ?? null) === $event) {
                    $token = $json['token'] ?? null;
                    Storage::disk('uploads')->delete($file);
                    if ($token) {
                        Storage::disk('uploads')->delete('eventqrs/'.$token.'.svg');
                    }
                }
            }
        } catch (\Throwable $e) {}

        return back()->with('status', "Event '$event' dihapus. Foto terhapus: $deleted");
    }

    public function generateEventQr(Request $request)
    {
        $data = $request->validate([
            'event' => ['required','string','max:160'],
            'custom_event' => ['nullable','string','max:160'],
        ]);

        // Handle custom event input
        if ($data['event'] === '__custom__' && !empty($data['custom_event'])) {
            $event = trim($data['custom_event']);
        } elseif ($data['event'] === '__custom__') {
            return back()->withErrors(['event' => 'Silakan masukkan Event ID atau pilih dari daftar.'])->withInput();
        } else {
            $event = $data['event'];
        }
        $url = route('gallery', ['event' => $event]);
        // Generate QR as SVG to avoid Imagick/GD dependency
        $renderer = new ImageRenderer(
            new RendererStyle(560),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($url);
        $dir = 'eventqrs';
        if (!Storage::disk('uploads')->exists($dir)) {
            Storage::disk('uploads')->makeDirectory($dir);
        }
        $token = Str::slug($event) . '-' . substr(sha1($event), 0, 8);
        $file = $dir . '/' . $token . '.svg';
        Storage::disk('uploads')->put($file, $svg);
        // Write metadata to ensure event appears in listing even with zero photos
        $meta = [
            'event' => $event,
            'token' => $token,
            'url' => $url,
            'created_at' => now()->toIso8601String(),
        ];
        Storage::disk('uploads')->put($dir . '/' . $token . '.json', json_encode($meta));
        if ($request->boolean('download')) {
            return response()->download(public_path('uploads/'.$file), 'qr-'.$token.'.svg');
        }
        return back()->with('status', 'QR berhasil dibuat: ' . url('/uploads/'.$file));
    }
}
