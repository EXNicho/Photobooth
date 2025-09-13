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

class PhotoAdminController extends Controller
{
    public function index()
    {
        $photos = Photo::query()->latest()->paginate(50);
        return view('admin.photos.index', compact('photos'));
    }

    public function create()
    {
        return view('admin.photos.create');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files' => ['required','array'],
            'files.*' => [FileRule::image()->max(10 * 1024)], // up to 10MB each (server limit may still apply)
        ]);

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
            $stored += $this->storePhotoFromContent($original, $mime, $content);
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        return back()->with('status', $stored . ' foto diunggah.');
    }

    public function importUrl(Request $request)
    {
        $data = $request->validate([
            'url' => ['required','url'],
        ]);

        $url = $this->normalizeDriveUrl($data['url']);
        $resp = Http::timeout(20)->get($url);
        if (!$resp->ok()) {
            return back()->withErrors(['url' => 'Gagal mengambil file dari URL'])->withInput();
        }
        $contentType = $resp->header('Content-Type', 'image/jpeg');
        $filename = $this->guessFilenameFromHeadersOrUrl($resp->header('Content-Disposition'), $url, $contentType);
        $this->storePhotoFromContent($filename, $contentType, $resp->body());
        return back()->with('status', 'Foto dari tautan berhasil diimpor.');
    }


    protected function storePhotoFromContent(string $originalName, ?string $mime, string $binary): int
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
        ]);

        ProcessPhoto::dispatch($photo->id)->onQueue('high');
        return 1;
    }

    protected function normalizeDriveUrl(string $url): string
    {
        // Handle various Google Drive formats, otherwise return original url
        if (preg_match('~drive\.google\.com/file/d/([\w-]+)/~', $url, $m)) {
            return 'https://drive.google.com/uc?id='.$m[1].'&export=download';
        }
        if (preg_match('~drive\.google\.com/open\?id=([\w-]+)~', $url, $m)) {
            return 'https://drive.google.com/uc?id='.$m[1].'&export=download';
        }
        return $url;
    }

    protected function guessFilenameFromHeadersOrUrl(?string $contentDisposition, string $url, ?string $contentType): string
    {
        if ($contentDisposition && preg_match('/filename="?([^";]+)"?/i', $contentDisposition, $m)) {
            return $m[1];
        }
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $basename = basename($path);
        if ($basename && strpos($basename, '.') !== false) {
            return $basename;
        }
        $ext = $contentType && str_contains($contentType, 'png') ? 'png' : 'jpg';
        return 'drive-import.'.$ext;
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
}
