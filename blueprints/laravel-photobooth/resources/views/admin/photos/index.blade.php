@extends('layouts.app')

@section('content')
<h1>Admin · Photos</h1>
<table style="width:100%; border-collapse:collapse;">
  <thead>
    <tr><th align="left">ID</th><th align="left">Token</th><th>Status</th><th>Uploaded</th><th>Aksi</th></tr>
  </thead>
  <tbody>
  @foreach($photos as $p)
    <tr style="border-top:1px solid #eee;">
      <td><code>{{ $p->id }}</code></td>
      <td><code>{{ $p->qr_token }}</code></td>
      <td>{{ $p->status }}</td>
      <td>{{ optional($p->uploaded_at)->format('Y-m-d H:i') }}</td>
      <td>
        <form method="post" action="{{ route('admin.photos.retry', $p) }}" style="display:inline;">@csrf<button class="btn" type="submit">Retry</button></form>
        <form method="post" action="{{ route('admin.photos.regenerate', $p) }}" style="display:inline;">@csrf<button class="btn" style="background:#555;" type="submit">Regen</button></form>
        <a class="btn" style="background:#777;" href="{{ route('photos.token', $p->qr_token) }}" target="_blank">Lihat</a>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
<div style="margin:16px 0;">{{ $photos->links() }}</div>
@endsection

