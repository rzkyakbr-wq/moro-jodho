<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Toko Tembakau Moro Jodho - Katalog &amp; Kasir</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('assets/logo-icon.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #1e1e1e;
      --primary-accent: #c6a052;
      --gold-hover: #e5b95f;
      --bg-dark: #121212;
      --card-bg: #1a1a1a;
      --card-border: #2a2a2a;
      --text-light: #f0f0f0;
      --text-muted: #a0a0a0;
      --success: #2ecc71;
      --danger: #e74c3c;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
    body { background-color: var(--bg-dark); color: var(--text-light); line-height: 1.6; }

    header {
      background: rgba(26, 26, 26, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--card-border);
      padding: 12px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .brand-logo-container { display: flex; align-items: center; gap: 14px; }
    .brand-logo-img {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--primary-accent);
    }
    .brand-title h1 {
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--primary-accent);
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .brand-title p { font-size: 0.72rem; color: var(--text-muted); letter-spacing: 0.5px; }

    nav { display: flex; gap: 10px; }
    nav button {
      background: #252525;
      color: var(--text-light);
      border: 1px solid var(--card-border);
      padding: 8px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    nav button:hover, nav button.active {
      background: var(--primary-accent);
      color: #000;
      border-color: var(--primary-accent);
      box-shadow: 0 4px 12px rgba(198, 160, 82, 0.3);
    }

    .container { padding: 25px; max-width: 1300px; margin: 0 auto; }

    .card {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    }
    .card h2 {
      font-size: 1.2rem;
      color: var(--primary-accent);
      border-bottom: 1px solid var(--card-border);
      padding-bottom: 12px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    .profile-hero-banner {
      background: linear-gradient(180deg, rgba(18, 18, 18, 0.25) 0%, rgba(18, 18, 18, 0.88) 100%),
                  url('{{ asset("assets/hero-bg.jpg") }}') center/cover no-repeat;
      border-radius: 16px;
      padding: 40px 30px;
      margin-bottom: 25px;
      border: 1px solid rgba(198, 160, 82, 0.4);
      box-shadow: 0 10px 30px rgba(0,0,0,0.6), 0 0 0 1px rgba(198,160,82,0.1);
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      min-height: 220px;
    }
    .profile-hero-banner h2 {
      font-size: 2.2rem;
      color: var(--primary-accent);
      font-weight: 800;
      margin-bottom: 8px;
      text-shadow: 0 2px 12px rgba(0,0,0,0.9), 0 0 30px rgba(198,160,82,0.4);
    }
    .profile-hero-banner p {
      color: #e8d9b8;
      font-size: 1rem;
      max-width: 750px;
      margin-bottom: 18px;
      text-shadow: 0 1px 4px rgba(0,0,0,0.8);
    }
    .profile-meta-tags { display: flex; gap: 15px; flex-wrap: wrap; }
    .meta-tag {
      background: rgba(0, 0, 0, 0.65);
      border: 1px solid rgba(198, 160, 82, 0.5);
      color: var(--primary-accent);
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 0.85rem;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s;
    }
    .meta-tag:hover { background: var(--primary-accent); color: #000; }

    .search-box { position: relative; margin-bottom: 16px; }
    .search-box i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
    }
    .search-box input { padding-left: 40px; margin-bottom: 0; }

    .filter-label {
      font-size: 0.78rem;
      color: var(--text-muted);
      margin-bottom: 6px;
      display: block;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .filter-bar {
      display: flex;
      gap: 10px;
      overflow-x: auto;
      padding-bottom: 8px;
      margin-bottom: 14px;
    }
    .filter-btn {
      background: #222;
      color: var(--text-muted);
      border: 1px solid var(--card-border);
      padding: 8px 16px;
      border-radius: 20px;
      cursor: pointer;
      font-size: 0.85rem;
      white-space: nowrap;
      transition: all 0.2s;
    }
    .filter-btn.active, .filter-btn:hover {
      background: var(--primary-accent);
      color: #000;
      font-weight: 700;
      border-color: var(--primary-accent);
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }
    @media (max-width: 960px) {
      .product-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
      .product-grid { grid-template-columns: 1fr; }
    }

    .product-card {
      background: #202020;
      border: 1px solid var(--card-border);
      border-radius: 12px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: transform 0.25s, box-shadow 0.25s;
    }
    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.5);
      border-color: var(--primary-accent);
    }
    .product-img-wrapper {
      width: 100%;
      height: 155px;
      background: #151515;
      overflow: hidden;
      position: relative;
    }
    .product-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
    .product-card:hover .product-img { transform: scale(1.06); }
    .unit-badge {
      position: absolute;
      top: 10px;
      right: 10px;
      background: rgba(0,0,0,0.8);
      color: var(--primary-accent);
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: bold;
      border: 1px solid var(--primary-accent);
    }
    .product-details {
      padding: 15px;
      display: flex;
      flex-direction: column;
      flex: 1;
      justify-content: space-between;
    }
    .product-title { font-size: 0.95rem; font-weight: 700; color: var(--text-light); margin-bottom: 4px; }
    .product-category { font-size: 0.75rem; color: var(--primary-accent); text-transform: uppercase; font-weight: 600; margin-bottom: 6px; }
    .product-price { font-size: 1rem; font-weight: 800; color: #fff; margin-bottom: 6px; }
    .product-subprice { font-size: 0.82rem; color: var(--text-muted); font-weight: 600; display: block; margin-top: 2px; }
    .product-stock { font-size: 0.8rem; margin-bottom: 10px; font-weight: 600; }
    .stock-ok { color: var(--success); }
    .stock-empty { color: var(--danger); }

    .btn-row { display: flex; gap: 6px; flex-wrap: wrap; }
    .btn-add-cart {
      background: var(--primary-accent);
      color: #000;
      border: none;
      padding: 9px 10px;
      border-radius: 8px;
      font-weight: 700;
      cursor: pointer;
      flex: 1;
      transition: background 0.2s;
      font-size: 0.82rem;
    }
    .btn-add-cart:hover { background: var(--gold-hover); }
    .btn-add-cart:disabled { background: #3a3a3a; color: #777; cursor: not-allowed; }
    .btn-add-half {
      background: #2b2b2b;
      color: var(--primary-accent);
      border: 1px solid var(--primary-accent);
      padding: 9px 10px;
      border-radius: 8px;
      font-weight: 700;
      cursor: pointer;
      flex: 1;
      font-size: 0.82rem;
    }
    .btn-add-half:hover { background: var(--primary-accent); color: #000; }
    .btn-add-half:disabled { border-color: #444; color: #666; cursor: not-allowed; }
    .btn-add-custom {
      background: #1f2d24;
      color: var(--success);
      border: 1px solid var(--success);
      padding: 8px 10px;
      border-radius: 8px;
      font-weight: 700;
      cursor: pointer;
      width: 100%;
      margin-top: 6px;
      font-size: 0.82rem;
    }
    .btn-add-custom:hover { background: var(--success); color: #000; }
    .btn-add-custom:disabled { border-color: #444; color: #666; background: #222; cursor: not-allowed; }

    /* PAGINATION */
    .pagination-bar {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      margin-top: 22px;
      flex-wrap: wrap;
    }
    .page-btn {
      background: #222;
      color: var(--text-light);
      border: 1px solid var(--card-border);
      padding: 7px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.85rem;
      font-weight: 600;
    }
    .page-btn.active, .page-btn:hover:not(:disabled) {
      background: var(--primary-accent);
      color: #000;
      border-color: var(--primary-accent);
    }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* PAYMENT METHOD TOGGLE */
    .pay-method-group {
      display: flex;
      gap: 10px;
      margin-bottom: 14px;
    }
    .pay-method-btn {
      flex: 1;
      background: #252525;
      color: var(--text-light);
      border: 1px solid var(--card-border);
      padding: 10px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 700;
      font-size: 0.88rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .pay-method-btn.active {
      background: var(--primary-accent);
      color: #000;
      border-color: var(--primary-accent);
    }

    /* ENCYCLOPEDIA GRID */
    .nusantara-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 16px;
    }
    .nusantara-item {
      background: #202020;
      border: 1px solid var(--card-border);
      border-left: 4px solid var(--primary-accent);
      border-radius: 10px;
      padding: 16px;
    }
    .nusantara-item h4 { color: var(--primary-accent); font-size: 1rem; margin-bottom: 4px; }
    .nusantara-item small { display: block; color: var(--text-muted); font-size: 0.78rem; margin-bottom: 8px; }
    .nusantara-item p { font-size: 0.86rem; color: var(--text-light); }

    .btn-wa-order {
      background: #25D366;
      color: #fff;
      border: none;
      padding: 14px;
      border-radius: 10px;
      font-size: 1.05rem;
      font-weight: 800;
      width: 100%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: background 0.2s;
    }
    .btn-wa-order:hover { background: #1eb956; }

    .pin-wrapper { position: relative; margin-bottom: 12px; }
    .pin-wrapper input { margin-bottom: 0; padding-right: 44px; }
    .pin-toggle-btn {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 1rem;
      padding: 4px;
    }
    .pin-toggle-btn:hover { color: var(--primary-accent); }

    .gps-box {
      background: #202020;
      border: 1px solid var(--card-border);
      border-radius: 8px;
      padding: 12px 14px;
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      font-size: 0.85rem;
    }
    .gps-map-preview {
      width: 100%;
      height: 200px;
      border: 1px solid var(--card-border);
      border-radius: 8px;
      margin-bottom: 12px;
    }

    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.78);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2000;
      padding: 20px;
    }
    .modal-box {
      background: var(--card-bg);
      border: 1px solid var(--card-border);
      border-radius: 14px;
      padding: 24px;
      width: 100%;
      max-width: 440px;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--card-border);
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .modal-header h3 { color: var(--primary-accent); font-size: 1.1rem; }
    .modal-close {
      background: #2a2a2a;
      color: #fff;
      border: none;
      width: 32px;
      height: 32px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 1rem;
    }

    .hidden { display: none !important; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table th, table td { border: 1px solid var(--card-border); padding: 12px; text-align: left; font-size: 0.88rem; }
    table th { background: #222; color: var(--primary-accent); font-weight: 700; }

    input, select, textarea {
      background: #222;
      border: 1px solid var(--card-border);
      color: #fff;
      padding: 10px;
      border-radius: 8px;
      width: 100%;
      margin-bottom: 12px;
    }

    .btn-sm {
      background: #2a2a2a;
      color: var(--text-light);
      border: 1px solid var(--card-border);
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.8rem;
    }
    .btn-sm:hover { background: var(--primary-accent); color: #000; }

    /* PRINT NOTA TEPAT 1 HALAMAN */
    #receipt-print { display: none; }
    @media print {
      @page { margin: 5mm; size: auto; }
      html, body {
        background: #fff !important;
        color: #000 !important;
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
      }
      header, .container, .modal-backdrop { display: none !important; }
      #receipt-print {
        display: block !important;
        position: static !important;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        padding: 8px;
        font-family: monospace;
        color: #000;
        background: #fff;
      }
    }
  </style>
</head>
<body>

  <header>
    <div class="brand-logo-container">
      <img src="{{ asset('assets/logo-icon.jpg') }}" alt="Logo Toko Moro Jodho" class="brand-logo-img">
      <div class="brand-title">
        <h1>Moro Jodho</h1>
        <p>Toko Tembakau &amp; Perlengkapan Meracik</p>
      </div>
    </div>
    <nav>
      <button onclick="switchRole('customer')" id="btn-customer" class="active"><i class="fa-solid fa-store"></i> Katalog &amp; Pesan</button>
      <button onclick="switchRole('kasir')" id="btn-kasir"><i class="fa-solid fa-cash-register"></i> Kasir</button>
      <button onclick="logout()" id="btn-logout" class="hidden" style="background: var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
    </nav>
  </header>

  <div class="container">

    <!-- FORM MASUK KASIR -->
    <div id="section-login" class="card hidden" style="max-width: 420px; margin: 40px auto;">
      <h2><i class="fa-solid fa-lock"></i> Masuk Halaman Kasir</h2>
      <form onsubmit="handleLogin(event)">
        <label style="font-size: 0.85rem; color: var(--text-muted);">Nama Kasir</label>
        <input type="text" id="login-username" placeholder="Masukkan nama Anda..." required>
        <label style="font-size: 0.85rem; color: var(--text-muted);">Kode Masuk Kasir (Dari Pemilik)</label>
        <div class="pin-wrapper">
          <input type="password" id="login-password" placeholder="Masukkan kode dari pemilik..." required>
          <button type="button" class="pin-toggle-btn" onclick="togglePin('login-password', this)" title="Lihat / Sembunyikan Kode">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <button type="submit" class="btn-add-cart" style="margin-top: 10px; width: 100%;">Masuk Sekarang</button>
      </form>
      <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 15px; text-align: center;">
        Saat berhasil masuk, kehadiran Anda otomatis tercatat.
      </p>
    </div>

    <!-- TAMPILAN PELANGGAN -->
    <div id="section-customer">

      <div class="profile-hero-banner">
        <h2>Toko Moro Jodho</h2>
        <p>Penyedia racikan tembakau mole pilihan, virginia premium, cengkeh rajang aromatik, papir manis, serta perlengkapan meracik lengkap di Semarang.</p>
        <div class="profile-meta-tags">
          <a href="https://maps.app.goo.gl/DZRpVj27LzvUehgQ6" target="_blank" class="meta-tag">
            <i class="fa-solid fa-location-dot"></i> Jl. Simongan, Semarang (Google Maps)
          </a>
          <a href="https://wa.me/6285728040343" target="_blank" class="meta-tag">
            <i class="fa-brands fa-whatsapp"></i> +62 857-2804-0343
          </a>
          <span class="meta-tag">
            <i class="fa-solid fa-clock"></i> Buka Setiap Hari: 09:00 - 21:00 WIB
          </span>
          <span class="meta-tag">
            <i class="fa-solid fa-qrcode"></i> Menerima Cash &amp; QRIS
          </span>
        </div>
      </div>

      <div style="display: flex; gap: 24px; flex-wrap: wrap;">

        <div style="flex: 2; min-width: 320px;">
          <div class="card">
            <h2><i class="fa-solid fa-boxes-stacked"></i> Katalog Produk Tembakau &amp; Perlengkapan</h2>

            <div class="search-box">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="customer-search" placeholder="Cari nama barang yang ingin dibeli..." oninput="setSearch('customer', this.value)">
            </div>

            <span class="filter-label">1. Pilih Jenis Barang</span>
            <div class="filter-bar" id="customer-cat-bar">
              <button class="filter-btn active" onclick="filterKategori('customer', 'all', this)">Semua Produk</button>
              <button class="filter-btn" onclick="filterKategori('customer', 'tembako olahan', this)">Tembako Olahan</button>
              <button class="filter-btn" onclick="filterKategori('customer', 'original', this)">Original</button>
              <button class="filter-btn" onclick="filterKategori('customer', 'racik', this)">Racik</button>
              <button class="filter-btn" onclick="filterKategori('customer', 'papir', this)">Papir</button>
              <button class="filter-btn" onclick="filterKategori('customer', 'cengkeh', this)">Cengkeh</button>
              <button class="filter-btn" onclick="filterKategori('customer', 'aksesoris', this)">Aksesoris</button>
              <button class="filter-btn" onclick="filterKategori('customer', 'alat', this)">Alat</button>
            </div>

            <span class="filter-label">2. Pilih Takaran Ons (Khusus Tembakau)</span>
            <div class="filter-bar" id="customer-ons-bar">
              <button class="filter-btn active" onclick="filterOns('customer', 'all', this)">Semua Takaran</button>
              <button class="filter-btn" onclick="filterOns('customer', '1_ons', this)">1 Ons (100 gram)</button>
              <button class="filter-btn" onclick="filterOns('customer', 'setengah_ons', this)">1/2 Ons (50 gram)</button>
            </div>

            <div class="product-grid" id="customer-catalog-grid"></div>
            <div class="pagination-bar" id="customer-pagination"></div>
          </div>
        </div>

        <div style="flex: 1; min-width: 280px;">
          <div class="card" style="position: sticky; top: 85px;">
            <h2><i class="fa-solid fa-cart-shopping"></i> Keranjang Pesanan</h2>
            <div id="wa-cart-list" style="margin-bottom: 15px;"></div>
            <hr style="border: none; border-top: 1px dashed var(--card-border); margin: 12px 0;">
            <p style="font-weight: bold; font-size: 1.1rem; display: flex; justify-content: space-between; margin-bottom: 15px;">
              Total Perkiraan: <span id="wa-cart-total" style="color: var(--primary-accent);">Rp 0</span>
            </p>
            <button class="btn-wa-order" onclick="kirimPesananWA()">
              <i class="fa-brands fa-whatsapp"></i> Pesan via WhatsApp
            </button>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 10px; text-align: center;">
              Pembayaran di toko mendukung <b>Tunai (Cash)</b> dan <b>QRIS</b>.
            </p>
          </div>
        </div>

      </div>

      <!-- ENSIKLOPEDIA TEMBAKAU NUSANTARA (SABANG - MERAUKE) -->
      <div class="card">
        <h2><i class="fa-solid fa-leaf"></i> Mengenal Jenis Tembakau Nusantara (Sabang sampai Merauke)</h2>
        <div class="nusantara-grid">
          <div class="nusantara-item">
            <h4>1. Tembakau Gayo (Aceh - Sabang)</h4>
            <small>Asal: Dataran Tinggi Gayo, Aceh</small>
            <p>Memiliki aroma tajam dengan karakter rempah pegunungan, rasa kuat (bold), dan mantap di tenggorokan.</p>
          </div>
          <div class="nusantara-item">
            <h4>2. Tembakau Deli (Sumatra Utara)</h4>
            <small>Asal: Deli Serdang, Sumatra Utara</small>
            <p>Daun tipis, elastis, dan berwarna cokelat cerah. Terkenal di seluruh dunia sebagai daun pembalut cerutu premium.</p>
          </div>
          <div class="nusantara-item">
            <h4>3. Tembakau Mole (Jawa Barat)</h4>
            <small>Asal: Sumedang &amp; Garut, Jawa Barat</small>
            <p>Tekstur lembut dengan olahan rasa manis legit, aroma madu atau karamel yang harum, dan hisapan ringan.</p>
          </div>
          <div class="nusantara-item">
            <h4>4. Tembakau Vorstenlanden (Jawa Tengah)</h4>
            <small>Asal: Klaten &amp; Boyolali, Jawa Tengah</small>
            <p>Aroma sedap, gurih, dan halus. Sangat cocok untuk racikan kretek maupun lintingan harian yang tidak menyengat.</p>
          </div>
          <div class="nusantara-item">
            <h4>5. Tembakau Temanggung &amp; Srinthil</h4>
            <small>Asal: Lereng Gunung Sumbing &amp; Sindoro</small>
            <p>Legendaris dengan kandungan nikotin tinggi, aroma pekat khas, dan rasa gurih yang menjadi kunci racikan kretek.</p>
          </div>
          <div class="nusantara-item">
            <h4>6. Tembakau Madura &amp; Besuki (Jawa Timur)</h4>
            <small>Asal: Pamekasan, Sumenep &amp; Jember</small>
            <p>Harum menyengat, daya bakar baik, serta rasa gurih manis yang hangat di setiap tarikan.</p>
          </div>
          <div class="nusantara-item">
            <h4>7. Tembakau Virginia Lombok (NTB)</h4>
            <small>Asal: Lombok Timur &amp; Tengah, NTB</small>
            <p>Berwarna kuning keemasan hasil pengeringan oven, rasa ringan cenderung manis alami dan pembakaran rata.</p>
          </div>
          <div class="nusantara-item">
            <h4>8. Tembakau Bone / Soppeng (Sulawesi)</h4>
            <small>Asal: Sulawesi Selatan</small>
            <p>Aroma kayu manis alami, rasa hangat dan pulen, sering dinikmati langsung maupun dicampur cengkeh.</p>
          </div>
          <div class="nusantara-item">
            <h4>9. Tembakau Lembah Baliem (Papua - Merauke)</h4>
            <small>Asal: Pegunungan Tengah Papua</small>
            <p>Tembakau tradisional pegunungan yang diolah alami tanpa bahan tambahan, berkarakter earthy dan murni.</p>
          </div>
        </div>
      </div>

    </div>

    <!-- TAMPILAN KASIR -->
    <div id="section-kasir" class="hidden">

      <div class="filter-bar" id="kasir-nav-tabs" style="margin-bottom: 20px;">
        <button class="filter-btn active" onclick="switchKasirTab('transaksi', this)"><i class="fa-solid fa-cash-register"></i> Transaksi Kasir</button>
        <button class="filter-btn" onclick="switchKasirTab('laporan', this)"><i class="fa-solid fa-receipt"></i> Laporan Penjualan</button>
        <button class="filter-btn" onclick="switchKasirTab('absensi', this)"><i class="fa-solid fa-clipboard-user"></i> Absensi &amp; Lokasi</button>
      </div>

      <!-- TAB 1: TRANSAKSI KASIR -->
      <div id="kasir-tab-transaksi" class="card">
        <h2><i class="fa-solid fa-cart-plus"></i> Kasir Penjualan Toko</h2>

        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="kasir-search" placeholder="Cari nama barang..." oninput="setSearch('kasir', this.value)">
        </div>

        <span class="filter-label">1. Pilih Jenis Barang</span>
        <div class="filter-bar" id="kasir-cat-bar">
          <button class="filter-btn active" onclick="filterKategori('kasir', 'all', this)">Semua Produk</button>
          <button class="filter-btn" onclick="filterKategori('kasir', 'tembako olahan', this)">Tembako Olahan</button>
          <button class="filter-btn" onclick="filterKategori('kasir', 'original', this)">Original</button>
          <button class="filter-btn" onclick="filterKategori('kasir', 'racik', this)">Racik</button>
          <button class="filter-btn" onclick="filterKategori('kasir', 'papir', this)">Papir</button>
          <button class="filter-btn" onclick="filterKategori('kasir', 'cengkeh', this)">Cengkeh</button>
          <button class="filter-btn" onclick="filterKategori('kasir', 'aksesoris', this)">Aksesoris</button>
          <button class="filter-btn" onclick="filterKategori('kasir', 'alat', this)">Alat</button>
        </div>

        <span class="filter-label">2. Pilih Takaran / Beli Bebas (Khusus Tembakau)</span>
        <div class="filter-bar" id="kasir-ons-bar">
          <button class="filter-btn active" onclick="filterOns('kasir', 'all', this)">Semua Pilihan</button>
          <button class="filter-btn" onclick="filterOns('kasir', '1_ons', this)">1 Ons (100 gram)</button>
          <button class="filter-btn" onclick="filterOns('kasir', 'setengah_ons', this)">1/2 Ons (50 gram)</button>
          <button class="filter-btn" onclick="filterOns('kasir', 'custom', this)">Beli Bebas (Nominal Uang)</button>
        </div>

        <div style="display: flex; gap: 24px; flex-wrap: wrap;">
          <div style="flex: 2; min-width: 320px;">
            <div class="product-grid" id="kasir-catalog-grid"></div>
            <div class="pagination-bar" id="kasir-pagination"></div>
          </div>
          <div style="flex: 1; min-width: 300px; background: #202020; padding: 20px; border-radius: 12px; border: 1px solid var(--card-border);">
            <h3>Detail Nota Belanja</h3>
            <div id="cart-pos-items" style="margin: 15px 0;"></div>
            <hr style="margin: 12px 0; border-color: var(--card-border);">
            <p style="font-weight: bold; font-size: 1.15rem; display: flex; justify-content: space-between; margin-bottom: 12px;">
              Total Tagihan: <span id="cart-pos-total" style="color: var(--primary-accent);">Rp 0</span>
            </p>

            <label style="font-size: 0.85rem; display:block; margin-bottom:6px;">Metode Pembayaran:</label>
            <div class="pay-method-group">
              <button type="button" class="pay-method-btn active" id="pay-btn-cash" onclick="setPaymentMethod('Cash')">
                <i class="fa-solid fa-money-bill-wave"></i> Cash
              </button>
              <button type="button" class="pay-method-btn" id="pay-btn-qris" onclick="setPaymentMethod('QRIS')">
                <i class="fa-solid fa-qrcode"></i> QRIS
              </button>
            </div>

            <div id="cash-input-wrap">
              <label style="font-size: 0.85rem;">Uang Tunai Pembeli (Rp):</label>
              <input type="text" inputmode="numeric" id="bayar-uang" placeholder="Contoh: 50.000" oninput="formatRibuanInput(this); hitungKembalian();">
              <p style="font-weight: bold; color: var(--danger); margin-bottom: 15px; font-size: 1.05rem;">
                Kembalian: <span id="bayar-kembali">Rp 0</span>
              </p>
            </div>

            <div id="qris-info-wrap" class="hidden" style="background:#18241d; border:1px solid var(--success); border-radius:8px; padding:10px; margin-bottom:14px; font-size:0.85rem; color:var(--success);">
              <i class="fa-solid fa-circle-check"></i> Pembayaran QRIS sesuai total tagihan (Kembalian Rp 0).
            </div>

            <button class="btn-add-cart" style="width:100%;" onclick="prosesTransaksi()">Simpan &amp; Cetak Nota Belanja</button>
          </div>
        </div>
      </div>

      <!-- TAB 2: LAPORAN PENJUALAN KASIR -->
      <div id="kasir-tab-laporan" class="card hidden">
        <h2><i class="fa-solid fa-receipt"></i> Laporan Penjualan Toko</h2>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Nomor Nota</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Metode</th>
                <th>Barang Dibeli</th>
                <th>Total</th>
                <th>Bayar</th>
                <th>Kembali</th>
                <th>Nota</th>
              </tr>
            </thead>
            <tbody id="table-kasir-laporan"></tbody>
          </table>
        </div>
      </div>

      <!-- TAB 3: ABSENSI JAM KERJA KASIR + PETA LOKASI -->
      <div id="kasir-tab-absensi" class="card hidden">
        <h2><i class="fa-solid fa-clipboard-user"></i> Absensi Kehadiran &amp; Lokasi Kasir</h2>
        <form onsubmit="simpanAbsensi(event)" style="margin-bottom: 24px;">
          <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <select id="absen-shift" required style="flex: 1;">
              <option value="Shift 1 (09:00 - 12:00)">Shift 1 (09:00 - 12:00)</option>
              <option value="Shift 2 (12:00 - 21:00)">Shift 2 (12:00 - 21:00)</option>
              <option value="Full Day / Lembur (09:00 - 21:00)">Full Day / Lembur (09:00 - 21:00)</option>
            </select>
            <select id="absen-status" required style="flex: 1;">
              <option value="Hadir">Hadir Kerja</option>
              <option value="Izin">Izin</option>
              <option value="Sakit">Sakit</option>
            </select>
          </div>

          <div class="gps-box">
            <div>
              <i class="fa-solid fa-location-crosshairs" style="color: var(--primary-accent);"></i>
              <span id="gps-status-text">Lokasi belum diambil. Tekan tombol di samping untuk cek lokasi Anda.</span>
            </div>
            <button type="button" class="btn-sm" onclick="ambilLokasiPeta()"><i class="fa-solid fa-location-dot"></i> Periksa Lokasi</button>
          </div>

          <iframe id="kasir-gps-map" class="gps-map-preview hidden" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

          <input type="text" id="absen-catatan" placeholder="Catatan jam kerja / lembur (Opsional)">
          <button type="submit" class="btn-add-cart" style="width: 100%;">Simpan Absensi &amp; Lokasi</button>
        </form>

        <h3 style="font-size: 1rem; color: var(--primary-accent); margin-bottom: 10px;">Riwayat Absensi Terbaru</h3>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Nama Kasir</th>
                <th>Jam Kerja / Shift</th>
                <th>Status</th>
                <th>Lokasi &amp; Akurasi</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody id="table-kasir-absensi"></tbody>
          </table>
        </div>
      </div>

    </div>

  </div>

  <!-- MODAL BELI BEBAS / ECERAN (NOMINAL UANG / GRAM) -->
  <div id="modal-custom-eceran" class="modal-backdrop hidden">
    <div class="modal-box">
      <div class="modal-header">
        <h3><i class="fa-solid fa-scale-balanced"></i> Beli Bebas (Sesuai Uang Pembeli)</h3>
        <button class="modal-close" onclick="closeCustomModal()">&times;</button>
      </div>
      <form onsubmit="submitCustomEceran(event)">
        <input type="hidden" id="custom-prod-id">
        <p id="custom-prod-info" style="font-weight:700; color:#fff; margin-bottom:12px;"></p>

        <label style="font-size:0.85rem; color:var(--text-muted); display:block; margin-bottom:4px;">Uang Pembeli / Harga (Rp)</label>
        <input type="text" inputmode="numeric" id="custom-harga-input" placeholder="Contoh: 15.000" required oninput="formatRibuanInput(this); hitungGramDariNominal();">

        <label style="font-size:0.85rem; color:var(--text-muted); display:block; margin-bottom:4px;">Jumlah Berat (gram)</label>
        <input type="number" id="custom-gram-input" min="1" required>
        <p id="custom-stok-hint" style="font-size:0.8rem; color:var(--text-muted); margin-bottom:14px;"></p>

        <button type="submit" class="btn-add-cart" style="width:100%;">Masukkan ke Nota Belanja</button>
      </form>
    </div>
  </div>

  <!-- TEMPLATE NOTA PRINT -->
  <div id="receipt-print">
    <div style="text-align: center; margin-bottom: 8px;">
      <h3>TOKO MORO JODHO</h3>
      <p style="font-size: 10px;">Jl. Simongan, Semarang Barat</p>
      <p style="font-size: 10px;">WA: +62 857-2804-0343</p>
      <p>================================</p>
    </div>
    <div style="font-size: 11px;">
      <p>NO: <span id="print-id"></span></p>
      <p>WAKTU: <span id="print-waktu"></span></p>
      <p>KASIR: <b id="print-kasir"></b></p>
      <p>METODE: <b id="print-metode"></b></p>
    </div>
    <p>================================</p>
    <div id="print-items" style="font-size: 11px;"></div>
    <p>================================</p>
    <div style="font-size: 11px;">
      <p style="display: flex; justify-content: space-between;"><span>TOTAL:</span> <span id="print-total"></span></p>
      <p style="display: flex; justify-content: space-between;"><span>BAYAR:</span> <span id="print-bayar"></span></p>
      <p style="display: flex; justify-content: space-between;"><span>KEMBALI:</span> <span id="print-kembali"></span></p>
    </div>
    <p style="text-align: center; margin-top: 10px; font-size: 10px;">
      Matur Nuwun Sampun Belanja!
    </p>
  </div>

  <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const DEFAULT_IMG = @json(asset('assets/logo-icon.jpg'));
    const TOBACCO_CATS = ['tembako olahan', 'original', 'racik'];
    const ITEMS_PER_PAGE = 9;

    let db = { produk: [], absensi: [], penjualan: [], pembelian: [] };
    let filters = {
      customer: { cat: 'all', ons: 'all', q: '', page: 1 },
      kasir: { cat: 'all', ons: 'all', q: '', page: 1 },
    };
    let waCart = [];
    let posCart = [];
    let paymentMethod = 'Cash';
    let currentUser = @json($currentUser);
    let gpsCoords = { latitude: null, longitude: null, akurasi: null };

    function isTobacco(kat) {
      return TOBACCO_CATS.includes((kat || '').toLowerCase());
    }

    function imgSrc(path) {
      if (!path) return DEFAULT_IMG;
      if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) return path;
      return '/' + path;
    }

    function formatRibuanInput(el) {
      const digits = (el.value || '').replace(/\D/g, '');
      el.value = digits ? Number(digits).toLocaleString('id-ID') : '';
    }

    function parseAngka(val) {
      return Number(String(val || '').replace(/\D/g, '')) || 0;
    }

    function togglePin(inputId, btn) {
      const input = document.getElementById(inputId);
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    async function apiRequest(url, options = {}) {
      const headers = {
        'Accept': 'application/json',
        ...(options.body ? { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } : {}),
        ...(options.headers || {}),
      };
      const res = await fetch(url, { ...options, headers });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.message || 'Terjadi kendala, silakan coba lagi.');
      return data;
    }

    async function loadData() {
      try {
        db = await apiRequest('/pos-api/all');
        renderCatalog('customer');
        if (currentUser && currentUser.role === 'kasir') {
          renderCatalog('kasir');
          renderKasirLaporan();
          renderKasirAbsensi();
        }
      } catch (e) {
        console.error(e);
      }
    }

    function formatRupiah(n) {
      return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function setSearch(scope, query) {
      filters[scope].q = (query || '').trim().toLowerCase();
      filters[scope].page = 1;
      renderCatalog(scope);
    }

    function filterKategori(scope, kat, btnEl) {
      filters[scope].cat = kat;
      filters[scope].page = 1;
      document.querySelectorAll(`#${scope}-cat-bar .filter-btn`).forEach(b => b.classList.remove('active'));
      if (btnEl) btnEl.classList.add('active');
      renderCatalog(scope);
    }

    function filterOns(scope, ons, btnEl) {
      filters[scope].ons = ons;
      filters[scope].page = 1;
      document.querySelectorAll(`#${scope}-ons-bar .filter-btn`).forEach(b => b.classList.remove('active'));
      if (btnEl) btnEl.classList.add('active');
      renderCatalog(scope);
    }

    function goToPage(scope, page) {
      filters[scope].page = page;
      renderCatalog(scope);
    }

    function switchKasirTab(tab, btnEl) {
      document.querySelectorAll('#kasir-nav-tabs .filter-btn').forEach(btn => btn.classList.remove('active'));
      if (btnEl) btnEl.classList.add('active');
      document.getElementById('kasir-tab-transaksi').classList.toggle('hidden', tab !== 'transaksi');
      document.getElementById('kasir-tab-laporan').classList.toggle('hidden', tab !== 'laporan');
      document.getElementById('kasir-tab-absensi').classList.toggle('hidden', tab !== 'absensi');
      if (tab === 'laporan') renderKasirLaporan();
      if (tab === 'absensi') {
        renderKasirAbsensi();
        ambilLokasiPeta();
      }
    }

    function renderCatalog(scope) {
      const container = document.getElementById(scope === 'customer' ? 'customer-catalog-grid' : 'kasir-catalog-grid');
      const pagEl = document.getElementById(scope === 'customer' ? 'customer-pagination' : 'kasir-pagination');
      const { cat, ons, q, page } = filters[scope];

      const filtered = db.produk.filter(p => {
        if (cat !== 'all' && p.kategori.toLowerCase() !== cat.toLowerCase()) return false;
        if (ons !== 'all' && !isTobacco(p.kategori)) return false;
        if (q && !p.nama.toLowerCase().includes(q) && !p.kategori.toLowerCase().includes(q)) return false;
        return true;
      });

      const totalPages = Math.max(1, Math.ceil(filtered.length / ITEMS_PER_PAGE));
      const safePage = Math.min(Math.max(1, page), totalPages);
      filters[scope].page = safePage;
      const startIdx = (safePage - 1) * ITEMS_PER_PAGE;
      const pageItems = filtered.slice(startIdx, startIdx + ITEMS_PER_PAGE);

      const addFn = scope === 'customer' ? 'addToWACart' : 'addToPOSCart';

      container.innerHTML = pageItems.map(p => {
        const tobacco = isTobacco(p.kategori);
        const halfPrice = p.harga_setengah_ons ?? Math.round(p.harga_jual / 2);
        const isHabis = Number(p.stok) <= 0;
        const disabledAttr = isHabis ? 'disabled' : '';

        let priceHtml = `<div class="product-price">${formatRupiah(p.harga_jual)} / ${p.satuan}</div>`;
        let buttonsHtml = `<button class="btn-add-cart" ${disabledAttr} onclick="${addFn}(${p.id}, 'satuan')">${isHabis ? 'Stok Habis' : '+ Tambah'}</button>`;

        if (tobacco) {
          if (ons === '1_ons') {
            priceHtml = `<div class="product-price">${formatRupiah(p.harga_jual)} <span class="product-subprice">Takaran 1 Ons (100 gram)</span></div>`;
            buttonsHtml = `<button class="btn-add-cart" ${disabledAttr} onclick="${addFn}(${p.id}, '1_ons')">+ 1 Ons (100g)</button>`;
          } else if (ons === 'setengah_ons') {
            priceHtml = `<div class="product-price">${formatRupiah(halfPrice)} <span class="product-subprice">Takaran 1/2 Ons (50 gram)</span></div>`;
            buttonsHtml = `<button class="btn-add-cart" ${disabledAttr} onclick="${addFn}(${p.id}, 'setengah_ons')">+ 1/2 Ons (50g)</button>`;
          } else if (ons === 'custom' && scope === 'kasir') {
            priceHtml = `<div class="product-price">${formatRupiah(p.harga_jual)} / 1 Ons <span class="product-subprice">Input nominal bebas</span></div>`;
            buttonsHtml = `<button class="btn-add-custom" style="margin-top:0;" ${disabledAttr} onclick="openCustomModal(${p.id})">+ Beli Bebas (Rp / gram)</button>`;
          } else {
            priceHtml = `
              <div class="product-price">
                ${formatRupiah(p.harga_jual)} / 1 Ons
                <span class="product-subprice">1/2 Ons (50g): ${formatRupiah(halfPrice)}</span>
              </div>`;
            buttonsHtml = `
              <div class="btn-row">
                <button class="btn-add-cart" ${disabledAttr} onclick="${addFn}(${p.id}, '1_ons')">+ 1 Ons</button>
                <button class="btn-add-half" ${disabledAttr} onclick="${addFn}(${p.id}, 'setengah_ons')">+ 1/2 Ons</button>
              </div>
              ${scope === 'kasir' ? `<button class="btn-add-custom" ${disabledAttr} onclick="openCustomModal(${p.id})">+ Beli Bebas (Rp)</button>` : ''}`;
          }
        }

        const stokHtml = isHabis
          ? `<div class="product-stock stock-empty">Stok: Habis (0 ${p.satuan})</div>`
          : `<div class="product-stock stock-ok">Stok tersedia: ${Number(p.stok).toLocaleString('id-ID')} ${p.satuan}</div>`;

        return `
          <div class="product-card">
            <div class="product-img-wrapper">
              <span class="unit-badge">${tobacco ? 'gram / ons' : p.satuan}</span>
              <img src="${imgSrc(p.gambar)}" alt="${p.nama}" class="product-img" onerror="this.src='${DEFAULT_IMG}'">
            </div>
            <div class="product-details">
              <div>
                <div class="product-category">${p.kategori}</div>
                <div class="product-title">${p.nama}</div>
                ${priceHtml}
                ${stokHtml}
              </div>
              <div>${buttonsHtml}</div>
            </div>
          </div>
        `;
      }).join('') || '<p style="color:var(--text-muted);">Barang yang dicari tidak ditemukan.</p>';

      if (totalPages <= 1) {
        pagEl.innerHTML = '';
      } else {
        let btns = `<button class="page-btn" ${safePage <= 1 ? 'disabled' : ''} onclick="goToPage('${scope}', ${safePage - 1})">&laquo; Sebelumnya</button>`;
        for (let i = 1; i <= totalPages; i++) {
          btns += `<button class="page-btn ${i === safePage ? 'active' : ''}" onclick="goToPage('${scope}', ${i})">${i}</button>`;
        }
        btns += `<button class="page-btn" ${safePage >= totalPages ? 'disabled' : ''} onclick="goToPage('${scope}', ${safePage + 1})">Berikutnya &raquo;</button>`;
        pagEl.innerHTML = btns;
      }
    }

    function buildCartItem(p, takaran) {
      if (takaran === '1_ons') {
        return { id: p.id, nama: p.nama, takaran: '1_ons', satuanLabel: '1 Ons (100g)', harga: p.harga_jual, qty: 1 };
      }
      if (takaran === 'setengah_ons') {
        const half = p.harga_setengah_ons ?? Math.round(p.harga_jual / 2);
        return { id: p.id, nama: p.nama, takaran: 'setengah_ons', satuanLabel: '1/2 Ons (50g)', harga: half, qty: 1 };
      }
      return { id: p.id, nama: p.nama, takaran: 'satuan', satuanLabel: p.satuan, harga: p.harga_jual, qty: 1 };
    }

    function addToWACart(id, takaran) {
      const p = db.produk.find(prod => prod.id == id);
      if (!p || p.stok <= 0) return;
      const existing = waCart.find(i => i.id == id && i.takaran === takaran);
      if (existing) existing.qty++;
      else waCart.push(buildCartItem(p, takaran));
      renderWACart();
    }

    function renderWACart() {
      const container = document.getElementById('wa-cart-list');
      let total = 0;
      container.innerHTML = waCart.map((i, idx) => {
        const sub = i.harga * i.qty;
        total += sub;
        return `
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-size:0.85rem;">
            <div>
              <b>${i.nama}</b><br>
              <span style="color:var(--text-muted);">${i.qty} x ${i.satuanLabel} (${formatRupiah(i.harga)})</span>
            </div>
            <button style="background:var(--danger); color:#fff; border:none; padding:4px 8px; border-radius:4px; cursor:pointer;" onclick="waCart.splice(${idx},1); renderWACart();">x</button>
          </div>
        `;
      }).join('') || '<p style="color:var(--text-muted); font-size:0.85rem;">Keranjang belanja kosong.</p>';

      document.getElementById('wa-cart-total').innerText = formatRupiah(total);
    }

    function kirimPesananWA() {
      if (waCart.length === 0) return alert('Silakan pilih barang terlebih dahulu!');
      const noWA = "6285728040343";
      let pesan = "Halo Toko Moro Jodho, saya ingin memesan:\n\n";
      let total = 0;
      waCart.forEach((item, i) => {
        const sub = item.harga * item.qty;
        total += sub;
        pesan += `${i + 1}. ${item.nama} (${item.qty} x ${item.satuanLabel}) = ${formatRupiah(sub)}\n`;
      });
      pesan += `\n*Total Perkiraan: ${formatRupiah(total)}*\n\nMohon info ketersediaannya, terima kasih!`;
      window.open(`https://wa.me/${noWA}?text=${encodeURIComponent(pesan)}`, '_blank');
    }

    function switchRole(role) {
      if (role === 'customer') return renderRole('customer');
      if (currentUser && currentUser.role === 'kasir') return renderRole('kasir');

      document.getElementById('section-customer').classList.add('hidden');
      document.getElementById('section-kasir').classList.add('hidden');
      document.getElementById('section-login').classList.remove('hidden');
    }

    async function handleLogin(e) {
      e.preventDefault();
      try {
        const res = await apiRequest('/pos-api/login', {
          method: 'POST',
          body: JSON.stringify({
            username: document.getElementById('login-username').value,
            password: document.getElementById('login-password').value,
            role: 'kasir',
            latitude: gpsCoords.latitude,
            longitude: gpsCoords.longitude,
            akurasi: gpsCoords.akurasi,
          }),
        });
        currentUser = res.user;
        document.getElementById('login-password').value = '';
        await loadData();
        renderRole('kasir');
      } catch (err) {
        alert(err.message);
      }
    }

    async function logout() {
      try {
        await apiRequest('/pos-api/logout', { method: 'POST', body: JSON.stringify({}) });
      } catch (e) {}
      currentUser = null;
      switchRole('customer');
    }

    function renderRole(role) {
      document.getElementById('section-login').classList.add('hidden');
      document.getElementById('section-customer').classList.toggle('hidden', role !== 'customer');
      document.getElementById('section-kasir').classList.toggle('hidden', role !== 'kasir');

      document.getElementById('btn-customer').classList.toggle('active', role === 'customer');
      document.getElementById('btn-kasir').classList.toggle('active', role === 'kasir');
      document.getElementById('btn-logout').classList.toggle('hidden', role === 'customer');

      if (role === 'kasir') {
        renderCatalog('kasir');
        renderKasirLaporan();
        renderKasirAbsensi();
      }
    }

    function addToPOSCart(id, takaran) {
      const p = db.produk.find(prod => prod.id == id);
      if (!p || p.stok <= 0) return;
      const existing = posCart.find(i => i.id == id && i.takaran === takaran);
      if (existing) existing.qty++;
      else posCart.push(buildCartItem(p, takaran));
      renderPOSCart();
    }

    function openCustomModal(id) {
      const p = db.produk.find(prod => prod.id == id);
      if (!p) return;
      document.getElementById('custom-prod-id').value = p.id;
      document.getElementById('custom-prod-info').innerText = `${p.nama} (${formatRupiah(p.harga_jual)} / 100 gram)`;
      document.getElementById('custom-stok-hint').innerText = `Stok tersedia saat ini: ${p.stok} ${p.satuan}`;
      document.getElementById('custom-harga-input').value = '';
      document.getElementById('custom-gram-input').value = '';
      document.getElementById('modal-custom-eceran').classList.remove('hidden');
    }

    function closeCustomModal() {
      document.getElementById('modal-custom-eceran').classList.add('hidden');
    }

    function hitungGramDariNominal() {
      const id = document.getElementById('custom-prod-id').value;
      const p = db.produk.find(prod => prod.id == id);
      if (!p || p.harga_jual <= 0) return;
      const nominal = parseAngka(document.getElementById('custom-harga-input').value);
      const gram = Math.max(1, Math.round((nominal / p.harga_jual) * 100));
      document.getElementById('custom-gram-input').value = nominal > 0 ? gram : '';
    }

    function submitCustomEceran(e) {
      e.preventDefault();
      const id = document.getElementById('custom-prod-id').value;
      const p = db.produk.find(prod => prod.id == id);
      if (!p) return;
      const nominal = parseAngka(document.getElementById('custom-harga-input').value);
      const gram = Number(document.getElementById('custom-gram-input').value) || 0;
      if (nominal < 100 || gram < 1) return alert('Masukkan nominal uang dan berat gram yang sesuai!');
      if (gram > p.stok) return alert(`Stok tidak mencukupi (sisa ${p.stok} ${p.satuan}).`);

      posCart.push({
        id: p.id,
        nama: p.nama,
        takaran: 'custom',
        satuanLabel: `${gram} gram (Eceran)`,
        harga: nominal,
        custom_gram: gram,
        custom_harga: nominal,
        qty: 1,
      });
      closeCustomModal();
      renderPOSCart();
    }

    function setPaymentMethod(method) {
      paymentMethod = method;
      document.getElementById('pay-btn-cash').classList.toggle('active', method === 'Cash');
      document.getElementById('pay-btn-qris').classList.toggle('active', method === 'QRIS');
      document.getElementById('cash-input-wrap').classList.toggle('hidden', method !== 'Cash');
      document.getElementById('qris-info-wrap').classList.toggle('hidden', method !== 'QRIS');
    }

    function renderPOSCart() {
      const container = document.getElementById('cart-pos-items');
      let total = 0;
      container.innerHTML = posCart.map((i, idx) => {
        const sub = i.harga * i.qty;
        total += sub;
        return `
          <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.85rem; margin-bottom:6px;">
            <span>${i.nama} (${i.qty} x ${i.satuanLabel})</span>
            <span>
              ${formatRupiah(sub)}
              <button style="background:var(--danger); color:#fff; border:none; padding:2px 6px; border-radius:4px; cursor:pointer; margin-left:6px;" onclick="posCart.splice(${idx},1); renderPOSCart();">x</button>
            </span>
          </div>
        `;
      }).join('') || '<p style="color:var(--text-muted); font-size:0.85rem;">Belum ada barang dipilih.</p>';
      document.getElementById('cart-pos-total').innerText = formatRupiah(total);
      hitungKembalian();
    }

    function hitungKembalian() {
      const total = posCart.reduce((a, i) => a + (i.harga * i.qty), 0);
      const bayar = parseAngka(document.getElementById('bayar-uang').value);
      document.getElementById('bayar-kembali').innerText = formatRupiah(bayar > total ? bayar - total : 0);
    }

    async function prosesTransaksi() {
      const total = posCart.reduce((a, i) => a + (i.harga * i.qty), 0);
      const bayar = paymentMethod === 'QRIS' ? total : parseAngka(document.getElementById('bayar-uang').value);
      if (posCart.length === 0) return alert('Keranjang Kasir masih kosong!');
      if (paymentMethod === 'Cash' && bayar < total) return alert('Uang tunai pembeli kurang!');

      try {
        const res = await apiRequest('/pos-api/penjualan', {
          method: 'POST',
          body: JSON.stringify({
            items: posCart.map(i => ({
              id: i.id,
              qty: i.qty,
              takaran: i.takaran,
              ...(i.takaran === 'custom' ? { custom_gram: i.custom_gram, custom_harga: i.custom_harga } : {}),
            })),
            metode_pembayaran: paymentMethod,
            bayar: bayar,
          }),
        });

        cetakStruk(res.transaksi);
        posCart = [];
        document.getElementById('bayar-uang').value = '';
        renderPOSCart();
        await loadData();
      } catch (err) {
        alert(err.message);
      }
    }

    function renderKasirLaporan() {
      const tbody = document.getElementById('table-kasir-laporan');
      tbody.innerHTML = db.penjualan.map((t, idx) => {
        const itemsHtml = (t.items || []).map(i => `${i.nama} (${i.qty} x ${i.satuan})`).join(', ');
        return `
          <tr>
            <td><b>${t.kode_transaksi}</b></td>
            <td>${t.waktu}</td>
            <td>${t.kasir}</td>
            <td><b>${t.metode_pembayaran || 'Cash'}</b></td>
            <td>${itemsHtml}</td>
            <td><b>${formatRupiah(t.total)}</b></td>
            <td>${formatRupiah(t.bayar)}</td>
            <td>${formatRupiah(t.kembali)}</td>
            <td><button class="btn-sm" onclick="cetakStrukDariRiwayat(${idx})"><i class="fa-solid fa-print"></i> Cetak</button></td>
          </tr>
        `;
      }).join('') || '<tr><td colspan="9" style="text-align:center; color:var(--text-muted);">Belum ada transaksi penjualan.</td></tr>';
    }

    function renderKasirAbsensi() {
      const tbody = document.getElementById('table-kasir-absensi');
      tbody.innerHTML = db.absensi.map(a => {
        const locText = (a.latitude && a.longitude)
          ? `Akurasi ±${a.akurasi ?? '-'} m<br><a href="https://maps.google.com/?q=${a.latitude},${a.longitude}" target="_blank" style="color:var(--primary-accent);">Buka Peta</a>`
          : 'Lokasi belum diambil';
        return `
          <tr>
            <td>${a.waktu}</td>
            <td><b>${a.karyawan}</b></td>
            <td>${a.shift}</td>
            <td>${a.status}</td>
            <td>${locText}</td>
            <td>${a.catatan || '-'}</td>
          </tr>
        `;
      }).join('') || '<tr><td colspan="6" style="text-align:center; color:var(--text-muted);">Belum ada catatan absensi.</td></tr>';
    }

    function ambilLokasiPeta() {
      const statusEl = document.getElementById('gps-status-text');
      const mapFrame = document.getElementById('kasir-gps-map');
      if (!navigator.geolocation) {
        statusEl.innerText = 'Perangkat ini tidak mendukung deteksi lokasi.';
        return;
      }
      statusEl.innerText = 'Sedang mencari titik lokasi Anda...';
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          gpsCoords.latitude = Number(pos.coords.latitude.toFixed(7));
          gpsCoords.longitude = Number(pos.coords.longitude.toFixed(7));
          gpsCoords.akurasi = Math.round(pos.coords.accuracy);
          statusEl.innerHTML = `<span style="color:var(--success);">Lokasi ditemukan!</span> Akurasi ±${gpsCoords.akurasi} meter`;
          mapFrame.src = `https://maps.google.com/maps?q=${gpsCoords.latitude},${gpsCoords.longitude}&z=16&output=embed`;
          mapFrame.classList.remove('hidden');
        },
        () => {
          statusEl.innerHTML = `<span style="color:var(--danger);">Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.</span>`;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
      );
    }

    function cetakStrukDariRiwayat(idx) {
      const t = db.penjualan[idx];
      if (!t) return;
      cetakStruk({
        id: t.kode_transaksi,
        waktu: t.waktu,
        kasir: t.kasir,
        metode_pembayaran: t.metode_pembayaran || 'Cash',
        items: t.items || [],
        total: t.total,
        bayar: t.bayar,
        kembali: t.kembali,
      });
    }

    function cetakStruk(trx) {
      document.getElementById('print-id').innerText = trx.id;
      document.getElementById('print-waktu').innerText = trx.waktu;
      document.getElementById('print-kasir').innerText = trx.kasir;
      document.getElementById('print-metode').innerText = trx.metode_pembayaran || 'Cash';
      document.getElementById('print-items').innerHTML = trx.items.map(i => `
        <div style="display:flex; justify-content:space-between;">
          <span>${i.nama} (${i.qty}x ${i.satuan})</span>
          <span>${formatRupiah(i.harga_jual * i.qty)}</span>
        </div>
      `).join('');
      document.getElementById('print-total').innerText = formatRupiah(trx.total);
      document.getElementById('print-bayar').innerText = formatRupiah(trx.bayar);
      document.getElementById('print-kembali').innerText = formatRupiah(trx.kembali);
      window.print();
    }

    async function simpanAbsensi(e) {
      e.preventDefault();
      try {
        await apiRequest('/pos-api/absensi', {
          method: 'POST',
          body: JSON.stringify({
            shift: document.getElementById('absen-shift').value,
            status: document.getElementById('absen-status').value,
            catatan: document.getElementById('absen-catatan').value || '-',
            latitude: gpsCoords.latitude,
            longitude: gpsCoords.longitude,
            akurasi: gpsCoords.akurasi,
          }),
        });
        alert('Absensi & lokasi berhasil disimpan!');
        document.getElementById('absen-catatan').value = '';
        await loadData();
      } catch (err) {
        alert(err.message);
      }
    }

    renderWACart();
    loadData();
  </script>
</body>
</html>
