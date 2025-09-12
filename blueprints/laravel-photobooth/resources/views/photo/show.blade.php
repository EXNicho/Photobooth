@extends('layouts.app')

@section('content')
<div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start;">
  <div class="card" style="border:none; box-shadow:none;">
    <img src="{{ $photo->public_url }}" alt="{{ $photo->original_name ?? $photo->filename }}" style="width:100%; border-radius:12px; border:1px solid #eee;">
  </div>
  <aside>
    <h2>Detail Foto</h2>
    <div>Token: <code>{{ $photo->qr_token }}</code></div>
    <div>Nama: {{ $photo->original_name ?? $photo->filename }}</div>
    <div>Ukuran: {{ number_format($photo->size/1024, 1) }} KB</div>
    <div>Tanggal: {{ optional($photo->captured_at ?? $photo->uploaded_at)->format('Y-m-d H:i') }}</div>
    <div>Event: {{ $photo->event_id ?? '-' }}</div>
    <div style="margin:12px 0;">
      <a class="btn" href="{{ route('photos.download', $photo) }}">Unduh</a>
      @if($photo->qr_url)
        <a class="btn" style="background:#444;" href="{{ $photo->qr_url }}" download>QR</a>
      @endif
    </div>
    @if($photo->qr_url)
      <img src="{{ $photo->qr_url }}" alt="QR" style="width:240px; border:1px solid #eee; border-radius:8px;" />
      <div><small>Scan untuk membuka: {{ url('/p/'.$photo->qr_token) }}</small></div>
    @endif
  </aside>
</div>
@endsection

