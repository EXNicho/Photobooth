@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-3">Admin • Photos</h1>
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
      <td>{{ $p->status }}</td>
      <td>{{ optional($p->uploaded_at)->format('Y-m-d H:i') }}</td>
      <td class="space-x-2 whitespace-nowrap">
        <form class="inline" method="post" action="{{ route('admin.photos.retry', $p) }}">@csrf<button class="btn-primary" type="submit">Retry</button></form>
        <form class="inline" method="post" action="{{ route('admin.photos.regenerate', $p) }}">@csrf<button class="btn-secondary" type="submit">Regen</button></form>
        <a class="btn-muted" href="{{ route('photos.token', $p->qr_token) }}" target="_blank">Lihat</a>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
</div>
<div class="my-4 pagination">{{ $photos->links() }}</div>
@endsection
