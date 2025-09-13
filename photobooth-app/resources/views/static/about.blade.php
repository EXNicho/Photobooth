@extends('layouts.app')

@section('content')
<div class="hero py-10">
  <div class="space-y-3 max-w-2xl">
    <h1 class="text-3xl font-semibold tracking-tight">Tentang Photobooth</h1>
    <p class="text-gray-700 dark:text-gray-300">Photobooth membantu Anda mengelola dan membagikan foto acara dengan cepat, aman, dan mudah. Unggah, moderasi, dan bagikan tautan atau QR untuk setiap foto.</p>
    <div class="flex items-center gap-2">
      <span class="badge badge-success">Cepat</span>
      <span class="badge badge-info">Real-time</span>
      <span class="badge badge-danger">Aman</span>
    </div>
  </div>
  <div>
    <img class="hero-img" src="https://dummyimage.com/420x260/efefef/aaa&text=Tentang+Kami" alt="Tentang" />
  </div>
  </div>

<h2 class="section-title">Kenapa Memilih Kami</h2>
<div class="grid md:grid-cols-3 gap-5">
  <div class="card p-5">
    <div class="flex items-center gap-2 mb-2">
      <span class="media-icon">⚡</span>
      <h3 class="font-medium">Cepat</h3>
    </div>
    <p class="text-gray-600 dark:text-gray-300">Proses unggah ringan, notifikasi real-time, dan pemrosesan otomatis (thumbnail & QR).</p>
  </div>
  <div class="card p-5">
    <div class="flex items-center gap-2 mb-2">
      <span class="media-icon">🔒</span>
      <h3 class="font-medium">Aman</h3>
    </div>
    <p class="text-gray-600 dark:text-gray-300">Kontrol visibilitas dan tautan bertanda tangan untuk unduhan yang lebih aman.</p>
  </div>
  <div class="card p-5">
    <div class="flex items-center gap-2 mb-2">
      <span class="media-icon">✨</span>
      <h3 class="font-medium">Mudah</h3>
    </div>
    <p class="text-gray-600 dark:text-gray-300">Antarmuka sederhana; bagikan via QR atau token.</p>
  </div>
</div>

<div class="mt-8 card p-6">
  <h2 class="font-semibold mb-2">Dukungan & Kontak</h2>
  <p class="text-gray-700 dark:text-gray-300">Butuh bantuan integrasi atau fitur khusus? Silakan hubungi kami melalui halaman <a class="btn-ghost" href="{{ route('contact') }}">Kontak</a>.</p>
</div>
@endsection
