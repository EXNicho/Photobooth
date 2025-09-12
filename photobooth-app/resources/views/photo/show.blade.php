@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-[1fr_320px] gap-5 items-start">
  <div>
    <img class="w-full hero-img" src="{{ $photo->public_url }}" alt="{{ $photo->original_name ?? $photo->filename }}">
  </div>
  <aside class="space-y-2">
    <h2 class="text-xl font-medium">Detail Foto</h2>
    <div>Token: <code>{{ $photo->qr_token }}</code></div>
    <div>Nama: {{ $photo->original_name ?? $photo->filename }}</div>
    <div>Ukuran: {{ number_format($photo->size/1024, 1) }} KB</div>
    <div>Tanggal: {{ optional($photo->captured_at ?? $photo->uploaded_at)->format('Y-m-d H:i') }}</div>
    <div>Event: {{ $photo->event_id ?? '-' }}</div>
    <div class="my-3 space-x-2">
      <a class="btn" href="{{ route('photos.download', $photo) }}">Unduh</a>
      @if($photo->qr_url)
        <a class="btn-secondary" href="{{ $photo->qr_url }}" download>QR</a>
      @endif
    </div>
    @if($photo->qr_url)
      <img class="w-60 hero-img" src="{{ $photo->qr_url }}" alt="QR" />
      <div><small>Scan untuk membuka: {{ url('/p/'.$photo->qr_token) }}</small></div>
    @endif
  </aside>
  
</div>
@endsection
