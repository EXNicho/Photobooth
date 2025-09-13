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
            const sun = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Zm0 4a1 1 0 0 1-1-1v-1a1 1 0 1 1 2 0v1a1 1 0 0 1-1 1Zm0-20a1 1 0 0 1 1 1v1a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1Zm10 10a1 1 0 0 1-1 1h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 1 1ZM4 12a1 1 0 0 1-1 1H2a1 1 0 1 1 0-2h1a1 1 0 0 1 1 1Zm13.657 6.243a1 1 0 0 1 0-1.414l.707-.707a1 1 0 1 1 1.414 1.414l-.707.707a1 1 0 0 1-1.414 0ZM4.222 5.636A1 1 0 0 1 5.636 4.22l.707.707A1 1 0 0 1 4.93 6.343l-.707-.707Zm14.142 0-.707.707A1 1 0 0 1 16.95 4.93l.707-.707a1 1 0 0 1 1.414 1.414ZM6.343 19.071l-.707.707A1 1 0 1 1 4.222 18.364l.707-.707a1 1 0 0 1 1.414 1.414Z"/></svg>';
            const moon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>';
            btn.innerHTML = isDark ? moon : sun;
            btn.setAttribute('aria-pressed', String(isDark));
            btn.setAttribute('aria-label', 'Ubah tema ke ' + (isDark ? 'Terang' : 'Gelap'));
            btn.title = isDark ? 'Mode Gelap' : 'Mode Terang';
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

          // Mobile nav toggle
          function toggleMobileNav(){
            const btn = document.getElementById('mobile-nav-btn');
            const panel = document.getElementById('mobile-nav');
            if (!btn || !panel) return;
            const isOpen = panel.classList.toggle('hidden');
            btn.setAttribute('aria-expanded', String(!isOpen));
            btn.setAttribute('aria-label', !isOpen ? 'Tutup menu' : 'Buka menu');
          }
          window.toggleMobileNav = toggleMobileNav;
          document.addEventListener('DOMContentLoaded', function(){
            const btn = document.getElementById('mobile-nav-btn');
            btn && btn.addEventListener('click', function(e){ e.stopPropagation(); toggleMobileNav(); });
            document.addEventListener('click', function(e){
              const panel = document.getElementById('mobile-nav');
              if (!panel || panel.classList.contains('hidden')) return;
              const within = panel.contains(e.target);
              const btnEl = document.getElementById('mobile-nav-btn');
              if (!within && btnEl && !btnEl.contains(e.target)) {
                panel.classList.add('hidden');
                btnEl.setAttribute('aria-expanded','false');
                btnEl.setAttribute('aria-label','Buka menu');
              }
            });
          });
        } catch(e) {}
      })();
    </script>
