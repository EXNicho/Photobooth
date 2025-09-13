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

<div class="grid gap-6 md:grid-cols-2">
  <div class="card p-6">
    <h2 class="font-semibold mb-3">Unggah File</h2>
    <form action="{{ route('admin.photos.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <input type="file" name="files[]" accept="image/*" multiple class="block w-full text-sm" required>
      <p class="text-xs text-gray-500">Catatan: Batas ukuran per file mengikuti konfigurasi server (upload_max_filesize, post_max_size). Jika terjadi kegagalan unggah, coba perkecil ukuran atau sesuaikan konfigurasi PHP.</p>
      <button type="submit" class="btn-primary">Unggah</button>
    </form>
  </div>

  <div class="card p-6">
    <h2 class="font-semibold mb-3">Impor dari Google Drive / URL Gambar</h2>
    <form action="{{ route('admin.photos.import') }}" method="POST" class="space-y-4">
      @csrf
      <label class="block text-sm text-gray-700 dark:text-gray-300" for="url">Tautan</label>
      <input id="url" name="url" type="url" value="{{ old('url') }}" placeholder="Tempel tautan berbagi Google Drive atau URL gambar" class="input w-full" required>
      <button type="submit" class="btn-secondary">Impor</button>
    </form>
    <p class="text-xs text-gray-500 mt-3">Dukungan tautan Drive: format "file/d/{id}/view" dan "open?id={id}".</p>
  </div>
</div>
@endsection
