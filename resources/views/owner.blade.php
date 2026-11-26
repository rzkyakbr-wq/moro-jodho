<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Halaman Pemilik Toko - Moro Jodho</title>
  <link rel="icon" type="image/jpeg" href="{{ asset('assets/logo-icon.jpg') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    nav { display: flex; gap: 10px; align-items: center; }
    nav a, nav button {
      background: #252525;
      color: var(--text-light);
      border: 1px solid var(--card-border);
      padding: 8px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.9rem;
      text-decoration: none;
      transition: all 0.25s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    nav a:hover, nav button:hover {
      background: var(--primary-accent);
      color: #000;
      border-color: var(--primary-accent);
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

    .search-box {
      position: relative;
      margin-bottom: 14px;
    }
    .search-box i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
    }
    .search-box input {
      padding-left: 40px;
      margin-bottom: 0;
    }

    .filter-bar { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px; margin-bottom: 20px; }
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

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 20px; }
    .stat-card {
      background: #202020;
      border: 1px solid var(--card-border);
      padding: 20px;
      border-radius: 10px;
      border-left: 4px solid var(--primary-accent);
      cursor: pointer;
      transition: transform 0.2s, border-color 0.2s;
    }
    .stat-card:hover { transform: translateY(-3px); border-color: var(--primary-accent); }
    .stat-card h4 { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; }
    .stat-card p { font-size: 1.4rem; font-weight: bold; color: #fff; margin-top: 5px; }
    .stat-card small { display: block; margin-top: 6px; font-size: 0.75rem; color: var(--primary-accent); }

    .date-filter-box {
      background: #202020;
      border: 1px solid var(--card-border);
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 22px;
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      align-items: flex-end;
      justify-content: space-between;
    }
    .date-inputs { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; flex: 1; }
    .date-inputs > div { min-width: 160px; flex: 1; max-width: 220px; }
    .date-inputs label { display: block; font-size: 0.78rem; color: var(--text-muted); margin-bottom: 4px; }
    .date-inputs input { margin-bottom: 0; }

    input, select, textarea {
      background: #222;
      border: 1px solid var(--card-border);
      color: #fff;
      padding: 10px;
      border-radius: 8px;
      width: 100%;
      margin-bottom: 12px;
    }
    label.field-label { font-size: 0.82rem; color: var(--text-muted); display: block; margin-bottom: 4px; }

    .btn-add-cart {
      background: var(--primary-accent);
      color: #000;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 700;
      cursor: pointer;
      width: 100%;
      transition: background 0.2s;
    }
    .btn-add-cart:hover { background: var(--gold-hover); }

    .btn-secondary {
      background: #2b2b2b;
      color: var(--text-light);
      border: 1px solid var(--card-border);
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
    }
    .btn-secondary:hover { background: #383838; }

    .btn-excel {
      background: #1d6f42;
      color: #fff;
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.88rem;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-excel:hover { background: #238850; }

    .btn-restock {
      background: #2ecc71;
      color: #000;
      border: none;
      padding: 6px 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 700;
      font-size: 0.8rem;
    }
    .btn-edit {
      background: var(--primary-accent);
      color: #000;
      border: none;
      padding: 6px 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.8rem;
    }
    .btn-del {
      background: var(--danger);
      color: #fff;
      border: none;
      padding: 6px 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 0.8rem;
    }

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

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table th, table td { border: 1px solid var(--card-border); padding: 12px; text-align: left; font-size: 0.88rem; }
    table th { background: #222; color: var(--primary-accent); font-weight: 700; }

    .hidden { display: none !important; }

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
      max-width: 720px;
      max-height: 90vh;
      overflow-y: auto;
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--card-border);
      padding-bottom: 12px;
      margin-bottom: 16px;
    }
    .modal-header h3 { color: var(--primary-accent); font-size: 1.15rem; }
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

    .upload-preview {
      width: 72px;
      height: 72px;
      border-radius: 8px;
      object-fit: cover;
      border: 1px solid var(--card-border);
      background: #111;
    }

    .btn-map {
      background: #252525;
      color: var(--primary-accent);
      border: 1px solid var(--primary-accent);
      padding: 5px 10px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.8rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 4px;
    }
    .btn-map:hover { background: var(--primary-accent); color: #000; }

    .link-forgot {
      background: none;
      border: none;
      color: var(--primary-accent);
      cursor: pointer;
      font-size: 0.85rem;
      text-decoration: underline;
      margin-top: 12px;
      display: inline-block;
    }
  </style>
</head>
<body>

  <header>
    <div class="brand-logo-container">
      <img src="{{ asset('assets/logo-icon.jpg') }}" alt="Logo Toko Moro Jodho" class="brand-logo-img">
      <div class="brand-title">
        <h1>Moro Jodho — Pemilik</h1>
        <p>Halaman Pengelolaan Toko &amp; Laporan Keuangan</p>
      </div>
    </div>
    <nav>
      <a href="{{ route('pos.index') }}" target="_blank"><i class="fa-solid fa-store"></i> Buka Katalog &amp; Kasir</a>
      <button onclick="logoutOwner()" id="btn-owner-logout" class="{{ ($currentUser['role'] ?? '') === 'owner' ? '' : 'hidden' }}" style="background: var(--danger);">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar
      </button>
    </nav>
  </header>

  <div class="container">

    <!-- FORM MASUK OWNER (GMAIL + KATA SANDI, TANPA TAMPILAN SANDI) -->
    <div id="owner-login-card" class="card {{ ($currentUser['role'] ?? '') === 'owner' ? 'hidden' : '' }}" style="max-width: 420px; margin: 40px auto;">
      <h2><i class="fa-solid fa-lock"></i> Masuk Halaman Pemilik</h2>
      <form onsubmit="handleOwnerLogin(event)">
        <label class="field-label">Email Gmail Pemilik</label>
        <input type="text" id="owner-username" placeholder="Masukkan Gmail Anda..." required autocomplete="username">
        <label class="field-label">Kata Sandi</label>
        <div class="pin-wrapper">
          <input type="password" id="owner-password" placeholder="Masukkan kata sandi..." required autocomplete="current-password">
          <button type="button" class="pin-toggle-btn" onclick="togglePin('owner-password', this)" title="Lihat / Sembunyikan Kata Sandi">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <button type="submit" class="btn-add-cart" style="margin-top: 10px;">Masuk Sekarang</button>
      </form>
      <div style="text-align: center;">
        <button type="button" class="link-forgot" onclick="openForgotModal()">Lupa kata sandi? Atur ulang lewat Gmail</button>
      </div>
    </div>

    <!-- DASHBOARD PEMILIK -->
    <div id="owner-dashboard" class="{{ ($currentUser['role'] ?? '') === 'owner' ? '' : 'hidden' }}">

      <div class="stats-grid">
        <div class="stat-card" onclick="openDetailModal('omset')">
          <h4>Total Omset Penjualan</h4>
          <p id="stat-omset">Rp 0</p>
          <small><i class="fa-solid fa-magnifying-glass"></i> Klik untuk lihat rincian penjualan</small>
        </div>
        <div class="stat-card" onclick="openDetailModal('kulakan')">
          <h4>Total Pengeluaran Kulakan</h4>
          <p id="stat-beli">Rp 0</p>
          <small><i class="fa-solid fa-magnifying-glass"></i> Klik untuk lihat rincian kulakan</small>
        </div>
        <div class="stat-card" onclick="openDetailModal('profit')">
          <h4>Perkiraan Keuntungan Bersih</h4>
          <p id="stat-profit">Rp 0</p>
          <small><i class="fa-solid fa-magnifying-glass"></i> Klik untuk lihat rincian keuntungan</small>
        </div>
      </div>

      <!-- SARING TANGGAL & 1 TOMBOL UNDUH EXCEL -->
      <div class="date-filter-box">
        <div class="date-inputs">
          <div>
            <label>Dari Tanggal</label>
            <input type="date" id="filter-start-date" onchange="applyDateFilter()">
          </div>
          <div>
            <label>Sampai Tanggal</label>
            <input type="date" id="filter-end-date" onchange="applyDateFilter()">
          </div>
          <div style="min-width: auto; flex: 0;">
            <button type="button" class="btn-secondary" onclick="resetDateFilter()">Tampilkan Semua</button>
          </div>
        </div>
        <div>
          <a id="btn-export-excel" href="{{ route('owner.export') }}" class="btn-excel">
            <i class="fa-solid fa-file-excel"></i> Unduh Laporan Excel (.xls)
          </a>
        </div>
      </div>

      <!-- TAB MENU OWNER (DI ATAS) -->
      <div class="filter-bar">
        <button class="filter-btn active" onclick="switchOwnerTab('produk', this)"><i class="fa-solid fa-boxes-stacked"></i> Kelola Produk &amp; Kulakan</button>
        <button class="filter-btn" onclick="switchOwnerTab('penjualan', this)"><i class="fa-solid fa-receipt"></i> Laporan Penjualan</button>
        <button class="filter-btn" onclick="switchOwnerTab('riwayat', this)"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Kulakan &amp; Absensi</button>
        <button class="filter-btn" onclick="switchOwnerTab('kodekasir', this)"><i class="fa-solid fa-key"></i> Kode Kasir &amp; Akun</button>
      </div>

      <!-- TAB 1: PRODUK & KULAKAN -->
      <div id="owner-tab-produk">
        <div class="card">
          <h2><i class="fa-solid fa-plus-circle"></i> Tambah Barang Baru &amp; Catat Kulakan</h2>
          <form onsubmit="simpanProduk(event)">
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
              <div style="flex: 2; min-width: 220px;">
                <label class="field-label">Nama Barang</label>
                <input type="text" id="prod-nama" placeholder="Contoh: Tembakau Mole Madu" required>
              </div>
              <div style="flex: 1; min-width: 160px;">
                <label class="field-label">Jenis Barang</label>
                <select id="prod-kategori" onchange="syncUnitByCategory('prod')">
                  <option value="tembako olahan">Tembako Olahan</option>
                  <option value="original">Original</option>
                  <option value="racik">Racik</option>
                  <option value="papir">Papir</option>
                  <option value="cengkeh">Cengkeh</option>
                  <option value="aksesoris">Aksesoris</option>
                  <option value="alat">Alat</option>
                </select>
              </div>
              <div style="flex: 1; min-width: 130px;">
                <label class="field-label">Satuan Stok</label>
                <select id="prod-satuan">
                  <option value="gram">gram</option>
                  <option value="pics">pics</option>
                  <option value="pack">pack</option>
                </select>
              </div>
            </div>

            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
              <div style="flex: 1; min-width: 150px;">
                <label class="field-label" id="prod-stok-label">Jumlah Stok Beli (gram)</label>
                <input type="number" id="prod-stok" min="1" placeholder="Contoh: 1000" required>
              </div>
              <div style="flex: 1; min-width: 170px;">
                <label class="field-label" id="prod-beli-label">Harga Modal (Per Ons / 100g)</label>
                <input type="text" inputmode="numeric" id="prod-hargabeli" placeholder="Contoh: 45.000" required oninput="formatRibuanInput(this)">
              </div>
              <div style="flex: 1; min-width: 170px;">
                <label class="field-label" id="prod-jual-label">Harga Jual 1 Ons (100g)</label>
                <input type="text" inputmode="numeric" id="prod-hargajual" placeholder="Contoh: 70.000" required oninput="formatRibuanInput(this); autoFillHalfOns('prod');">
              </div>
              <div style="flex: 1; min-width: 170px;" id="prod-half-ons-wrap">
                <label class="field-label">Harga Jual 1/2 Ons (50g)</label>
                <input type="text" inputmode="numeric" id="prod-hargasetengah" placeholder="Otomatis setengah harga" oninput="formatRibuanInput(this); this.dataset.touched='1';">
              </div>
            </div>

            <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-bottom: 12px;">
              <img id="add-preview" src="{{ asset('assets/logo-icon.jpg') }}" alt="Pratinjau Gambar" class="upload-preview">
              <div style="flex: 1; min-width: 220px;">
                <label class="field-label">Foto Produk (Pilih dari Galeri atau Ambil Foto Langsung)</label>
                <input type="file" id="prod-gambar" accept="image/*" capture="environment" style="margin-bottom: 0;" onchange="previewFile(this, 'add-preview')">
              </div>
            </div>
            <button type="submit" class="btn-add-cart">Simpan Barang Baru &amp; Catat Kulakan</button>
          </form>
        </div>

        <div class="card">
          <h2><i class="fa-solid fa-list"></i> Daftar Produk Toko</h2>

          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="owner-prod-search" placeholder="Cari nama barang atau jenis barang..." oninput="renderOwnerProdukTable()">
          </div>

          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Gambar</th>
                  <th>Nama Barang</th>
                  <th>Kategori</th>
                  <th>Stok</th>
                  <th>Harga Modal</th>
                  <th>Harga Jual</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="table-owner-produk"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 2: LAPORAN PENJUALAN -->
      <div id="owner-tab-penjualan" class="card hidden">
        <h2><i class="fa-solid fa-receipt"></i> Laporan Penjualan Toko</h2>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Nomor Nota</th>
                <th>Waktu</th>
                <th>Kasir</th>
                <th>Barang Dibeli</th>
                <th>Total Tagihan</th>
                <th>Uang Bayar</th>
                <th>Kembalian</th>
              </tr>
            </thead>
            <tbody id="table-owner-penjualan"></tbody>
          </table>
        </div>
      </div>

      <!-- TAB 3: RIWAYAT KULAKAN & ABSENSI -->
      <div id="owner-tab-riwayat" class="hidden">
        <div class="card">
          <h2><i class="fa-solid fa-truck-ramp-box"></i> Riwayat Kulakan Barang</h2>
          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Nama Barang</th>
                  <th>Jumlah Masuk</th>
                  <th>Harga Modal</th>
                  <th>Total Pengeluaran</th>
                  <th>Catatan</th>
                </tr>
              </thead>
              <tbody id="table-owner-kulakan"></tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <h2><i class="fa-solid fa-clipboard-user"></i> Riwayat Absensi &amp; Lokasi Kasir</h2>
          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Waktu</th>
                  <th>Nama Kasir</th>
                  <th>Jam Kerja / Shift</th>
                  <th>Status</th>
                  <th>Lokasi &amp; Peta</th>
                  <th>Catatan</th>
                </tr>
              </thead>
              <tbody id="table-owner-absensi"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 4: PENGATURAN KODE KASIR & AKUN PEMILIK -->
      <div id="owner-tab-kodekasir" class="hidden">
        <div class="card">
          <h2><i class="fa-solid fa-key"></i> Pengaturan Kode Masuk Kasir</h2>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px;">
            Kode khusus ini dibuat oleh Pemilik Toko dan diberikan kepada staf kasir agar tidak sembarang orang bisa masuk ke halaman Kasir.
          </p>
          <form onsubmit="simpanKodeKasir(event)" style="max-width: 420px;">
            <label class="field-label">Kode Masuk Kasir Saat Ini / Baru</label>
            <div class="pin-wrapper">
              <input type="password" id="setting-kasir-code" minlength="4" placeholder="Minimal 4 karakter..." required>
              <button type="button" class="pin-toggle-btn" onclick="togglePin('setting-kasir-code', this)" title="Lihat / Sembunyikan Kode">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <button type="submit" class="btn-add-cart" style="margin-top: 8px;">Simpan Kode Kasir</button>
          </form>
        </div>

        <div class="card">
          <h2><i class="fa-solid fa-user-shield"></i> Pengaturan Akun Gmail &amp; Kata Sandi Pemilik</h2>
          <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 16px;">
            Gunakan Gmail pribadi Anda dan kata sandi yang kuat agar akun pemilik tetap aman dan mudah dipulihkan jika lupa sandi.
          </p>
          <form onsubmit="simpanAkunOwner(event)" style="max-width: 420px;">
            <label class="field-label">Email Gmail Pemilik</label>
            <input type="email" id="setting-owner-email" placeholder="nama@gmail.com" required>
            <label class="field-label">Kata Sandi Baru (Kosongkan jika tidak ingin mengubah)</label>
            <div class="pin-wrapper">
              <input type="password" id="setting-owner-password" minlength="8" placeholder="Minimal 8 karakter...">
              <button type="button" class="pin-toggle-btn" onclick="togglePin('setting-owner-password', this)" title="Lihat / Sembunyikan Kata Sandi">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
            <button type="submit" class="btn-add-cart" style="margin-top: 8px;">Simpan Perubahan Akun</button>
          </form>
        </div>
      </div>

      <!-- DIAGRAM KEUANGAN (DI BAWAH) -->
      <div class="card">
        <h2><i class="fa-solid fa-chart-column"></i> Diagram Ringkasan Keuangan Toko</h2>
        <div style="height: 300px; position: relative;">
          <canvas id="financialChart"></canvas>
        </div>
      </div>

    </div>
  </div>

  <!-- MODAL LUPA KATA SANDI (LEWAT GMAIL) -->
  <div id="modal-forgot-password" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width: 450px;">
      <div class="modal-header">
        <h3><i class="fa-solid fa-envelope"></i> Atur Ulang Kata Sandi</h3>
        <button class="modal-close" onclick="closeForgotModal()">&times;</button>
      </div>
      <div id="forgot-step-1">
        <p style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 12px;">
          Masukkan alamat Gmail yang terdaftar pada akun Pemilik. Kami akan mengirimkan kode 6 digit ke Gmail Anda.
        </p>
        <form onsubmit="kirimKodeReset(event)">
          <label class="field-label">Email Gmail Terdaftar</label>
          <input type="email" id="forgot-email" placeholder="contoh@gmail.com" required>
          <button type="submit" class="btn-add-cart">Kirim Kode ke Gmail</button>
        </form>
      </div>
      <div id="forgot-step-2" class="hidden">
        <p style="font-size: 0.88rem; color: var(--success); margin-bottom: 12px;" id="forgot-info-msg"></p>
        <form onsubmit="simpanPasswordBaru(event)">
          <label class="field-label">Kode 6 Digit dari Gmail</label>
          <input type="text" id="reset-code" maxlength="6" placeholder="Contoh: 123456" required>
          <label class="field-label">Kata Sandi Baru (Minimal 8 karakter)</label>
          <div class="pin-wrapper">
            <input type="password" id="reset-new-password" minlength="8" placeholder="Buat kata sandi baru yang kuat..." required>
            <button type="button" class="pin-toggle-btn" onclick="togglePin('reset-new-password', this)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <button type="submit" class="btn-add-cart" style="margin-top: 8px;">Simpan Kata Sandi Baru</button>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL PETA LOKASI ABSENSI -->
  <div id="modal-peta-lokasi" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width: 680px;">
      <div class="modal-header">
        <h3 id="map-modal-title"><i class="fa-solid fa-map-location-dot"></i> Lokasi Absensi Kasir</h3>
        <button class="modal-close" onclick="closeMapModal()">&times;</button>
      </div>
      <p id="map-modal-info" style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 12px;"></p>
      <iframe id="map-modal-iframe" style="width:100%; height:360px; border:1px solid var(--card-border); border-radius:10px;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      <div style="margin-top: 14px; text-align: right;">
        <a id="map-modal-external" href="#" target="_blank" class="btn-excel" style="background: var(--primary-accent); color: #000;">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka di Google Maps
        </a>
      </div>
    </div>
  </div>

  <!-- MODAL KULAKAN / RESTOCK BARANG LAMA -->
  <div id="modal-restock" class="modal-backdrop hidden">
    <div class="modal-box" style="max-width: 460px;">
      <div class="modal-header">
        <h3><i class="fa-solid fa-boxes-packing"></i> Kulakan / Tambah Stok</h3>
        <button class="modal-close" onclick="closeRestockModal()">&times;</button>
      </div>
      <form onsubmit="submitRestock(event)">
        <input type="hidden" id="restock-id">
        <p id="restock-nama" style="font-weight: 700; font-size: 1.05rem; margin-bottom: 12px; color: #fff;"></p>
        <label class="field-label" id="restock-jumlah-label">Jumlah Tambah Stok</label>
        <input type="number" id="restock-jumlah" min="1" required>
        <label class="field-label" id="restock-harga-label">Harga Modal Baru</label>
        <input type="text" inputmode="numeric" id="restock-harga" required oninput="formatRibuanInput(this)">
        <label class="field-label">Catatan (Opsional)</label>
        <input type="text" id="restock-catatan" placeholder="Contoh: Kulakan dari distributor Semarang">
        <button type="submit" class="btn-add-cart">Simpan &amp; Tambah Stok</button>
      </form>
    </div>
  </div>

  <!-- MODAL EDIT PRODUK -->
  <div id="modal-edit-produk" class="modal-backdrop hidden">
    <div class="modal-box">
      <div class="modal-header">
        <h3><i class="fa-solid fa-pen-to-square"></i> Edit Data Produk</h3>
        <button class="modal-close" onclick="closeEditModal()">&times;</button>
      </div>
      <form onsubmit="submitEditProduk(event)">
        <input type="hidden" id="edit-id">
        <label class="field-label">Nama Barang</label>
        <input type="text" id="edit-nama" required>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 160px;">
            <label class="field-label">Kategori</label>
            <select id="edit-kategori" onchange="syncUnitByCategory('edit')">
              <option value="tembako olahan">Tembako Olahan</option>
              <option value="original">Original</option>
              <option value="racik">Racik</option>
              <option value="papir">Papir</option>
              <option value="cengkeh">Cengkeh</option>
              <option value="aksesoris">Aksesoris</option>
              <option value="alat">Alat</option>
            </select>
          </div>
          <div style="flex: 1; min-width: 140px;">
            <label class="field-label">Satuan</label>
            <select id="edit-satuan">
              <option value="gram">gram</option>
              <option value="pics">pics</option>
              <option value="pack">pack</option>
            </select>
          </div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 120px;">
            <label class="field-label" id="edit-stok-label">Stok</label>
            <input type="number" id="edit-stok" min="0" required>
          </div>
          <div style="flex: 1; min-width: 140px;">
            <label class="field-label" id="edit-beli-label">Harga Modal</label>
            <input type="text" inputmode="numeric" id="edit-hargabeli" required oninput="formatRibuanInput(this)">
          </div>
          <div style="flex: 1; min-width: 140px;">
            <label class="field-label" id="edit-jual-label">Harga Jual</label>
            <input type="text" inputmode="numeric" id="edit-hargajual" required oninput="formatRibuanInput(this)">
          </div>
          <div style="flex: 1; min-width: 140px;" id="edit-half-ons-wrap">
            <label class="field-label">Harga Jual 1/2 Ons (50g)</label>
            <input type="text" inputmode="numeric" id="edit-hargasetengah" oninput="formatRibuanInput(this)">
          </div>
        </div>

        <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-bottom: 16px;">
          <img id="edit-preview" src="{{ asset('assets/logo-icon.jpg') }}" alt="Foto Produk" class="upload-preview">
          <div style="flex: 1; min-width: 220px;">
            <label class="field-label">Ganti Foto Produk (Galeri atau Kamera)</label>
            <input type="file" id="edit-gambar" accept="image/*" capture="environment" style="margin-bottom: 0;" onchange="previewFile(this, 'edit-preview')">
          </div>
        </div>

        <button type="submit" class="btn-add-cart">Simpan Perubahan</button>
      </form>
    </div>
  </div>

  <!-- MODAL RINCIAN KEUANGAN -->
  <div id="modal-detail-keuangan" class="modal-backdrop hidden">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="detail-modal-title">Rincian</h3>
        <button class="modal-close" onclick="closeDetailModal()">&times;</button>
      </div>
      <div id="detail-modal-body"></div>
    </div>
  </div>

  <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const DEFAULT_IMG = @json(asset('assets/logo-icon.jpg'));
    const TOBACCO_CATS = ['tembako olahan', 'original', 'racik'];
    let db = { produk: [], absensi: [], penjualan: [], pembelian: [], kasir_code: '', owner_email: '' };
    let currentUser = @json($currentUser);
    let financialChartInstance = null;

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

    function formatAngka(n) {
      return n !== null && n !== undefined && n !== '' ? Number(n).toLocaleString('id-ID') : '';
    }

    function parseAngka(val) {
      return Number(String(val || '').replace(/\D/g, '')) || 0;
    }

    function formatRupiah(n) {
      const num = Number(n) || 0;
      const prefix = num < 0 ? '-Rp ' : 'Rp ';
      return prefix + Math.abs(num).toLocaleString('id-ID');
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

    function previewFile(input, previewId) {
      const file = input.files && input.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById(previewId).src = e.target.result;
      };
      reader.readAsDataURL(file);
    }

    function syncUnitByCategory(prefix) {
      const kat = document.getElementById(`${prefix}-kategori`).value;
      const satuanEl = document.getElementById(`${prefix}-satuan`);
      const stokLabel = document.getElementById(`${prefix}-stok-label`);
      const beliLabel = document.getElementById(`${prefix}-beli-label`);
      const jualLabel = document.getElementById(`${prefix}-jual-label`);
      const halfWrap = document.getElementById(`${prefix}-half-ons-wrap`);

      if (isTobacco(kat)) {
        satuanEl.value = 'gram';
        satuanEl.disabled = true;
        stokLabel.innerText = prefix === 'prod' ? 'Jumlah Stok Beli (gram)' : 'Stok (gram)';
        beliLabel.innerText = 'Harga Modal (Per Ons / 100g)';
        jualLabel.innerText = 'Harga Jual 1 Ons (100g)';
        halfWrap.classList.remove('hidden');
      } else {
        satuanEl.disabled = false;
        stokLabel.innerText = prefix === 'prod' ? 'Jumlah Stok Beli' : 'Stok';
        beliLabel.innerText = 'Harga Modal (Per Satuan)';
        jualLabel.innerText = 'Harga Jual (Per Satuan)';
        halfWrap.classList.add('hidden');
      }
    }

    function autoFillHalfOns(prefix) {
      const jual = parseAngka(document.getElementById(`${prefix}-hargajual`).value);
      const halfInput = document.getElementById(`${prefix}-hargasetengah`);
      if (jual > 0 && !halfInput.dataset.touched) {
        halfInput.value = formatAngka(Math.round(jual / 2));
      }
    }

    async function apiJson(url, options = {}) {
      const headers = {
        'Accept': 'application/json',
        ...(options.body && !(options.body instanceof FormData)
          ? { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
          : { 'X-CSRF-TOKEN': CSRF_TOKEN }),
        ...(options.headers || {}),
      };
      const res = await fetch(url, { ...options, headers });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.message || 'Terjadi kendala, silakan coba lagi.');
      return data;
    }

    async function handleOwnerLogin(e) {
      e.preventDefault();
      try {
        const res = await apiJson('/api/pos/login', {
          method: 'POST',
          body: JSON.stringify({
            username: document.getElementById('owner-username').value,
            password: document.getElementById('owner-password').value,
            role: 'owner',
          }),
        });
        currentUser = res.user;
        document.getElementById('owner-password').value = '';
        document.getElementById('owner-login-card').classList.add('hidden');
        document.getElementById('owner-dashboard').classList.remove('hidden');
        document.getElementById('btn-owner-logout').classList.remove('hidden');
        await loadOwnerData();
      } catch (err) {
        alert(err.message);
      }
    }

    async function logoutOwner() {
      try {
        await apiJson('/api/pos/logout', { method: 'POST', body: JSON.stringify({}) });
      } catch (e) {}
      currentUser = null;
      document.getElementById('owner-dashboard').classList.add('hidden');
      document.getElementById('btn-owner-logout').classList.add('hidden');
      document.getElementById('owner-login-card').classList.remove('hidden');
    }

    function openForgotModal() {
      document.getElementById('forgot-step-1').classList.remove('hidden');
      document.getElementById('forgot-step-2').classList.add('hidden');
      document.getElementById('modal-forgot-password').classList.remove('hidden');
    }

    function closeForgotModal() {
      document.getElementById('modal-forgot-password').classList.add('hidden');
    }

    async function kirimKodeReset(e) {
      e.preventDefault();
      try {
        const res = await apiJson('/api/pos/owner/forgot-password', {
          method: 'POST',
          body: JSON.stringify({ email: document.getElementById('forgot-email').value }),
        });
        document.getElementById('forgot-info-msg').innerText = res.message;
        document.getElementById('forgot-step-1').classList.add('hidden');
        document.getElementById('forgot-step-2').classList.remove('hidden');
      } catch (err) {
        alert(err.message);
      }
    }

    async function simpanPasswordBaru(e) {
      e.preventDefault();
      try {
        const res = await apiJson('/api/pos/owner/reset-password', {
          method: 'POST',
          body: JSON.stringify({
            email: document.getElementById('forgot-email').value,
            code: document.getElementById('reset-code').value,
            password: document.getElementById('reset-new-password').value,
          }),
        });
        alert(res.message);
        closeForgotModal();
      } catch (err) {
        alert(err.message);
      }
    }

    function switchOwnerTab(tab, btnEl) {
      document.querySelectorAll('#owner-dashboard .filter-btn').forEach(b => b.classList.remove('active'));
      if (btnEl) btnEl.classList.add('active');
      document.getElementById('owner-tab-produk').classList.toggle('hidden', tab !== 'produk');
      document.getElementById('owner-tab-penjualan').classList.toggle('hidden', tab !== 'penjualan');
      document.getElementById('owner-tab-riwayat').classList.toggle('hidden', tab !== 'riwayat');
      document.getElementById('owner-tab-kodekasir').classList.toggle('hidden', tab !== 'kodekasir');
    }

    function toJakartaDateString(isoStr) {
      if (!isoStr) return '';
      const d = new Date(isoStr);
      return d.toLocaleDateString('en-CA', { timeZone: 'Asia/Jakarta' });
    }

    function getFilteredData() {
      const start = document.getElementById('filter-start-date').value;
      const end = document.getElementById('filter-end-date').value;

      const filterByDate = (list) => list.filter(item => {
        const dStr = toJakartaDateString(item.created_at);
        if (!dStr) return true;
        if (start && dStr < start) return false;
        if (end && dStr > end) return false;
        return true;
      });

      return {
        penjualan: filterByDate(db.penjualan),
        pembelian: filterByDate(db.pembelian),
      };
    }

    function applyDateFilter() {
      const start = document.getElementById('filter-start-date').value;
      const end = document.getElementById('filter-end-date').value;
      const params = new URLSearchParams();
      if (start) params.set('start_date', start);
      if (end) params.set('end_date', end);
      const qs = params.toString();
      document.getElementById('btn-export-excel').href = `{{ route('owner.export') }}${qs ? '?' + qs : ''}`;
      renderOwnerDashboard();
    }

    function resetDateFilter() {
      document.getElementById('filter-start-date').value = '';
      document.getElementById('filter-end-date').value = '';
      applyDateFilter();
    }

    async function loadOwnerData() {
      try {
        db = await apiJson('/api/pos/all');
        if (db.kasir_code) {
          document.getElementById('setting-kasir-code').value = db.kasir_code;
        }
        if (db.owner_email) {
          document.getElementById('setting-owner-email').value = db.owner_email;
        }
        renderOwnerDashboard();
      } catch (e) {
        console.error(e);
      }
    }

    function renderOwnerProdukTable() {
      const q = (document.getElementById('owner-prod-search').value || '').trim().toLowerCase();
      const list = q
        ? db.produk.filter(p => p.nama.toLowerCase().includes(q) || p.kategori.toLowerCase().includes(q))
        : db.produk;

      document.getElementById('table-owner-produk').innerHTML = list.map(p => {
        const tobacco = isTobacco(p.kategori);
        const unitNote = tobacco ? ' / ons' : ` / ${p.satuan}`;
        const halfText = tobacco ? `<br><small style="color:var(--text-muted)">1/2 ons: ${formatRupiah(p.harga_setengah_ons ?? Math.round(p.harga_jual / 2))}</small>` : '';
        return `
          <tr>
            <td><img src="${imgSrc(p.gambar)}" style="width:44px; height:44px; border-radius:6px; object-fit:cover;" onerror="this.src='${DEFAULT_IMG}'"></td>
            <td><b>${p.nama}</b></td>
            <td>${p.kategori}</td>
            <td><b>${p.stok}</b> ${p.satuan}</td>
            <td>${formatRupiah(p.harga_beli)}${unitNote}</td>
            <td><b>${formatRupiah(p.harga_jual)}${unitNote}</b>${halfText}</td>
            <td style="white-space: nowrap;">
              <button class="btn-restock" onclick="openRestockModal(${p.id})"><i class="fa-solid fa-plus"></i> Kulakan</button>
              <button class="btn-edit" onclick="openEditModal(${p.id})"><i class="fa-solid fa-pen"></i> Edit</button>
              <button class="btn-del" onclick="hapusProduk(${p.id}, '${p.nama.replace(/'/g, "\\'")}')"><i class="fa-solid fa-trash"></i> Hapus</button>
            </td>
          </tr>
        `;
      }).join('') || '<tr><td colspan="7" style="text-align:center; color:var(--text-muted);">Barang yang dicari tidak ditemukan.</td></tr>';
    }

    function renderOwnerDashboard() {
      const filtered = getFilteredData();
      const omset = filtered.penjualan.reduce((a, t) => a + Number(t.total), 0);
      const kulakan = filtered.pembelian.reduce((a, b) => a + Number(b.total_harga ?? (b.harga_beli * b.jumlah)), 0);
      const profit = omset - kulakan;

      document.getElementById('stat-omset').innerText = formatRupiah(omset);
      document.getElementById('stat-beli').innerText = formatRupiah(kulakan);
      document.getElementById('stat-profit').innerText = formatRupiah(profit);

      renderFinancialChart(omset, kulakan, profit);
      renderOwnerProdukTable();

      document.getElementById('table-owner-penjualan').innerHTML = filtered.penjualan.map(t => {
        const itemsText = (t.items || []).map(i => `${i.nama} (${i.qty} x ${i.satuan})`).join(', ');
        return `
          <tr>
            <td><b>${t.kode_transaksi}</b></td>
            <td>${t.waktu}</td>
            <td>${t.kasir}</td>
            <td>${itemsText}</td>
            <td><b>${formatRupiah(t.total)}</b></td>
            <td>${formatRupiah(t.bayar)}</td>
            <td>${formatRupiah(t.kembali)}</td>
          </tr>
        `;
      }).join('') || '<tr><td colspan="7" style="text-align:center; color:var(--text-muted);">Belum ada data penjualan pada rentang tanggal ini.</td></tr>';

      document.getElementById('table-owner-kulakan').innerHTML = filtered.pembelian.map(b => {
        const unitNote = b.produk && isTobacco(b.produk.kategori) ? ' / ons' : '';
        const tgl = toJakartaDateString(b.created_at) || '-';
        return `
          <tr>
            <td>${tgl}</td>
            <td><b>${b.produk ? b.produk.nama : 'Produk #' + b.produk_id}</b></td>
            <td>${b.jumlah} ${b.produk ? b.produk.satuan : ''}</td>
            <td>${formatRupiah(b.harga_beli)}${unitNote}</td>
            <td><b>${formatRupiah(b.total_harga ?? (b.harga_beli * b.jumlah))}</b></td>
            <td>${b.catatan || '-'}</td>
          </tr>
        `;
      }).join('') || '<tr><td colspan="6" style="text-align:center; color:var(--text-muted);">Belum ada data kulakan pada rentang tanggal ini.</td></tr>';

      document.getElementById('table-owner-absensi').innerHTML = db.absensi.map(a => {
        let locHtml = '<span style="color:var(--text-muted);">Lokasi tidak tersedia</span>';
        if (a.latitude && a.longitude) {
          const accText = a.akurasi ? `Akurasi ±${a.akurasi} meter` : 'Lokasi aktif';
          locHtml = `${accText}<br><button type="button" class="btn-map" onclick="openMapModal('${a.karyawan.replace(/'/g, "\\'")}', ${a.latitude}, ${a.longitude}, ${a.akurasi ?? 'null'})"><i class="fa-solid fa-map-location-dot"></i> Lihat Peta</button>`;
        }
        return `
          <tr>
            <td>${a.waktu}</td>
            <td><b>${a.karyawan}</b></td>
            <td>${a.shift}</td>
            <td>${a.status}</td>
            <td>${locHtml}</td>
            <td>${a.catatan || '-'}</td>
          </tr>
        `;
      }).join('') || '<tr><td colspan="6" style="text-align:center; color:var(--text-muted);">Belum ada data absensi.</td></tr>';
    }

    function openMapModal(nama, lat, lng, akurasi) {
      document.getElementById('map-modal-title').innerHTML = `<i class="fa-solid fa-map-location-dot"></i> Lokasi Absen: ${nama}`;
      document.getElementById('map-modal-info').innerText = `Titik lokasi: ${lat}, ${lng}` + (akurasi ? ` (Akurasi ±${akurasi} meter)` : '');
      document.getElementById('map-modal-iframe').src = `https://maps.google.com/maps?q=${lat},${lng}&z=16&output=embed`;
      document.getElementById('map-modal-external').href = `https://maps.google.com/?q=${lat},${lng}`;
      document.getElementById('modal-peta-lokasi').classList.remove('hidden');
    }

    function closeMapModal() {
      document.getElementById('modal-peta-lokasi').classList.add('hidden');
      document.getElementById('map-modal-iframe').src = '';
    }

    function renderFinancialChart(omset, kulakan, profit) {
      const ctx = document.getElementById('financialChart').getContext('2d');
      if (financialChartInstance) financialChartInstance.destroy();

      const actualValues = [omset, kulakan, profit];
      const plotValues = [omset, kulakan, Math.max(0, profit)];

      financialChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Total Omset', 'Total Kulakan', 'Perkiraan Keuntungan'],
          datasets: [{
            data: plotValues,
            backgroundColor: ['#c6a052', '#e74c3c', profit >= 0 ? '#2ecc71' : '#e67e22'],
            borderRadius: 6,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: (ctx) => ' ' + formatRupiah(actualValues[ctx.dataIndex])
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              min: 0,
              ticks: {
                color: '#a0a0a0',
                callback: (val) => formatRupiah(val)
              },
              grid: { color: 'rgba(255,255,255,0.06)' }
            },
            x: {
              ticks: { color: '#f0f0f0' },
              grid: { display: false }
            }
          }
        }
      });
    }

    async function simpanProduk(e) {
      e.preventDefault();
      const formData = new FormData();
      const kat = document.getElementById('prod-kategori').value;
      formData.append('nama', document.getElementById('prod-nama').value);
      formData.append('kategori', kat);
      formData.append('satuan', isTobacco(kat) ? 'gram' : document.getElementById('prod-satuan').value);
      formData.append('stok', document.getElementById('prod-stok').value);
      formData.append('harga_beli', parseAngka(document.getElementById('prod-hargabeli').value));
      formData.append('harga_jual', parseAngka(document.getElementById('prod-hargajual').value));
      const halfVal = parseAngka(document.getElementById('prod-hargasetengah').value);
      if (isTobacco(kat) && document.getElementById('prod-hargasetengah').value !== '') {
        formData.append('harga_setengah_ons', halfVal);
      }
      const fileInput = document.getElementById('prod-gambar');
      if (fileInput.files && fileInput.files[0]) {
        formData.append('gambar', fileInput.files[0]);
      }

      try {
        await apiJson('/api/pos/produk', { method: 'POST', body: formData });
        alert('Barang baru berhasil disimpan!');
        e.target.reset();
        delete document.getElementById('prod-hargasetengah').dataset.touched;
        document.getElementById('add-preview').src = DEFAULT_IMG;
        syncUnitByCategory('prod');
        await loadOwnerData();
      } catch (err) {
        alert(err.message);
      }
    }

    function openRestockModal(id) {
      const p = db.produk.find(item => item.id == id);
      if (!p) return;
      const tobacco = isTobacco(p.kategori);
      document.getElementById('restock-id').value = p.id;
      document.getElementById('restock-nama').innerText = `${p.nama} (Stok saat ini: ${p.stok} ${p.satuan})`;
      document.getElementById('restock-jumlah-label').innerText = `Jumlah Tambah Stok (${p.satuan})`;
      document.getElementById('restock-harga-label').innerText = tobacco ? 'Harga Modal (Per Ons / 100g)' : `Harga Modal (Per ${p.satuan})`;
      document.getElementById('restock-jumlah').value = '';
      document.getElementById('restock-harga').value = formatAngka(p.harga_beli);
      document.getElementById('restock-catatan').value = '';
      document.getElementById('modal-restock').classList.remove('hidden');
    }

    function closeRestockModal() {
      document.getElementById('modal-restock').classList.add('hidden');
    }

    async function submitRestock(e) {
      e.preventDefault();
      const id = document.getElementById('restock-id').value;
      try {
        await apiJson(`/api/pos/produk/${id}/restock`, {
          method: 'POST',
          body: JSON.stringify({
            jumlah: Number(document.getElementById('restock-jumlah').value),
            harga_beli: parseAngka(document.getElementById('restock-harga').value),
            catatan: document.getElementById('restock-catatan').value,
          }),
        });
        closeRestockModal();
        alert('Stok kulakan berhasil ditambahkan!');
        await loadOwnerData();
      } catch (err) {
        alert(err.message);
      }
    }

    function openEditModal(id) {
      const p = db.produk.find(item => item.id == id);
      if (!p) return;
      document.getElementById('edit-id').value = p.id;
      document.getElementById('edit-nama').value = p.nama;
      document.getElementById('edit-kategori').value = p.kategori;
      document.getElementById('edit-satuan').value = p.satuan;
      document.getElementById('edit-stok').value = p.stok;
      document.getElementById('edit-hargabeli').value = formatAngka(p.harga_beli);
      document.getElementById('edit-hargajual').value = formatAngka(p.harga_jual);
      document.getElementById('edit-hargasetengah').value = formatAngka(p.harga_setengah_ons ?? Math.round(p.harga_jual / 2));
      document.getElementById('edit-gambar').value = '';
      document.getElementById('edit-preview').src = imgSrc(p.gambar);
      syncUnitByCategory('edit');
      document.getElementById('modal-edit-produk').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modal-edit-produk').classList.add('hidden');
    }

    async function submitEditProduk(e) {
      e.preventDefault();
      const id = document.getElementById('edit-id').value;
      const kat = document.getElementById('edit-kategori').value;
      const formData = new FormData();
      formData.append('nama', document.getElementById('edit-nama').value);
      formData.append('kategori', kat);
      formData.append('satuan', isTobacco(kat) ? 'gram' : document.getElementById('edit-satuan').value);
      formData.append('stok', document.getElementById('edit-stok').value);
      formData.append('harga_beli', parseAngka(document.getElementById('edit-hargabeli').value));
      formData.append('harga_jual', parseAngka(document.getElementById('edit-hargajual').value));
      if (isTobacco(kat) && document.getElementById('edit-hargasetengah').value !== '') {
        formData.append('harga_setengah_ons', parseAngka(document.getElementById('edit-hargasetengah').value));
      }
      const fileInput = document.getElementById('edit-gambar');
      if (fileInput.files && fileInput.files[0]) {
        formData.append('gambar', fileInput.files[0]);
      }

      try {
        await apiJson(`/api/pos/produk/${id}`, { method: 'POST', body: formData });
        closeEditModal();
        alert('Data produk berhasil diperbarui!');
        await loadOwnerData();
      } catch (err) {
        alert(err.message);
      }
    }

    async function hapusProduk(id, nama) {
      if (!confirm(`Yakin ingin menghapus "${nama}"?`)) return;
      try {
        await apiJson(`/api/pos/produk/${id}`, { method: 'DELETE' });
        alert('Produk berhasil dihapus!');
        await loadOwnerData();
      } catch (err) {
        alert(err.message);
      }
    }

    async function simpanKodeKasir(e) {
      e.preventDefault();
      const code = document.getElementById('setting-kasir-code').value;
      try {
        await apiJson('/api/pos/kasir-code', {
          method: 'POST',
          body: JSON.stringify({ kasir_code: code }),
        });
        alert('Kode masuk kasir berhasil diperbarui!');
      } catch (err) {
        alert(err.message);
      }
    }

    async function simpanAkunOwner(e) {
      e.preventDefault();
      try {
        const res = await apiJson('/api/pos/owner/account', {
          method: 'POST',
          body: JSON.stringify({
            email: document.getElementById('setting-owner-email').value,
            password: document.getElementById('setting-owner-password').value || null,
          }),
        });
        document.getElementById('setting-owner-password').value = '';
        alert(res.message);
      } catch (err) {
        alert(err.message);
      }
    }

    function openDetailModal(type) {
      const filtered = getFilteredData();
      const titleEl = document.getElementById('detail-modal-title');
      const bodyEl = document.getElementById('detail-modal-body');

      if (type === 'omset') {
        titleEl.innerText = 'Rincian Total Omset Penjualan';
        const rows = filtered.penjualan.map(t => `
          <tr>
            <td>${t.kode_transaksi}</td>
            <td>${t.waktu}</td>
            <td>${t.kasir}</td>
            <td><b>${formatRupiah(t.total)}</b></td>
          </tr>
        `).join('') || '<tr><td colspan="4" style="text-align:center;">Belum ada transaksi penjualan.</td></tr>';
        bodyEl.innerHTML = `<table><thead><tr><th>Nomor Nota</th><th>Waktu</th><th>Kasir</th><th>Total</th></tr></thead><tbody>${rows}</tbody></table>`;
      } else if (type === 'kulakan') {
        titleEl.innerText = 'Rincian Total Pengeluaran Kulakan';
        const rows = filtered.pembelian.map(b => `
          <tr>
            <td>${b.produk ? b.produk.nama : 'Produk #' + b.produk_id}</td>
            <td>${b.jumlah} ${b.produk ? b.produk.satuan : ''}</td>
            <td>${formatRupiah(b.harga_beli)}</td>
            <td><b>${formatRupiah(b.total_harga ?? (b.harga_beli * b.jumlah))}</b></td>
          </tr>
        `).join('') || '<tr><td colspan="4" style="text-align:center;">Belum ada catatan kulakan.</td></tr>';
        bodyEl.innerHTML = `<table><thead><tr><th>Barang</th><th>Jumlah</th><th>Modal</th><th>Total</th></tr></thead><tbody>${rows}</tbody></table>`;
      } else {
        const omset = filtered.penjualan.reduce((a, t) => a + Number(t.total), 0);
        const kulakan = filtered.pembelian.reduce((a, b) => a + Number(b.total_harga ?? (b.harga_beli * b.jumlah)), 0);
        const profit = omset - kulakan;
        titleEl.innerText = 'Rincian Perkiraan Keuntungan Bersih';
        bodyEl.innerHTML = `
          <table>
            <tbody>
              <tr><td>Total Pemasukan Penjualan (Omset)</td><td><b>${formatRupiah(omset)}</b></td></tr>
              <tr><td>Total Pengeluaran Kulakan Barang</td><td><b>${formatRupiah(kulakan)}</b></td></tr>
              <tr><td><b>Selisih (Keuntungan Bersih)</b></td><td><b style="color:${profit >= 0 ? 'var(--success)' : 'var(--danger)'}">${formatRupiah(profit)}</b></td></tr>
            </tbody>
          </table>
        `;
      }

      document.getElementById('modal-detail-keuangan').classList.remove('hidden');
    }

    function closeDetailModal() {
      document.getElementById('modal-detail-keuangan').classList.add('hidden');
    }

    syncUnitByCategory('prod');
    if (currentUser && currentUser.role === 'owner') {
      loadOwnerData();
    }
  </script>
</body>
</html>