</head>
<body class="antialiased">
  <header class="header">
      <div class="flex items-center gap-3">
          <button id="mobile-nav-btn" type="button" class="icon-btn md:hidden" aria-label="Buka menu" aria-controls="mobile-nav" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M3.75 6.75A.75.75 0 0 1 4.5 6h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Zm0 5.25a.75.75 0 0 1 .75-.75h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Zm0 5.25a.75.75 0 0 1 .75-.75h15a.75.75 0 0 1 0 1.5h-15a.75.75 0 0 1-.75-.75Z"/></svg>
          </button>
          <strong class="text-lg">Photobooth</strong>
          @if(!empty($agentsPoliciesActive))
              <span class="badge badge-default"><span class="badge-dot"></span> Policies Active</span>
          @endif
      </div>
      <nav class="nav hidden md:flex absolute left-1/2 -translate-x-1/2">
          <a class="nav-link" href="{{ route('home') }}">Home</a>
          <a class="nav-link" href="{{ route('gallery') }}">Cari Foto</a>
      </nav>
      <div class="flex items-center gap-4">
          <button id="theme-btn" type="button" class="icon-btn" onclick="toggleTheme()" aria-label="Ubah tema" aria-pressed="false"></button>
          @auth
              @if(auth()->user()->is_admin)
                <a class="icon-btn" href="{{ route('admin.events') }}" aria-label="Event QR" title="Event QR">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                    <path d="M3 3h8v8H3V3Zm2 2v4h4V5H5Zm11-2h5v5h-5V3Zm2 2v1h1V5h-1ZM3 13h5v5H3v-5Zm2 2v1h1v-1H5Zm7-2h2v2h-2v-2Zm0 3h2v2h-2v-2Zm3-3h2v2h-2v-2Zm3 0h2v2h-2v-2Zm-6 6h2v2h-2v-2Zm3-3h2v2h-2v-2Zm3 3h2v2h-2v-2Zm-3 3h2v2h-2v-2Z"/>
                  </svg>
                </a>
                <a class="nav-link" href="{{ route('admin.photos.index') }}">Admin</a>
              @endif
              <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
          @else
              <a class="icon-btn" href="{{ route('login') }}" aria-label="Login" title="Login">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                  <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm-7 9a7 7 0 1 1 14 0H5Z" />
                </svg>
              </a>
          @endauth
      </div>
  </header>
  <!-- Mobile nav -->
  <div id="mobile-nav" class="md:hidden hidden border-b border-gray-200 bg-white dark:bg-gray-900 px-5 py-3">
      <nav class="flex flex-col items-start gap-3">
          <a class="nav-link" href="{{ route('home') }}">Home</a>
          <a class="nav-link" href="{{ route('gallery') }}">Cari Foto</a>
          @auth
              @if(auth()->user()->is_admin)
                <a class="nav-link" href="{{ route('admin.photos.index') }}">Admin</a>
                <a class="nav-link" href="{{ route('admin.events') }}">Event QR</a>
              @endif
              <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">Logout</a>
              <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
          @else
              <a class="nav-link" href="{{ route('login') }}">Login</a>
          @endauth
      </nav>
  </div>
  <main class="container-app py-6">
      @include('components.flash')
      @yield('content')
  </main>
    <footer class="footer">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 w-full">
        <div class="flex items-center gap-2">
          <strong>Photobooth</strong>
          <span class="text-sm">&copy; {{ date('Y') }}</span>
        </div>
        <div class="flex items-center gap-4">
          <a class="nav-link" href="{{ route('about') }}">Tentang</a>
          <a class="nav-link" href="{{ route('contact') }}">Kontak</a>
          <a class="nav-link" href="{{ route('gallery') }}">Galeri</a>
        </div>
        <div class="flex items-center gap-2">
          <a class="icon-ghost" href="https://instagram.com/yourbrand" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3.5A5.5 5.5 0 1 1 6.5 13 5.5 5.5 0 0 1 12 7.5Zm0 2A3.5 3.5 0 1 0 15.5 13 3.5 3.5 0 0 0 12 9.5ZM18 6.75a1.25 1.25 0 1 1-1.25 1.25A1.25 1.25 0 0 1 18 6.75Z"/></svg>
          </a>
          <a class="icon-ghost" href="https://wa.me/6281234567890" target="_blank" rel="noopener" aria-label="WhatsApp" title="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M20.52 3.48A11.93 11.93 0 0 0 12.06 0C5.55 0 .34 5.21.34 11.73a11.64 11.64 0 0 0 1.54 5.85L0 24l6.6-1.86a11.9 11.9 0 0 0 5.43 1.38h.01c6.51 0 11.73-5.21 11.73-11.73 0-3.13-1.22-6.07-3.25-8.21ZM12.03 21.2h-.01a9.5 9.5 0 0 1-4.84-1.33l-.35-.2-3.92 1.11 1.05-4.02-.21-.37a9.51 9.51 0 0 1-1.44-5.05c0-5.25 4.27-9.52 9.52-9.52 2.55 0 4.95.99 6.76 2.79a9.47 9.47 0 0 1 2.79 6.76c0 5.25-4.27 9.53-9.52 9.53Zm5.49-7.13c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15s-.77.97-.95 1.17-.35.22-.65.07c-.3-.15-1.27-.47-2.43-1.5-.9-.8-1.51-1.78-1.69-2.08-.18-.3-.02-.46.13-.61.14-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.15.2 2.11 3.22 5.12 4.52.72.31 1.28.5 1.72.64.72.23 1.37.2 1.88.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.18-1.42-.07-.12-.27-.2-.57-.35Z"/></svg>
          </a>
          <a class="icon-ghost" href="mailto:halo@photobooth.local" aria-label="Email" title="Email">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M2 5.75A2.75 2.75 0 0 1 4.75 3h14.5A2.75 2.75 0 0 1 22 5.75v12.5A2.75 2.75 0 0 1 19.25 21H4.75A2.75 2.75 0 0 1 2 18.25V5.75Zm2.75-.25a.75.75 0 0 0-.75.75v.3l8 4.9 8-4.9v-.3a.75.75 0 0 0-.75-.75H4.75Zm16 3.39-7.55 4.62a1.75 1.75 0 0 1-1.8 0L3.86 8.89v9.36c0 .41.34.75.75.75h14.5c.41 0 .75-.34.75-.75V8.89Z"/></svg>
          </a>
        </div>
      </div>
  </footer>
</body>
</html>


