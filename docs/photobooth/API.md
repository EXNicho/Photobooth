Photobooth API – Ingest Foto

Autentikasi
- Header: Authorization: Bearer {SANCTUM_TOKEN}
- Rate limit: throttle:photobooth (konfig di RouteServiceProvider)

Endpoint
- POST /api/photos

Content-Type
- multipart/form-data

Field Form
- file: required, image/jpeg|png|heic|webp; max 25MB (konfigurable)
- original_name: optional string (nama file asli)
- mime: optional string (MIME terdeteksi klien)
- size: optional integer (bytes)
- checksum: optional string (sha256 hex); server akan hitung jika tidak ada
- captured_at: optional string ISO8601 (exif/datetime foto)
- event_id: optional string
- visibility: optional enum: public|private|unlisted (default: public)

Respon (201 Created – baru)
{
  "id": "01HQZ...ULID",
  "qr_token": "k32v7...",
  "filename": "01HQ...jpg",
  "storage_path": "photos/25/01/01HQ...jpg",
  "thumbnail_url": "/storage/photos/25/01/01HQ..._thumb.jpg",
  "qr_url": "/storage/qrcodes/k32v7....png",
  "status": "pending|ready",
  "uploaded_at": "2025-01-01T12:00:00Z"
}

Respon (200 OK – idempoten, sudah ada)
{ ...objek sama seperti di atas, flag: "existing": true }

Kode Error
- 400: Validasi gagal
- 401: Token tidak valid
- 415: MIME tidak diizinkan
- 429: Terlalu banyak permintaan (rate limit)
- 500: Kesalahan server/penyimpanan

Catatan
- Server menormalkan nama file (aman, tanpa karakter berbahaya)
- Jika checksum disediakan dan cocok, server tidak menggandakan entri
- Setelah insert, job ProcessPhoto akan membuat thumbnail + QR dan mem-broadcast event

