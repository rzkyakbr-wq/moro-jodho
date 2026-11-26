<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toko Moro Jodho - Pilih Halaman</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('assets/logo-icon.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --accent: #c6a052;
      --bg: #121212;
      --card: #1a1a1a;
      --border: #2a2a2a;
      --muted: #a0a0a0;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
    body {
      background: var(--bg);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 30px 20px;
    }

    .logo-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
      margin-bottom: 40px;
    }
    .logo-wrapper img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--accent);
      box-shadow: 0 0 24px rgba(198,160,82,0.4);
    }
    .logo-wrapper h1 {
      font-size: 2rem;
      font-weight: 800;
      color: var(--accent);
      letter-spacing: 2px;
      text-transform: uppercase;
      text-align: center;
    }
    .logo-wrapper p {
      font-size: 0.9rem;
      color: var(--muted);
      text-align: center;
    }

    .cards {
      display: flex;
      gap: 24px;
      flex-wrap: wrap;
      justify-content: center;
      width: 100%;
      max-width: 760px;
    }

    .link-card {
      flex: 1;
      min-width: 280px;
      max-width: 340px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 18px;
      padding: 36px 28px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 16px;
      text-decoration: none;
      color: #f0f0f0;
      transition: transform 0.25s, border-color 0.25s, box-shadow 0.25s;
    }
    .link-card:hover {
      transform: translateY(-6px);
      border-color: var(--accent);
      box-shadow: 0 12px 32px rgba(198,160,82,0.25);
    }

    .link-card .icon-circle {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
    }
    .icon-customer { background: rgba(198,160,82,0.15); color: var(--accent); }
    .icon-owner    { background: rgba(46,204,113,0.12); color: #2ecc71; }

    .link-card h2 {
      font-size: 1.25rem;
      font-weight: 800;
      text-align: center;
    }
    .link-card p {
      font-size: 0.85rem;
      color: var(--muted);
      text-align: center;
      line-height: 1.6;
    }
    .link-card .btn-go {
      margin-top: 8px;
      padding: 10px 28px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.95rem;
      border: none;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: background 0.2s;
    }
    .btn-gold  { background: var(--accent); color: #000; }
    .btn-gold:hover  { background: #e5b95f; }
    .btn-green { background: #2ecc71; color: #000; }
    .btn-green:hover { background: #27b861; }

    footer {
      margin-top: 48px;
      font-size: 0.75rem;
      color: var(--muted);
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="logo-wrapper">
    <img src="{{ asset('assets/logo-icon.jpg') }}" alt="Logo Moro Jodho">
    <h1>Moro Jodho</h1>
    <p>Toko Tembakau &amp; Perlengkapan Meracik — Semarang</p>
  </div>

  <div class="cards">

    <!-- LINK 1: PELANGGAN & KASIR -->
    <a href="{{ route('pos.index') }}" class="link-card">
      <div class="icon-circle icon-customer">
        <i class="fa-solid fa-store"></i>
      </div>
      <h2>Katalog &amp; Kasir</h2>
      <p>
        Lihat produk tembakau kami, pesan melalui WhatsApp,
        atau masuk sebagai kasir untuk mencatat penjualan.
      </p>
      <span class="btn-go btn-gold"><i class="fa-solid fa-arrow-right"></i> Buka Halaman</span>
    </a>

    <!-- LINK 2: PEMILIK (OWNER) -->
    <a href="{{ route('owner.index') }}" class="link-card">
      <div class="icon-circle icon-owner">
        <i class="fa-solid fa-chart-line"></i>
      </div>
      <h2>Halaman Pemilik</h2>
      <p>
        Kelola produk, pantau laporan penjualan, kulakan barang,
        dan unduh laporan keuangan toko.
      </p>
      <span class="btn-go btn-green"><i class="fa-solid fa-arrow-right"></i> Buka Halaman</span>
    </a>

  </div>

  <footer>
    &copy; {{ date('Y') }} Toko Moro Jodho &mdash; Indonesian Tobacco
  </footer>

</body>
</html>
