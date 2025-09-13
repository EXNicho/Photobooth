@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-2xl font-semibold">Tambah Foto</h1>
  <a href="{{ route('admin.photos.index') }}" class="btn-outline">Kembali</a>
  </div>

@if(session('status'))
  <div class="alert-success mb-4">{{ session('status') }}</div>
@endif
@if($errors->any())
  <div class="alert-error mb-4">Terjadi kesalahan. Periksa input Anda.</div>
@endif

<div class="card p-6 max-w-2xl mx-auto">
  <h2 class="font-semibold mb-3">Unggah File</h2>
  <form action="{{ route('admin.photos.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm text-gray-700 dark:text-gray-300" for="event-upload">Event ID</label>
      <select id="event-upload" name="event" required class="input w-full" onchange="toggleCustomEventInput('upload')">
        <option value="">-- Pilih Event --</option>
        @foreach($availableEvents as $event)
          <option value="{{ $event }}" {{ old('event') === $event ? 'selected' : '' }}>
            {{ $event }}
          </option>
        @endforeach
        <option value="__custom__" {{ !in_array(old('event'), $availableEvents) && old('event') ? 'selected' : '' }}>
          Event Baru (Custom)
        </option>
      </select>
      <input
        id="custom_event_upload"
        name="custom_event"
        type="text"
        class="input w-full mt-2"
        placeholder="Misal: wedding-rian-dita-2025-01-12"
        value="{{ !in_array(old('event'), $availableEvents) ? old('event') : '' }}"
        style="display: {{ !in_array(old('event'), $availableEvents) && old('event') ? 'block' : 'none' }}"
      />
      <p class="text-xs text-gray-500 mt-1">Wajib diisi. User dapat menyaring foto dengan Event ID ini di halaman Cari Foto.</p>
    </div>
    <input type="file" name="files[]" accept="image/*" multiple class="block w-full text-sm" required>
    <p class="text-xs text-gray-500">Catatan: Batas ukuran per file mengikuti konfigurasi server (upload_max_filesize, post_max_size). Jika terjadi kegagalan unggah, coba perkecil ukuran atau sesuaikan konfigurasi PHP.</p>
    <button type="submit" class="btn-primary">Unggah</button>
  </form>
</div>

<script>
function toggleCustomEventInput(formType) {
  const select = document.getElementById('event-' + formType);
  const customInput = document.getElementById('custom_event_' + formType);

  if (select.value === '__custom__') {
    customInput.style.display = 'block';
    customInput.focus();
  } else {
    customInput.style.display = 'none';
    customInput.value = '';
  }
}

// Handle form submission to process custom event
document.querySelector('form').addEventListener('submit', function(e) {
  const select = document.getElementById('event-upload');
  const customInput = document.getElementById('custom_event_upload');

  if (select.value === '__custom__' && customInput.value.trim()) {
    // Create a new option with the custom value and select it
    const newOption = document.createElement('option');
    newOption.value = customInput.value.trim();
    newOption.selected = true;
    select.appendChild(newOption);
    select.value = customInput.value.trim();
  }
});
</script>
@endsection
