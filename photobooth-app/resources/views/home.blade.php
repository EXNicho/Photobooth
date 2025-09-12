@extends('layouts.app')

@section('content')
<div class="hero">
  <div class="space-y-3">
    <h1 class="text-3xl font-semibold tracking-tight">Temukan Foto Anda</h1>
    <p class="text-gray-600 dark:text-gray-300">Foto baru akan muncul otomatis dalam hitungan detik.</p>
    <div class="flex items-center gap-3">
      <a class="btn-primary btn-lg" href="{{ route('gallery') }}">Cari Foto Anda</a>
      <a class="btn-outline" href="{{ route('about') }}">Pelajari Lebih Lanjut</a>
    </div>
  </div>
  <div>
    <img class="hero-img" src="https://dummyimage.com/480x300/efefef/aaa&text=Photobooth" alt="hero" />
  </div>
</div>

<h2 class="section-title">Foto Terbaik</h2>
@php
  $featured = collect($photos)->take(6);
  $placeholders = max(0, 6 - $featured->count());
@endphp
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
  @foreach($featured as $p)
    <a class="card group relative card-lift {{ $loop->first ? 'col-span-2 md:col-span-3' : '' }}" href="{{ route('photos.token', $p->qr_token) }}" title="Lihat foto terbaik">
      <img class="w-full object-cover {{ $loop->first ? 'ar-16-9' : 'ar-4-3' }}" loading="lazy" src="{{ $p->thumb_url ?? $p->public_url }}" alt="{{ $p->original_name ?? $p->filename }}">
      <span class="media-overlay"></span>
      <span class="absolute top-2 left-2 badge badge-award">Terbaik</span>
    </a>
  @endforeach
  @for($i = 1; $i <= $placeholders; $i++)
    <div class="card relative card-lift {{ ($featured->count() === 0 && $i === 1) ? 'col-span-2 md:col-span-3' : '' }}">
      <img class="w-full object-cover {{ ($featured->count() === 0 && $i === 1) ? 'ar-16-9' : 'ar-4-3' }}" src="https://dummyimage.com/800x600/eaeaea/aaa&text=Top+Shot+{{$i}}" alt="Contoh foto terbaik {{$i}}">
      <span class="media-overlay"></span>
      <span class="absolute top-2 left-2 badge badge-award">Terbaik</span>
    </div>
  @endfor
</div>

<h2 class="section-title">Pilihan Unggulan</h2>
@php
  $featured2 = collect($photos)->skip(6)->take(6);
  $placeholders2 = max(0, 6 - $featured2->count());
@endphp
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
  @foreach($featured2 as $p)
    <a class="card group relative card-lift" href="{{ route('photos.token', $p->qr_token) }}" title="Lihat foto unggulan">
      <img class="w-full object-cover ar-4-3" loading="lazy" src="{{ $p->thumb_url ?? $p->public_url }}" alt="{{ $p->original_name ?? $p->filename }}">
      <span class="media-overlay"></span>
      <span class="absolute top-2 left-2 badge badge-award">Unggulan</span>
    </a>
  @endforeach
  @for($i = 1; $i <= $placeholders2; $i++)
    <div class="card relative card-lift">
      <img class="w-full object-cover ar-4-3" src="https://dummyimage.com/800x600/eaeaea/aaa&text=Featured+{{$i}}" alt="Contoh foto unggulan {{$i}}">
      <span class="media-overlay"></span>
      <span class="absolute top-2 left-2 badge badge-award">Unggulan</span>
    </div>
  @endfor
</div>

<h2 class="section-title">Terbaru</h2>
<div class="grid-gallery" id="latest-grid">
  @foreach($photos as $p)
    <a class="card" href="{{ route('photos.token', $p->qr_token) }}" title="Lihat">
      <img class="img-thumb" loading="lazy" src="{{ $p->thumb_url ?? $p->public_url }}" alt="{{ $p->original_name ?? $p->filename }}">
    </a>
  @endforeach
</div>

<script>
// Realtime insert (opsional di home)
window.subscribePhotos && window.subscribePhotos('#latest-grid');
</script>
@endsection

