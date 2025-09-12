@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-3">Tentang Photobooth</h1>
<p class="text-gray-700 mb-4">Photobooth membantu Anda mengelola dan membagikan foto acara dengan cepat, aman, dan mudah. Unggah, moderasi, dan bagikan tautan atau QR untuk setiap foto.</p>

<div class="grid md:grid-cols-3 gap-4 mt-6">
  <div class="card p-4">
    <h2 class="font-medium mb-1">Cepat</h2>
    <p class="text-gray-600">Proses unggah dan penayangan yang ringan agar tamu bisa langsung menemukan fotonya.</p>
  </div>
  <div class="card p-4">
    <h2 class="font-medium mb-1">Aman</h2>
    <p class="text-gray-600">Kontrol visibilitas dan tautan bertanda tangan untuk unduhan yang lebih aman.</p>
  </div>
  <div class="card p-4">
    <h2 class="font-medium mb-1">Mudah</h2>
    <p class="text-gray-600">Antarmuka sederhana; bagikan via QR atau token.</p>
  </div>
  
</div>
@endsection

