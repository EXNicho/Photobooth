<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function drive()
    {
        $link = Setting::get('gdrive_folder_link');
        $folderId = Setting::get('gdrive_folder_id');
        $defaultEvent = Setting::get('gdrive_default_event');

        // Get available events from photos table
        $availableEvents = DB::table('photos')
            ->select('event_id')
            ->whereNotNull('event_id')
            ->distinct()
            ->orderBy('event_id')
            ->pluck('event_id')
            ->toArray();

        return view('admin.settings.drive', compact('link','folderId','defaultEvent','availableEvents'));
    }

    public function saveDrive(Request $request)
    {
        $data = $request->validate([
            'gdrive_folder_link' => ['required','string','max:1000'],
            'gdrive_default_event' => ['nullable','string','max:160'],
            'custom_event' => ['nullable','string','max:160'],
        ]);

        // Handle custom event input
        if ($data['gdrive_default_event'] === '__custom__' && !empty($data['custom_event'])) {
            $data['gdrive_default_event'] = trim($data['custom_event']);
        } elseif ($data['gdrive_default_event'] === '__custom__') {
            $data['gdrive_default_event'] = null;
        }

        $folderId = $this->extractDriveFolderId($data['gdrive_folder_link']);
        if (!$folderId) {
            return back()->withErrors(['gdrive_folder_link' => 'Tautan folder Google Drive tidak valid.'])->withInput();
        }
        Setting::put('gdrive_folder_link', $data['gdrive_folder_link']);
        Setting::put('gdrive_folder_id', $folderId);
        Setting::put('gdrive_default_event', $data['gdrive_default_event'] ?? null);
        return back()->with('status', 'Pengaturan Drive disimpan. Folder ID: '.$folderId);
    }

    public function syncNow()
    {
        $folderId = Setting::get('gdrive_folder_id');
        if (!$folderId) {
            return back()->withErrors(['sync' => 'Folder ID belum diset. Simpan pengaturan terlebih dahulu.']);
        }
        $event = Setting::get('gdrive_default_event');

        // Reset rate limit cache for manual sync
        Cache::forget('gdrive_last_sync');

        // Jalankan sinkronisasi secara sinkron untuk kesederhanaan
        Artisan::call('photos:sync-gdrive', array_filter([
            '--folder-id' => $folderId,
            '--event' => $event,
        ]));
        $output = Artisan::output();
        return back()->with('status', "Sinkronisasi manual selesai.\n".trim($output));
    }

    public function uploadCredentials(Request $request)
    {
        $data = $request->validate([
            'credentials' => ['required','file','mimetypes:application/json,application/octet-stream','max:10240'],
        ]);
        $file = $data['credentials'];
        // Simpan ke storage/app/secrets/google.json
        if (!Storage::disk('local')->exists('secrets')) {
            Storage::disk('local')->makeDirectory('secrets');
        }
        $stored = $file->storeAs('secrets', 'google.json', 'local');
        $abs = Storage::disk('local')->path($stored); // honor custom 'local' root
        Setting::put('gdrive_credentials_path', $abs);
        return back()->with('status', 'Kredensial berhasil diunggah dan disimpan.');
    }

    private function extractDriveFolderId(string $link): ?string
    {
        // Pola umum: /drive/folders/{id}
        if (preg_match('~drive\.google\.com/(?:drive/folders|folders)/([\w-]+)~', $link, $m)) {
            return $m[1];
        }
        // Pola open?id={id}
        if (preg_match('~drive\.google\.com/.*[?&]id=([\w-]+)~', $link, $m)) {
            return $m[1];
        }
        return null;
    }
}
