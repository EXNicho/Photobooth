@if (session('status'))
  <div class="alert-success mb-3 relative pr-10">
    {{ session('status') }}
    <button type="button" class="absolute top-2 right-2 text-gray-700 dark:text-gray-300"
            aria-label="Tutup" onclick="this.closest('[class*=alert-]').remove()">×</button>
  </div>
@endif

@if ($errors->any())
  <div class="alert-error mb-3 relative pr-10">
    <strong>Terjadi kesalahan:</strong>
    <ul class="list-disc ml-5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="absolute top-2 right-2 text-danger-700"
            aria-label="Tutup" onclick="this.closest('[class*=alert-]').remove()">×</button>
  </div>
@endif

