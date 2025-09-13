@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
  <h1 class="section-title">Daftar</h1>
  <div class="card p-6">
    @if($errors->any())
      <div class="alert-error mb-4">
        <strong>Gagal mendaftar.</strong> Periksa data Anda.
      </div>
    @endif

    <form action="{{ route('register.perform') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label for="name" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Nama</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required class="input w-full">
      </div>
      <div>
        <label for="email" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input w-full">
      </div>
      <div>
        <label for="password" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Kata Sandi</label>
        <input id="password" name="password" type="password" required class="input w-full">
      </div>
      <div>
        <label for="password_confirmation" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Konfirmasi Kata Sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required class="input w-full">
      </div>
      <div class="pt-2 flex items-center gap-3">
        <button type="submit" class="btn-primary">Buat Akun</button>
        <a href="{{ route('login') }}" class="btn-outline">Sudah punya akun?</a>
      </div>
    </form>
  </div>
</div>
@endsection

