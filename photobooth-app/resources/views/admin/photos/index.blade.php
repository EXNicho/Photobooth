@extends('layouts.app')

@section('content')
@if(!($storageLinked ?? false))
  <div class="alert-error mb-4">
    Storage link belum aktif. Jalankan <code>php artisan storage:link</code> agar gambar/thumbnail bisa tampil di web.
  </div>
@endif
<div class="flex items-center justify-between mb-3">
  <h1 class="text-2xl font-semibold">Admin • Photos</h1>
  <div class="flex items-center gap-2">
    <a class="btn-primary" href="{{ route('admin.photos.create') }}">Tambah Foto</a>
  </div>
</div>

<div class="overflow-x-auto">
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Token</th>
        <th>Status</th>
        <th>Uploaded</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    @foreach($photos as $p)
      <tr class="border-t border-gray-200">
        <td><code>{{ $p->id }}</code></td>
        <td><code>{{ $p->qr_token }}</code></td>
      <td>
        @php
          $map = [
            'ready' => 'badge-success',
            'pending' => 'badge-warning',
            'failed' => 'badge-danger',
            'rejected' => 'badge-danger',
          ];
          $cls = $map[$p->status] ?? 'badge-default';
        @endphp
        <span class="badge {{ $cls }}">{{ ucfirst($p->status) }}</span>
      </td>
        <td>{{ optional($p->uploaded_at)->format('Y-m-d H:i') }}</td>
        <td class="space-x-2 whitespace-nowrap">
          <form class="inline" method="post" action="{{ route('admin.photos.retry', $p) }}">@csrf<button class="btn-warning" type="submit">Retry</button></form>
          <form class="inline" method="post" action="{{ route('admin.photos.regenerate', $p) }}">@csrf<button class="btn-info" type="submit">Regen</button></form>
          @if($p->status !== 'ready')
            <form class="inline" method="post" action="{{ route('admin.photos.approve', $p) }}">@csrf<button class="btn-success" type="submit">Approve</button></form>
          @endif
          @if($p->status !== 'rejected')
            <form class="inline" method="post" action="{{ route('admin.photos.reject', $p) }}">@csrf<button class="btn-danger" type="submit">Reject</button></form>
          @endif
          <form class="inline" method="post" action="{{ route('admin.photos.destroy', $p) }}" onsubmit="return confirm('Hapus foto ini?');">@csrf @method('DELETE')<button class="btn-danger" type="submit">Delete</button></form>
          <a class="btn-muted" href="{{ route('photos.token', $p->qr_token) }}" target="_blank">Lihat</a>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
<div class="my-4 pagination">{{ $photos->links() }}</div>
@endsection
