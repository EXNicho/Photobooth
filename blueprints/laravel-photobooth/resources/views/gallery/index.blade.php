@extends('layouts.app')

@section('content')
<h1>Galeri</h1>
<form method="get" action="{{ route('gallery') }}" style="display:flex; gap:8px; margin:12px 0;">
  <input type="text" name="token" value="{{ request('token') }}" placeholder="Cari token" style="flex:1; padding:8px; border:1px solid #ddd; border-radius:8px;">
  <input type="text" name="event" value="{{ request('event') }}" placeholder="Event ID" style="width:160px; padding:8px; border:1px solid #ddd; border-radius:8px;">
  <input type="date" name="date" value="{{ request('date') }}" style="padding:8px; border:1px solid #ddd; border-radius:8px;">
  <select name="sort" style="padding:8px; border:1px solid #ddd; border-radius:8px;">
    <option value="newest" @selected($sort==='newest')>Terbaru</option>
    <option value="oldest" @selected($sort==='oldest')>Terlama</option>
  </select>
  <button class="btn" type="submit">Cari</button>
  <a href="{{ route('gallery') }}" class="btn" style="background:#666;">Reset</a>
  </form>

<div class="grid" id="gallery-grid">
  @foreach($photos as $p)
    <div class="card">
      <a href="{{ route('photos.token', $p->qr_token) }}" title="Lihat">
        <img loading="lazy" src="{{ $p->thumb_url ?? $p->public_url }}" alt="{{ $p->original_name ?? $p->filename }}">
      </a>
    </div>
  @endforeach
</div>

<div style="margin:16px 0;">{{ $photos->links() }}</div>

<script>
window.subscribePhotos && window.subscribePhotos('#gallery-grid');
window.enableLightbox && window.enableLightbox('#gallery-grid');
</script>
@endsection
