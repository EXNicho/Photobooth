@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold">Galeri</h1>
<form method="get" action="{{ route('gallery') }}" class="flex flex-wrap gap-2 mt-3 mb-4">
  <input class="input input-wide" type="text" name="token" value="{{ request('token') }}" placeholder="Cari token">
  <input class="input w-40" type="text" name="event" value="{{ request('event') }}" placeholder="Event ID">
  <input class="input" type="date" name="date" value="{{ request('date') }}">
  <select class="select" name="sort">
    <option value="newest" @selected($sort==='newest')>Terbaru</option>
    <option value="oldest" @selected($sort==='oldest')>Terlama</option>
  </select>
  <button class="btn-primary" type="submit">Cari</button>
  <a href="{{ route('gallery') }}" class="btn-muted">Reset</a>
  </form>

<div class="grid-gallery" id="gallery-grid">
  @forelse($photos as $p)
    <a class="card group relative" href="{{ route('photos.token', $p->qr_token) }}" title="Lihat">
      <img class="img-thumb" loading="lazy" src="{{ $p->thumb_url ?? $p->public_url }}" alt="{{ $p->original_name ?? $p->filename }}">
      <span class="media-overlay"></span>
      <span class="media-icon" aria-hidden="true">🔍</span>
    </a>
  @empty
    <div class="col-span-full text-center text-gray-600 py-10">
      Belum ada foto yang cocok dengan filter.
    </div>
  @endforelse
</div>

<div class="my-4 pagination">{{ $photos->links() }}</div>

<script>
window.subscribePhotos && window.subscribePhotos('#gallery-grid', { event: @json(request('event')) });
window.enableLightbox && window.enableLightbox('#gallery-grid');
</script>
@endsection
