@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-3">Kontak</h1>
<p class="text-gray-700 mb-4">Ada pertanyaan atau permintaan khusus? Kirim pesan Anda melalui formulir di bawah.</p>

<form method="post" action="{{ route('contact.send') }}" class="max-w-xl space-y-3">
  @csrf
  <div>
    <label class="block mb-1 text-sm text-gray-700" for="name">Nama</label>
    <input id="name" name="name" type="text" value="{{ old('name') }}" required class="input w-full">
  </div>
  <div>
    <label class="block mb-1 text-sm text-gray-700" for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input w-full">
  </div>
  <div>
    <label class="block mb-1 text-sm text-gray-700" for="message">Pesan</label>
    <textarea id="message" name="message" rows="5" required class="input w-full">{{ old('message') }}</textarea>
  </div>
  <div>
    <button class="btn" type="submit">Kirim</button>
  </div>
</form>
@endsection

