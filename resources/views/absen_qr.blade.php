<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Absensi QR - Toko Moro Jodho</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('assets/logo-icon.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root { --accent: #c6a052; --bg: #121212; --card: #1a1a1a; --border: #2a2a2a; --text: #f0f0f0; --muted: #a0a0a0; --success: #2ecc71; --danger: #e74c3c; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
    body { background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 24px; width: 100%; max-width: 440px; }
    h2 { color: var(--accent); margin-bottom: 12px; font-size: 1.25rem; }
    input { background: #222; border: 1px solid var(--border); color: #fff; padding: 10px; border-radius: 8px; width: 100%; margin-bottom: 12px; }
    label { font-size: 0.84rem; color: var(--muted); display: block; margin-bottom: 4px; }
    .btn { background: var(--accent); color: #000; border: none; padding: 12px; border-radius: 8px; font-weight: 700; width: 100%; cursor: pointer; }
  </style>
</head>
<body>
  <div class="card">
    <h2><i class="fa-solid fa-qrcode"></i> Absensi {{ $tipe === 'pulang' ? 'Pulang' : 'Masuk' }} - Moro Jodho</h2>
    @if (!$valid)
      <p style="color: var(--danger);">QR Code ini tidak berlaku atau sudah diperbarui oleh Pemilik Toko.</p>
    @else
      <form onsubmit="submitQr(event)">
        <label>Nama Kasir</label>
        <input type="text" id="qr-karyawan" value="{{ $currentUser['username'] ?? '' }}" placeholder="Masukkan nama Anda..." required>
        @if (($currentUser['role'] ?? '') !== 'kasir')
          <label>Kode Masuk Kasir</label>
          <input type="password" id="qr-code" placeholder="Masukkan kode kasir..." required>
        @endif
        <p id="qr-loc-status" style="font-size:0.82rem; color:var(--muted); margin-bottom:12px;">Mengambil titik lokasi Anda...</p>
        <button type="submit" class="btn">Catat Absen {{ $tipe === 'pulang' ? 'Pulang' : 'Masuk' }}</button>
      </form>
    @endif
  </div>
  <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let coords = { latitude: null, longitude: null, akurasi: null };
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(p => {
        coords.latitude = Number(p.coords.latitude.toFixed(7));
        coords.longitude = Number(p.coords.longitude.toFixed(7));
        coords.akurasi = Math.round(p.coords.accuracy);
        document.getElementById('qr-loc-status').innerHTML = `<span style="color:var(--success)">Lokasi terkunci (Akurasi ±${coords.akurasi} m)</span>`;
      }, () => {
        document.getElementById('qr-loc-status').innerText = 'Lokasi tidak terdeteksi.';
      }, { enableHighAccuracy: true, timeout: 10000 });
    }
    async function submitQr(e) {
      e.preventDefault();
      const res = await fetch('/pos-api/absen-qr', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
          tipe: @json($tipe),
          token: @json($token),
          karyawan: document.getElementById('qr-karyawan').value,
          kasir_code: document.getElementById('qr-code')?.value || null,
          ...coords
        })
      });
      const data = await res.json();
      alert(data.message);
    }
  </script>
</body>
</html>
