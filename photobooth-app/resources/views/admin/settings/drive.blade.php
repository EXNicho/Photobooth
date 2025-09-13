@extends('layouts.app')

@section('content')
  <div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold mb-4">Pengaturan Google Drive</h1>
    @if ($errors->any())
      <div class="alert-error mb-4">
        @foreach ($errors->all() as $e)
          <div>• {{ $e }}</div>
        @endforeach
      </div>
    @endif
    @if (session('status'))
      <div class="alert-success whitespace-pre-line mb-4">{{ session('status') }}</div>
    @endif

    <div class="card p-4">
      <form id="driveForm" method="post" action="{{ route('admin.settings.drive.save') }}" class="space-y-4">
        @csrf
        <div>
          <label class="form-label" for="gdrive_folder_link">Tautan Folder Google Drive</label>
          <input class="input" id="gdrive_folder_link" name="gdrive_folder_link" type="text" placeholder="https://drive.google.com/drive/folders/…" value="{{ old('gdrive_folder_link', $link) }}" required />
          <p class="form-help">Share folder ini ke service account (Viewer). Link ini akan dipakai sampai Anda menggantinya.</p>
        </div>
        <div>
          <label class="form-label" for="gdrive_default_event">Default Event (opsional)</label>
          <select class="input" id="gdrive_default_event" name="gdrive_default_event" onchange="toggleCustomEventInput()">
            <option value="">-- Pilih Event --</option>
            @foreach($availableEvents as $event)
              <option value="{{ $event }}" {{ old('gdrive_default_event', $defaultEvent) === $event ? 'selected' : '' }}>
                {{ $event }}
              </option>
            @endforeach
            <option value="__custom__" {{ !in_array(old('gdrive_default_event', $defaultEvent), $availableEvents) && old('gdrive_default_event', $defaultEvent) ? 'selected' : '' }}>
              Event Baru (Custom)
            </option>
          </select>
          <input
            class="input mt-2"
            id="custom_event_input"
            name="custom_event"
            type="text"
            placeholder="mis. wedding-andi-sinta"
            value="{{ !in_array(old('gdrive_default_event', $defaultEvent), $availableEvents) ? old('gdrive_default_event', $defaultEvent) : '' }}"
            style="display: {{ !in_array(old('gdrive_default_event', $defaultEvent), $availableEvents) && old('gdrive_default_event', $defaultEvent) ? 'block' : 'none' }}"
          />
          <p class="form-help">Jika diisi, semua foto baru dari Drive akan di-tag ke event ini.</p>
        </div>
        <div class="flex items-center gap-2">
          <button class="btn-primary" type="submit">Simpan</button>
          <a class="btn-outline" href="{{ route('admin.photos.index') }}">Kembali</a>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">Folder ID saat ini: <code>{{ $folderId ?: '—' }}</code></div>
      </form>
    </div>

    <div class="card p-4 mt-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="font-medium">Sinkronisasi Otomatis</div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Sistem akan melakukan sync otomatis setiap 5 menit untuk menghindari rate limit Google Drive.
            <br>Klik tombol di samping untuk melakukan sync manual segera.
          </div>
        </div>
        <form id="syncForm" method="post" action="{{ route('admin.settings.drive.sync') }}">
          @csrf
          <button class="btn-primary" type="submit">Sync Now</button>
        </form>
      </div>
      <p class="form-help mt-2">Pastikan kredensial telah diatur di <code>.env</code> (<code>GDRIVE_CREDENTIALS_JSON</code>) atau gunggah di bawah.</p>
    </div>

    <div class="card p-4 mt-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="font-medium">Unggah Kredensial Service Account (JSON)</div>
          <div class="text-sm text-gray-500 dark:text-gray-400">File JSON dari Google Cloud untuk akses Drive API.</div>
        </div>
      </div>
      <form method="post" action="{{ route('admin.settings.drive.credentials') }}" enctype="multipart/form-data" class="mt-3 flex items-center gap-2">
        @csrf
        <input class="input" type="file" name="credentials" accept="application/json" required />
        <button class="btn-primary" type="submit">Unggah</button>
      </form>
      <p class="form-help mt-2">Jika diunggah di sini, sistem otomatis memakai file tersebut tanpa perlu mengubah <code>.env</code>.</p>
    </div>
  </div>

  <script>
    function toggleCustomEventInput() {
      const select = document.getElementById('gdrive_default_event');
      const customInput = document.getElementById('custom_event_input');

      if (select.value === '__custom__') {
        customInput.style.display = 'block';
        customInput.focus();
      } else {
        customInput.style.display = 'none';
        customInput.value = '';
      }
    }

    // Update form submission to handle custom event
    document.getElementById('driveForm').addEventListener('submit', function(e) {
      const select = document.getElementById('gdrive_default_event');
      const customInput = document.getElementById('custom_event_input');

      if (select.value === '__custom__' && customInput.value.trim()) {
        // Replace the select value with the custom input value
        select.innerHTML += '<option value="' + customInput.value + '" selected></option>';
        select.value = customInput.value;
      }
    });
  </script>
@endsection
