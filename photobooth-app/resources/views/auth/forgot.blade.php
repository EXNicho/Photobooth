@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
  <h1 class="section-title">Lupa Kata Sandi</h1>
  <div class="card p-6">
    @if(session('status'))
      <div class="alert-success mb-4">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div class="alert-error mb-4">Terjadi kesalahan. Coba lagi.</div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label for="email" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input w-full">
        <p class="text-xs text-gray-500 mt-1">Kami akan mengirimkan tautan reset jika email terdaftar.</p>
      </div>
      <div class="pt-2 flex items-center gap-3">
        <button type="submit" class="btn-primary">Kirim Tautan Reset</button>
        <a href="{{ route('login') }}" class="btn-outline">Kembali ke Login</a>
      </div>
    </form>
  </div>
</div>
@endsection

