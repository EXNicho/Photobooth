@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
  <h1 class="section-title">Masuk</h1>
  <div class="card p-6">
    @if($errors->any())
      <div class="alert-error mb-4">
        <strong>Gagal masuk.</strong> Periksa kembali email dan kata sandi.
      </div>
    @endif

    <form action="{{ route('login.perform') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label for="email" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input w-full">
      </div>
      <div>
        <label for="password" class="block mb-1 text-sm text-gray-700 dark:text-gray-300">Kata Sandi</label>
        <input id="password" name="password" type="password" required class="input w-full">
      </div>
      <div class="flex items-center justify-between">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
          <input type="checkbox" name="remember" class="rounded border-gray-300"> Ingat saya
        </label>
        <a href="{{ route('password.request') }}" class="btn-ghost text-sm">Lupa kata sandi?</a>
      </div>
      <div class="pt-2 flex items-center gap-3">
        <button type="submit" class="btn-primary">Masuk</button>
        <a href="{{ route('home') }}" class="btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
