@extends('layouts.app')

@section('content')
@php($activeTab = ($tab ?? request('tab', 'login')) === 'register' ? 'register' : 'login')
<div class="max-w-lg mx-auto">
  <div class="card p-6">
    <div class="flex items-center gap-4 mb-6 border-b border-gray-200 dark:border-gray-800 pb-3">
      <a href="{{ route('login', ['tab' => 'login']) }}" class="btn-ghost {{ $activeTab==='login' ? 'font-semibold text-gray-900 dark:text-white underline underline-offset-4' : '' }}">Masuk</a>
      <a href="{{ route('login', ['tab' => 'register']) }}" class="btn-ghost {{ $activeTab==='register' ? 'font-semibold text-gray-900 dark:text-white underline underline-offset-4' : '' }}">Daftar</a>
    </div>

    @if($activeTab==='login')
      @if($errors->any())
        <div class="alert-error mb-4">
          <strong>Gagal masuk.</strong> Periksa kembali email dan kata sandi.
        </div>
      @endif
      <form action="{{ route('login.perform') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label for="login" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Email atau Username</label>
          <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus class="input w-full" autocomplete="username">
        </div>
        <div>
          <label for="password" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Kata Sandi</label>
          <input id="password" name="password" type="password" required class="input w-full" autocomplete="current-password">
        </div>
        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <input type="checkbox" name="remember" class="rounded border-gray-300"> Ingat saya
          </label>
          <a href="{{ route('password.request') }}" class="btn-ghost text-sm">Lupa kata sandi?</a>
        </div>
        <div class="pt-2 flex items-center gap-3">
          <button type="submit" class="btn-primary">Masuk</button>
          <a href="{{ route('login', ['tab' => 'register']) }}" class="btn-outline">Belum punya akun?</a>
        </div>
      </form>
    @else
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
          <label for="username" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Username (opsional)</label>
          <input id="username" name="username" type="text" value="{{ old('username') }}" class="input w-full" autocomplete="username">
        </div>
        <div>
          <label for="email" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input w-full" autocomplete="email">
        </div>
        <div>
          <label for="password" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Kata Sandi</label>
          <input id="password" name="password" type="password" required class="input w-full" autocomplete="new-password">
        </div>
        <div>
          <label for="password_confirmation" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Konfirmasi Kata Sandi</label>
          <input id="password_confirmation" name="password_confirmation" type="password" required class="input w-full" autocomplete="new-password">
        </div>
        <div class="pt-2 flex items-center gap-3">
          <button type="submit" class="btn-primary">Buat Akun</button>
          <a href="{{ route('login', ['tab' => 'login']) }}" class="btn-outline">Sudah punya akun?</a>
        </div>
      </form>
    @endif
  </div>
</div>
@endsection
