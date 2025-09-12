<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Photobooth</title>
    <link rel="stylesheet" href="https://unpkg.com/modern-css-reset/dist/reset.min.css">
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, "Helvetica Neue", Arial; color:#222; }
        header, footer { display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-bottom:1px solid #eee; }
        header .left { display:flex; align-items:center; gap:12px; }
        header nav a { margin:0 8px; text-decoration:none; color:#333; }
        header nav a:hover { color:#111; }
        .container { max-width:1100px; margin:0 auto; padding:20px; }
        .hero { display:flex; align-items:center; justify-content:space-between; gap:20px; padding:40px 0; }
        .btn { display:inline-block; padding:10px 14px; border-radius:8px; background:#111; color:#fff; text-decoration:none; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px,1fr)); gap:12px; }
        .card { border-radius:10px; overflow:hidden; background:#fafafa; border:1px solid #eee; transition:transform .15s ease, box-shadow .15s ease; }
        .card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.08); }
        .card img { width:100%; display:block; aspect-ratio:4/3; object-fit:cover; }
        footer { margin-top:30px; border-top:1px solid #eee; color:#666; }
        .pill { display:inline-flex; align-items:center; gap:6px; border:1px solid #ddd; color:#333; padding:4px 8px; border-radius:999px; font-size:12px; }
        .dot { width:8px; height:8px; border-radius:50%; background:#0a0; }
    </style>
    @vite(['resources/js/app.js'])
</head>
<body>
<header>
    <div class="left">
        <strong>📸 Photobooth</strong>
        @if(!empty($agentsPoliciesActive))
            <span class="pill" title="AGENTS.md policies active"><span class="dot"></span> Policies Active</span>
        @endif
    </div>
    <nav>
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('gallery') }}">Cari Foto</a>
        @auth
            <a href="{{ route('admin.photos.index') }}">Admin</a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        @else
            <a href="{{ route('login') }}">Login</a>
        @endauth
    </nav>
  </header>
  <div class="container">
      @yield('content')
  </div>
  <footer>
      <div>© {{ date('Y') }} Photobooth. Semua hak cipta.</div>
      <div><a href="#">Kebijakan</a> · <a href="#">Kontak</a></div>
  </footer>
</body>
</html>

