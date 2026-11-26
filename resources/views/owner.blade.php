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
    .stat-card:hover {
      transform: translateY(-3px);
      border-color: var(--primary-accent);
    }
    .stat-card h4 { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; }
    .stat-card p { font-size: 1.4rem; font-weight: bold; color: #fff; margin-top: 5px; }
    .stat-card small { display: block; margin-top: 6px; font-size: 0.75rem; color: var(--primary-accent); }

    input, select, textarea {
      background: #222;
      border: 1px solid var(--card-border);
      color: #fff;
      padding: 10px;
      border-radius: 8px;
      width: 100%;
      margin-bottom: 12px;
    }

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

    .btn-excel {
      background: #1d6f42;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 0.88rem;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-excel:hover { background: #238850; }

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

    /* MODAL */
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

    <!-- FORM MASUK OWNER -->
    <div id="owner-login-card" class="card {{ ($currentUser['role'] ?? '') === 'owner' ? 'hidden' : '' }}" style="max-width: 420px; margin: 40px auto;">
      <h2><i class="fa-solid fa-lock"></i> Masuk Halaman Pemilik</h2>
      <form onsubmit="handleOwnerLogin(event)">
        <label style="font-size: 0.85rem; color: var(--text-muted);">Nama Pemilik</label>
        <input type="text" id="owner-username" placeholder="Masukkan nama Anda..." required>
        <label style="font-size: 0.85rem; color: var(--text-muted);">PIN Pemilik</label>
        <div class="pin-wrapper">
          <input type="password" id="owner-password" placeholder="Masukkan PIN pemilik..." required>
          <button type="button" class="pin-toggle-btn" onclick="togglePin('owner-password', this)" title="Lihat / Sembunyikan PIN">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>
        <button type="submit" class="btn-add-cart" style="margin-top: 10px;">Masuk Sekarang</button>
      </form>
      <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 15px; text-align: center;">
        PIN Pemilik: <b>owner123</b>
      </p>
    </div>

    <!-- DASHBOARD PEMILIK -->
    <div id="owner-dashboard" class="{{ ($currentUser['role'] ?? '') === 'owner' ? '' : 'hidden' }}">

      <!-- KARTU RINGKASAN (BISA DIKLIK UNTUK DETAIL) -->
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

      <!-- DIAGRAM KEUANGAN -->
      <div class="card">
        <h2>
          <span><i class="fa-solid fa-chart-column"></i> Diagram Ringkasan Keuangan Toko</span>
          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('owner.export', 'penjualan') }}" class="btn-excel"><i class="fa-solid fa-file-excel"></i> Unduh Penjualan</a>
            <a href="{{ route('owner.export', 'kulakan') }}" class="btn-excel"><i class="fa-solid fa-file-excel"></i> Unduh Kulakan</a>
            <a href="{{ route('owner.export', 'omset') }}" class="btn-excel"><i class="fa-solid fa-file-excel"></i> Unduh Omset</a>
          </div>
        </h2>
        <div style="height: 300px; position: relative;">
          <canvas id="financialChart"></canvas>
        </div>
      </div>

      <!-- TAB MENU OWNER -->
      <div class="filter-bar">
        <button class="filter-btn active" onclick="switchOwnerTab('produk', this)"><i class="fa-solid fa-boxes-stacked"></i> Kelola Produk &amp; Kulakan</button>
        <button class="filter-btn" onclick="switchOwnerTab('penjualan', this)"><i class="fa-solid fa-receipt"></i> Laporan Penjualan</button>
        <button class="filter-btn" onclick="switchOwnerTab('riwayat', this)"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Kulakan &amp; Absensi</button>
      </div>

      <!-- TAB 1: PRODUK & KULAKAN -->
      <div id="owner-tab-produk">
        <div class="card">
          <h2><i class="fa-solid fa-plus-circle"></i> Tambah &amp; Isi Ulang Stok Barang</h2>
          <form onsubmit="simpanProduk(event)">
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
              <input type="text" id="prod-nama" placeholder="Nama Produk Tembakau / Perlengkapan" required style="flex: 2; min-width: 220px;">
              <select id="prod-kategori" style="flex: 1; min-width: 160px;">
                <option value="tembako olahan">Tembako Olahan</option>
                <option value="original">Original</option>
                <option value="racik">Racik</option>
                <option value="papir">Papir</option>
                <option value="cengkeh">Cengkeh</option>
                <option value="aksesoris">Aksesoris</option>
                <option value="alat">Alat</option>
              </select>
              <select id="prod-satuan" style="flex: 1; min-width: 120px;">
                <option value="gram">gram</option>
                <option value="pics">pics</option>
                <option value="pack">pack</option>
              </select>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
              <input type="number" id="prod-stok" placeholder="Jumlah Stok Beli" required style="flex: 1; min-width: 140px;">
              <input type="number" id="prod-hargabeli" placeholder="Harga Modal (Per Satuan)" required style="flex: 1; min-width: 160px;">
              <input type="number" id="prod-hargajual" placeholder="Harga Jual Toko (Per Satuan)" required style="flex: 1; min-width: 160px;">
            </div>
            <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-bottom: 12px;">
              <img id="add-preview" src="{{ asset('assets/logo-icon.jpg') }}" alt="Pratinjau Gambar" class="upload-preview">
              <div style="flex: 1; min-width: 220px;">
                <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 4px;">
                  Foto Produk (Pilih dari Galeri atau Ambil Foto Langsung)
                </label>
                <input type="file" id="prod-gambar" accept="image/*" capture="environment" style="margin-bottom: 0;" onchange="previewFile(this, 'add-preview')">
              </div>
            </div>
            <button type="submit" class="btn-add-cart">Simpan Barang &amp; Catat Kulakan</button>
          </form>
        </div>

        <div class="card">
          <h2><i class="fa-solid fa-list"></i> Daftar Produk Toko</h2>
          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Gambar</th>
                  <th>Nama Barang</th>
                  <th>Kategori</th>
                  <th>Satuan</th>
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
        <h2>
          <span><i class="fa-solid fa-receipt"></i> Laporan Penjualan Toko</span>
          <a href="{{ route('owner.export', 'penjualan') }}" class="btn-excel"><i class="fa-solid fa-file-excel"></i> Unduh Excel</a>
        </h2>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Kode</th>
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
          <h2>
            <span><i class="fa-solid fa-truck-ramp-box"></i> Riwayat Kulakan Barang</span>
            <a href="{{ route('owner.export', 'kulakan') }}" class="btn-excel"><i class="fa-solid fa-file-excel"></i> Unduh Excel</a>
          </h2>
          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Nama Barang</th>
                  <th>Jumlah</th>
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
          <h2><i class="fa-solid fa-clipboard-user"></i> Riwayat Absensi Staf Kasir</h2>
          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Waktu</th>
                  <th>Nama Kasir</th>
                  <th>Jam Kerja / Shift</th>
                  <th>Status</th>
                  <th>Catatan</th>
                </tr>
              </thead>
              <tbody id="table-owner-absensi"></tbody>
            </table>
          </div>
        </div>
      </div>

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
        <label style="font-size: 0.85rem; color: var(--text-muted);">Nama Barang</label>
        <input type="text" id="edit-nama" required>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 160px;">
            <label style="font-size: 0.85rem; color: var(--text-muted);">Kategori</label>
            <select id="edit-kategori">
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
            <label style="font-size: 0.85rem; color: var(--text-muted);">Satuan</label>
            <select id="edit-satuan">
              <option value="gram">gram</option>
              <option value="pics">pics</option>
              <option value="pack">pack</option>
            </select>
          </div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 120px;">
            <label style="font-size: 0.85rem; color: var(--text-muted);">Stok</label>
            <input type="number" id="edit-stok" min="0" required>
          </div>
          <div style="flex: 1; min-width: 140px;">
            <label style="font-size: 0.85rem; color: var(--text-muted);">Harga Modal</label>
            <input type="number" id="edit-hargabeli" min="0" required>
          </div>
          <div style="flex: 1; min-width: 140px;">
            <label style="font-size: 0.85rem; color: var(--text-muted);">Harga Jual</label>
            <input type="number" id="edit-hargajual" min="0" required>
          </div>
        </div>

        <div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-bottom: 16px;">
          <img id="edit-preview" src="{{ asset('assets/logo-icon.jpg') }}" alt="Foto Produk" class="upload-preview">
          <div style="flex: 1; min-width: 220px;">
            <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 4px;">
              Ganti Foto Produk (Galeri atau Kamera)
            </label>
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
    let db = { produk: [], absensi: [], penjualan: [], pembelian: [] };
    let currentUser = @json($currentUser);
    let financialChartInstance = null;

    function imgSrc(path) {
      if (!path) return DEFAULT_IMG;
      if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) return path;
      return '/' + path;
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
      if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan pada server');
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

    function switchOwnerTab(tab, btnEl) {
      document.querySelectorAll('#owner-dashboard .filter-btn').forEach(b => b.classList.remove('active'));
      if (btnEl) btnEl.classList.add('active');
      document.getElementById('owner-tab-produk').classList.toggle('hidden', tab !== 'produk');
      document.getElementById('owner-tab-penjualan').classList.toggle('hidden', tab !== 'penjualan');
      document.getElementById('owner-tab-riwayat').classList.toggle('hidden', tab !== 'riwayat');
    }

    async function loadOwnerData() {
      try {
        db = await apiJson('/api/pos/all');
        renderOwnerDashboard();
      } catch (e) {
        console.error(e);
      }
    }

    function renderOwnerDashboard() {
      const omset = db.penjualan.reduce((a, t) => a + Number(t.total), 0);
      const kulakan = db.pembelian.reduce((a, b) => a + Number(b.total_harga ?? (b.harga_beli * b.jumlah)), 0);
      const profit = omset - kulakan;

      document.getElementById('stat-omset').innerText = formatRupiah(omset);
      document.getElementById('stat-beli').innerText = formatRupiah(kulakan);
      document.getElementById('stat-profit').innerText = formatRupiah(profit);

      renderFinancialChart(omset, kulakan, profit);

      document.getElementById('table-owner-produk').innerHTML = db.produk.map(p => `
        <tr>
          <td><img src="${imgSrc(p.gambar)}" style="width:44px; height:44px; border-radius:6px; object-fit:cover;" onerror="this.src='${DEFAULT_IMG}'"></td>
          <td><b>${p.nama}</b></td>
          <td>${p.kategori}</td>
          <td>${p.satuan}</td>
          <td>${p.stok}</td>
          <td>${formatRupiah(p.harga_beli)}</td>
          <td><b>${formatRupiah(p.harga_jual)}</b></td>
          <td style="white-space: nowrap;">
            <button class="btn-edit" onclick="openEditModal(${p.id})"><i class="fa-solid fa-pen"></i> Edit</button>
            <button class="btn-del" onclick="hapusProduk(${p.id}, '${p.nama.replace(/'/g, "\\'")}')"><i class="fa-solid fa-trash"></i> Hapus</button>
          </td>
        </tr>
      `).join('');

      document.getElementById('table-owner-penjualan').innerHTML = db.penjualan.map(t => {
        const itemsText = (t.items || []).map(i => `${i.nama} (${i.qty} ${i.satuan})`).join(', ');
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
      }).join('') || '<tr><td colspan="7" style="text-align:center; color:var(--text-muted);">Belum ada data penjualan.</td></tr>';

      document.getElementById('table-owner-kulakan').innerHTML = db.pembelian.map(b => `
        <tr>
          <td><b>${b.produk ? b.produk.nama : 'Produk #' + b.produk_id}</b></td>
          <td>${b.jumlah} ${b.produk ? b.produk.satuan : ''}</td>
          <td>${formatRupiah(b.harga_beli)}</td>
          <td><b>${formatRupiah(b.total_harga ?? (b.harga_beli * b.jumlah))}</b></td>
          <td>${b.catatan || '-'}</td>
        </tr>
      `).join('') || '<tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Belum ada data kulakan.</td></tr>';

      document.getElementById('table-owner-absensi').innerHTML = db.absensi.map(a => `
        <tr>
          <td>${a.waktu}</td>
          <td><b>${a.karyawan}</b></td>
          <td>${a.shift}</td>
          <td>${a.status}</td>
          <td>${a.catatan || '-'}</td>
        </tr>
      `).join('') || '<tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Belum ada data absensi.</td></tr>';
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
      formData.append('nama', document.getElementById('prod-nama').value);
      formData.append('kategori', document.getElementById('prod-kategori').value);
      formData.append('satuan', document.getElementById('prod-satuan').value);
      formData.append('stok', document.getElementById('prod-stok').value);
      formData.append('harga_beli', document.getElementById('prod-hargabeli').value);
      formData.append('harga_jual', document.getElementById('prod-hargajual').value);
      const fileInput = document.getElementById('prod-gambar');
      if (fileInput.files && fileInput.files[0]) {
        formData.append('gambar', fileInput.files[0]);
      }

      try {
        await apiJson('/api/pos/produk', { method: 'POST', body: formData });
        alert('Barang baru berhasil disimpan!');
        e.target.reset();
        document.getElementById('add-preview').src = DEFAULT_IMG;
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
      document.getElementById('edit-hargabeli').value = p.harga_beli;
      document.getElementById('edit-hargajual').value = p.harga_jual;
      document.getElementById('edit-gambar').value = '';
      document.getElementById('edit-preview').src = imgSrc(p.gambar);
      document.getElementById('modal-edit-produk').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modal-edit-produk').classList.add('hidden');
    }

    async function submitEditProduk(e) {
      e.preventDefault();
      const id = document.getElementById('edit-id').value;
      const formData = new FormData();
      formData.append('nama', document.getElementById('edit-nama').value);
      formData.append('kategori', document.getElementById('edit-kategori').value);
      formData.append('satuan', document.getElementById('edit-satuan').value);
      formData.append('stok', document.getElementById('edit-stok').value);
      formData.append('harga_beli', document.getElementById('edit-hargabeli').value);
      formData.append('harga_jual', document.getElementById('edit-hargajual').value);
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

    function openDetailModal(type) {
      const titleEl = document.getElementById('detail-modal-title');
      const bodyEl = document.getElementById('detail-modal-body');

      if (type === 'omset') {
        titleEl.innerText = 'Rincian Total Omset Penjualan';
        const rows = db.penjualan.map(t => `
          <tr>
            <td>${t.kode_transaksi}</td>
            <td>${t.waktu}</td>
            <td>${t.kasir}</td>
            <td><b>${formatRupiah(t.total)}</b></td>
          </tr>
        `).join('') || '<tr><td colspan="4" style="text-align:center;">Belum ada transaksi penjualan.</td></tr>';
        bodyEl.innerHTML = `<table><thead><tr><th>Kode</th><th>Waktu</th><th>Kasir</th><th>Total</th></tr></thead><tbody>${rows}</tbody></table>`;
      } else if (type === 'kulakan') {
        titleEl.innerText = 'Rincian Total Pengeluaran Kulakan';
        const rows = db.pembelian.map(b => `
          <tr>
            <td>${b.produk ? b.produk.nama : 'Produk #' + b.produk_id}</td>
            <td>${b.jumlah} ${b.produk ? b.produk.satuan : ''}</td>
            <td>${formatRupiah(b.harga_beli)}</td>
            <td><b>${formatRupiah(b.total_harga ?? (b.harga_beli * b.jumlah))}</b></td>
          </tr>
        `).join('') || '<tr><td colspan="4" style="text-align:center;">Belum ada catatan kulakan.</td></tr>';
        bodyEl.innerHTML = `<table><thead><tr><th>Barang</th><th>Jumlah</th><th>Modal Satuan</th><th>Total</th></tr></thead><tbody>${rows}</tbody></table>`;
      } else {
        const omset = db.penjualan.reduce((a, t) => a + Number(t.total), 0);
        const kulakan = db.pembelian.reduce((a, b) => a + Number(b.total_harga ?? (b.harga_beli * b.jumlah)), 0);
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
          <p style="margin-top:12px; font-size:0.85rem; color:var(--text-muted);">
            Catatan: Nilai dihitung dari total uang masuk penjualan dikurangi total modal kulakan barang yang sudah dicatat.
          </p>
        `;
      }

      document.getElementById('modal-detail-keuangan').classList.remove('hidden');
    }

    function closeDetailModal() {
      document.getElementById('modal-detail-keuangan').classList.add('hidden');
    }

    if (currentUser && currentUser.role === 'owner') {
      loadOwnerData();
    }
  </script>
</body>
</html>
