@extends('layouts.app')

@section('content')
<div class="hero">
  <div>
    <h1>Temukan Foto Anda</h1>
    <p>Foto baru akan muncul otomatis dalam hitungan detik.</p>
    <a class="btn" href="{{ route('gallery') }}">Cari Foto Anda →</a>
  </div>
  <div>
    <img src="https://dummyimage.com/480x300/efefef/aaa&text=Photobooth" alt="hero" style="border-radius:12px;border:1px solid #eee;" />
  </div>
</div>

<h2>Terbaru</h2>
<div class="grid" id="latest-grid">
  @foreach($photos as $p)
    <div class="card">
      <a href="{{ route('photos.token', $p->qr_token) }}" title="Lihat">
        <img loading="lazy" src="{{ $p->thumb_url ?? $p->public_url }}" alt="{{ $p->original_name ?? $p->filename }}">
      </a>
    </div>
  @endforeach
</div>

<script>
// Laravel Echo realtime insert (opsional di home)
window.subscribePhotos && window.subscribePhotos('#latest-grid');
</script>
@endsection

