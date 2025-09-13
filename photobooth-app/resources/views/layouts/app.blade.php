<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Photobooth</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
      (function(){
        try {
          const saved = localStorage.getItem('theme');
          const root = document.documentElement;
          if (saved === 'dark') root.classList.add('dark');

          function updateThemeLabel() {
            const btn = document.getElementById('theme-btn');
            if (!btn) return;
            const isDark = root.classList.contains('dark');
            btn.textContent = 'Tema: ' + (isDark ? 'Gelap' : 'Terang');
            btn.setAttribute('aria-pressed', String(isDark));
            btn.setAttribute('aria-label', 'Ubah tema ke ' + (isDark ? 'Terang' : 'Gelap'));
          }

          window.toggleTheme = function(){
            const isDark = root.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeLabel();
          }

          // Expose updater in case other scripts need it
          window.updateThemeLabel = updateThemeLabel;
          // Initialize on load
          document.addEventListener('DOMContentLoaded', updateThemeLabel);
        } catch(e) {}
      })();
    </script>
</head>
<body class="antialiased">
  <header class="header">
      <div class="flex items-center gap-3">
          <strong class="text-lg">Photobooth</strong>
          @if(!empty($agentsPoliciesActive))
              <span class="badge badge-default"><span class="badge-dot"></span> Policies Active</span>
          @endif
      </div>
      <nav class="nav">
          <a class="nav-link" href="{{ route('home') }}">Home</a>
          <a class="nav-link" href="{{ route('gallery') }}">Cari Foto</a>
          @auth
              <a class="nav-link" href="{{ route('admin.photos.index') }}">Admin</a>
              <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
          @else
              <a class="nav-link" href="{{ route('login') }}">Login</a>
              <a class="nav-link" href="{{ route('register') }}">Daftar</a>
          @endauth
          <button id="theme-btn" type="button" class="btn-muted" onclick="toggleTheme()" aria-label="Ubah tema" aria-pressed="false">Tema</button>
      </nav>
  </header>
  <main class="container-app py-6">
      @include('components.flash')
      @yield('content')
  </main>
  <footer class="footer">
      <div>&copy; {{ date('Y') }} Photobooth. Semua hak cipta.</div>
      <div class="flex items-center gap-3"><a class="nav-link" href="{{ route('about') }}">Tentang</a> <span aria-hidden="true">•</span> <a class="nav-link" href="{{ route('contact') }}">Kontak</a></div>
  </footer>
</body>
</html>
