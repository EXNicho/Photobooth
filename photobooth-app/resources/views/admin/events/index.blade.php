@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-3">
  <h1 class="text-2xl font-semibold">Admin • Event QR</h1>
  <a href="{{ route('admin.photos.index') }}" class="btn-outline">Kembali</a>
</div>

@if(session('status'))
  <div class="alert-success mb-4">{{ session('status') }}</div>
@endif

<div class="card p-6">
  <h2 class="font-medium mb-3">Buat QR untuk Event</h2>
  <form method="post" action="{{ route('admin.events.qr') }}" class="flex flex-col sm:flex-row gap-3 items-start">
    @csrf
    <input type="text" name="event" value="{{ old('event') }}" required class="input flex-1 min-w-60" placeholder="Masukkan Event ID (misal: wedding-rian-dita-2025-01-12)">
    <div class="flex items-center gap-2">
      <button type="submit" class="btn-primary">Generate QR</button>
      <button type="submit" name="download" value="1" class="btn-muted">Generate & Unduh</button>
    </div>
  </form>
</div>

<div class="mt-6 card p-6">
  <h2 class="font-medium mb-3">Daftar Event</h2>
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>Event ID</th>
          <th>Total Foto</th>
          <th>QR</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($events as $e)
          <tr class="border-t border-gray-200">
            <td><code>{{ $e->event_id }}</code></td>
            <td>{{ $e->total }}</td>
            <td>
              @if($e->has_qr ?? false)
                <img src="{{ $e->qr_url }}" alt="QR {{ $e->event_id }}" class="w-20 h-20 object-contain bg-white rounded-md p-1 border border-gray-200">
              @else
                <span class="text-sm text-gray-500">Belum ada</span>
              @endif
            </td>
            <td class="whitespace-nowrap space-x-2">
              <a class="btn-muted" href="{{ route('gallery', ['event' => $e->event_id]) }}" target="_blank">Lihat</a>
              @if($e->has_qr ?? false)
                <a class="btn-success" href="{{ $e->qr_url }}" download>Unduh</a>
              @else
                <form class="inline" method="post" action="{{ route('admin.events.qr') }}">@csrf<input type="hidden" name="event" value="{{ $e->event_id }}"><button class="btn-info" type="submit">Generate</button></form>
                <form class="inline" method="post" action="{{ route('admin.events.qr') }}">@csrf<input type="hidden" name="event" value="{{ $e->event_id }}"><input type="hidden" name="download" value="1"><button class="btn-success" type="submit">Generate & Unduh</button></form>
              @endif
              <form class="inline" method="post" action="{{ route('admin.events.delete') }}" onsubmit="return confirm('Hapus event ini dan semua foto di dalamnya? Tindakan ini tidak dapat dibatalkan.');">@csrf<input type="hidden" name="event" value="{{ $e->event_id }}"><button class="btn-danger" type="submit">Delete</button></form>
            </td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-center text-gray-500 py-6">Belum ada event.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
