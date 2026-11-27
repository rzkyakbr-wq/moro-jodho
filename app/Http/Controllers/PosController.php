<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PosController extends Controller
{
    private const TOBACCO_CATEGORIES = ['tembako olahan', 'original', 'racik'];

    public function index(Request $request): View
    {
        $user = $request->session()->get('pos_user');
        if ($user && $user['role'] === 'owner') {
            $user = null;
        }

        return view('pos', [
            'currentUser' => $user,
        ]);
    }

    public function ownerIndex(Request $request): View
    {
        return view('owner', [
            'currentUser' => $request->session()->get('pos_user'),
        ]);
    }

    public function getAll(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        $payload = [
            'produk' => Produk::orderBy('id')->get(),
        ];

        if ($user && in_array($user['role'], ['owner', 'kasir'], true)) {
            $payload += [
                'absensi' => Absensi::latest()->limit(100)->get(),
                'penjualan' => Penjualan::latest()->limit(500)->get(),
                'pembelian' => Pembelian::with('produk:id,nama,satuan,kategori')->latest()->limit(500)->get(),
            ];
        }

        if ($user && $user['role'] === 'owner') {
            $owner = User::where('role', 'owner')->first();
            $payload['kasir_code'] = Setting::getValue('kasir_code', env('POS_KASIR_PIN', 'MJ-KASIR-2026'));
            $payload['owner_email'] = $owner?->email ?? '';
            $payload['qr_masuk_token'] = $this->qrTokenFor('masuk');
            $payload['qr_pulang_token'] = $this->qrTokenFor('pulang');
        }

        return response()->json($payload);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:kasir,owner'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'akurasi' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validated['role'] === 'owner') {
            $loginInput = trim($validated['username']);
            $owner = User::where('role', 'owner')
                ->where(function ($q) use ($loginInput) {
                    $q->where('email', $loginInput)->orWhere('username', $loginInput);
                })
                ->first();

            if (! $owner || ! Hash::check($validated['password'], $owner->password)) {
                return response()->json([
                    'message' => 'Email Gmail atau Kata Sandi Pemilik salah!',
                ], 401);
            }

            $user = [
                'username' => $owner->username,
                'email' => $owner->email,
                'name' => $owner->name,
                'role' => 'owner',
            ];
        } else {
            $kasirCode = Setting::getValue('kasir_code', env('POS_KASIR_PIN', 'MJ-KASIR-2026'));
            if ($validated['password'] !== $kasirCode) {
                return response()->json([
                    'message' => 'Kode Masuk Kasir salah!',
                ], 401);
            }

            $user = [
                'username' => trim($validated['username']),
                'role' => 'kasir',
            ];

            $nowJkt = now('Asia/Jakarta');
            $shiftLabel = $nowJkt->hour < 12 ? 'Shift 1 (09:00 - 12:00)' : 'Shift 2 (12:00 - 21:00)';
            Absensi::create([
                'karyawan' => $user['username'],
                'shift' => $shiftLabel,
                'tipe' => 'masuk',
                'is_lembur' => false,
                'status' => 'Hadir',
                'catatan' => 'Otomatis tercatat hadir saat masuk kasir',
                'waktu' => $nowJkt->format('d/m/Y, H.i.s'),
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'akurasi' => $validated['akurasi'] ?? null,
            ]);
        }

        $request->session()->put('pos_user', $user);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->session()->forget('pos_user');

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function updateKasirCode(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Khusus Pemilik Toko.'], 403);
        }

        $validated = $request->validate([
            'kasir_code' => ['required', 'string', 'min:4', 'max:50'],
        ]);

        Setting::setValue('kasir_code', trim($validated['kasir_code']));

        return response()->json([
            'message' => 'Kode masuk kasir berhasil diperbarui!',
            'kasir_code' => trim($validated['kasir_code']),
        ]);
    }

    public function requestOwnerReset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $owner = User::where('role', 'owner')
            ->where('email', trim($validated['email']))
            ->first();

        if (! $owner) {
            return response()->json([
                'message' => 'Email Gmail tersebut tidak terdaftar sebagai Pemilik Toko.',
            ], 404);
        }

        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $owner->email],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        try {
            Mail::raw(
                "Halo {$owner->name},\n\nKode pemulihan kata sandi Toko Moro Jodho Anda adalah: {$code}\n\nKode ini berlaku selama 30 menit. Jangan berikan kode ini kepada siapa pun.",
                function ($message) use ($owner) {
                    $message->to($owner->email)
                        ->subject('Kode Atur Ulang Kata Sandi - Toko Moro Jodho');
                }
            );
        } catch (\Throwable $e) {
            // If mail transport is not configured yet, still allow reset via log/configured mailer
        }

        $isLogMailer = config('mail.default') === 'log';

        return response()->json([
            'message' => $isLogMailer
                ? 'Kode pemulihan telah dibuat. Cek inbox Gmail Anda (atau file log aplikasi jika email belum diatur).'
                : 'Kode pemulihan telah dikirim ke Gmail Anda. Silakan cek kotak masuk atau folder spam.',
            'dev_hint' => app()->environment('testing') ? $code : null,
        ]);
    }

    public function resetOwnerPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', trim($validated['email']))->first();
        if (! $record || ! Hash::check($validated['code'], $record->token)) {
            return response()->json(['message' => 'Kode pemulihan tidak cocok atau sudah kadaluarsa.'], 422);
        }

        $owner = User::where('role', 'owner')->where('email', trim($validated['email']))->firstOrFail();
        $owner->password = Hash::make($validated['password']);
        $owner->save();

        DB::table('password_reset_tokens')->where('email', $owner->email)->delete();

        return response()->json(['message' => 'Kata sandi baru berhasil disimpan! Silakan masuk kembali.']);
    }

    public function updateOwnerAccount(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Khusus Pemilik Toko.'], 403);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $owner = User::where('role', 'owner')->firstOrFail();
        $owner->email = trim($validated['email']);
        if (! empty($validated['password'])) {
            $owner->password = Hash::make($validated['password']);
        }
        $owner->save();

        $user['email'] = $owner->email;
        $request->session()->put('pos_user', $user);

        return response()->json([
            'message' => 'Akun Gmail & kata sandi Pemilik berhasil diperbarui!',
            'email' => $owner->email,
        ]);
    }

    private function storeUploadedImage(Request $request): ?string
    {
        if (! $request->hasFile('gambar')) {
            return null;
        }

        $file = $request->file('gambar');
        $dir = public_path('uploads/produk');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return 'uploads/produk/' . $filename;
    }

    private function deleteLocalImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/produk/')) {
            $fullPath = public_path($path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }

    private function isTobacco(string $kategori): bool
    {
        return in_array(strtolower($kategori), self::TOBACCO_CATEGORIES, true);
    }

    public function addProduk(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Khusus Pemilik Toko.'], 403);
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:1'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'harga_jual' => ['required', 'integer', 'min:0'],
            'harga_setengah_ons' => ['nullable', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'max:5120'],
        ]);

        $isTobacco = $this->isTobacco($validated['kategori']);
        $satuan = $isTobacco ? 'gram' : $validated['satuan'];
        $hargaSetengahOns = $isTobacco
            ? ($validated['harga_setengah_ons'] ?? (int) round($validated['harga_jual'] / 2))
            : null;
        $totalKulakan = $isTobacco
            ? (int) round(($validated['stok'] / 100) * $validated['harga_beli'])
            : $validated['stok'] * $validated['harga_beli'];

        $imagePath = $this->storeUploadedImage($request);

        $produk = DB::transaction(function () use ($validated, $satuan, $hargaSetengahOns, $totalKulakan, $imagePath) {
            $produk = Produk::create([
                'nama' => $validated['nama'],
                'kategori' => $validated['kategori'],
                'satuan' => $satuan,
                'stok' => $validated['stok'],
                'harga_beli' => $validated['harga_beli'],
                'harga_jual' => $validated['harga_jual'],
                'harga_setengah_ons' => $hargaSetengahOns,
                'gambar' => $imagePath,
            ]);

            Pembelian::create([
                'produk_id' => $produk->id,
                'jumlah' => $validated['stok'],
                'harga_beli' => $validated['harga_beli'],
                'total_harga' => $totalKulakan,
                'catatan' => 'Kulakan produk baru',
            ]);

            return $produk;
        });

        return response()->json([
            'message' => 'Produk baru berhasil disimpan & stok tercatat!',
            'produk' => $produk,
        ], 201);
    }

    public function restockProduk(Request $request, int $id): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Khusus Pemilik Toko.'], 403);
        }

        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $isTobacco = $this->isTobacco($produk->kategori);
        $totalKulakan = $isTobacco
            ? (int) round(($validated['jumlah'] / 100) * $validated['harga_beli'])
            : $validated['jumlah'] * $validated['harga_beli'];

        DB::transaction(function () use ($produk, $validated, $totalKulakan) {
            $produk->increment('stok', $validated['jumlah']);
            $produk->harga_beli = $validated['harga_beli'];
            $produk->save();

            Pembelian::create([
                'produk_id' => $produk->id,
                'jumlah' => $validated['jumlah'],
                'harga_beli' => $validated['harga_beli'],
                'total_harga' => $totalKulakan,
                'catatan' => $validated['catatan'] ?: 'Isi ulang stok (Kulakan)',
            ]);
        });

        return response()->json([
            'message' => 'Stok kulakan berhasil ditambahkan!',
            'produk' => $produk->fresh(),
        ]);
    }

    public function updateProduk(Request $request, int $id): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Khusus Pemilik Toko.'], 403);
        }

        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:50'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'harga_jual' => ['required', 'integer', 'min:0'],
            'harga_setengah_ons' => ['nullable', 'integer', 'min:0'],
            'gambar' => ['nullable', 'image', 'max:5120'],
        ]);

        $isTobacco = $this->isTobacco($validated['kategori']);
        $newImagePath = $this->storeUploadedImage($request);
        if ($newImagePath) {
            $this->deleteLocalImage($produk->gambar);
            $produk->gambar = $newImagePath;
        }

        $produk->nama = $validated['nama'];
        $produk->kategori = $validated['kategori'];
        $produk->satuan = $isTobacco ? 'gram' : $validated['satuan'];
        $produk->stok = $validated['stok'];
        $produk->harga_beli = $validated['harga_beli'];
        $produk->harga_jual = $validated['harga_jual'];
        $produk->harga_setengah_ons = $isTobacco
            ? ($validated['harga_setengah_ons'] ?? (int) round($validated['harga_jual'] / 2))
            : null;
        $produk->save();

        return response()->json([
            'message' => 'Data produk berhasil diperbarui!',
            'produk' => $produk,
        ]);
    }

    public function deleteProduk(Request $request, int $id): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Khusus Pemilik Toko.'], 403);
        }

        $produk = Produk::findOrFail($id);
        $this->deleteLocalImage($produk->gambar);
        $produk->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus!',
        ]);
    }

    public function addPenjualan(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || ! in_array($user['role'], ['kasir', 'owner'], true)) {
            return response()->json(['message' => 'Silakan masuk sebagai Kasir terlebih dahulu.'], 403);
        }

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:produks,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.takaran' => ['nullable', 'in:1_ons,setengah_ons,satuan,custom'],
            'items.*.custom_gram' => ['nullable', 'integer', 'min:1'],
            'items.*.custom_harga' => ['nullable', 'integer', 'min:100'],
            'metode_pembayaran' => ['nullable', 'in:Cash,QRIS'],
            'bayar' => ['required', 'integer', 'min:0'],
        ]);

        $metode = $validated['metode_pembayaran'] ?? 'Cash';
        $habisItems = [];

        $result = DB::transaction(function () use ($validated, $user, $metode, &$habisItems) {
            $total = 0;
            $lineItems = [];

            foreach ($validated['items'] as $entry) {
                $produk = Produk::lockForUpdate()->findOrFail($entry['id']);
                $qty = (int) $entry['qty'];
                $takaran = $entry['takaran'] ?? ($this->isTobacco($produk->kategori) ? '1_ons' : 'satuan');

                if ($takaran === 'custom') {
                    $customGram = (int) ($entry['custom_gram'] ?? 0);
                    $customHarga = (int) ($entry['custom_harga'] ?? 0);
                    if ($customGram < 1 || $customHarga < 100) {
                        abort(422, "Jumlah gram atau nominal untuk {$produk->nama} tidak valid.");
                    }
                    $stokKeluar = $qty * $customGram;
                    $hargaSatuan = $customHarga;
                    $takaranLabel = "{$customGram} gram (Eceran)";
                } elseif ($takaran === '1_ons') {
                    $stokKeluar = $qty * 100;
                    $hargaSatuan = $produk->harga_jual;
                    $takaranLabel = '1 Ons (100g)';
                } elseif ($takaran === 'setengah_ons') {
                    $stokKeluar = $qty * 50;
                    $hargaSatuan = $produk->harga_setengah_ons ?? (int) round($produk->harga_jual / 2);
                    $takaranLabel = '1/2 Ons (50g)';
                } else {
                    $stokKeluar = $qty;
                    $hargaSatuan = $produk->harga_jual;
                    $takaranLabel = $produk->satuan;
                }

                if ($produk->stok < $stokKeluar) {
                    abort(422, "Stok {$produk->nama} tidak mencukupi (butuh {$stokKeluar} {$produk->satuan}, sisa {$produk->stok} {$produk->satuan}).");
                }

                $produk->decrement('stok', $stokKeluar);
                $produk->refresh();
                if ($produk->stok <= 0) {
                    $habisItems[] = $produk->nama;
                }

                $total += $hargaSatuan * $qty;

                $lineItems[] = [
                    'id' => $produk->id,
                    'nama' => $produk->nama,
                    'satuan' => $takaranLabel,
                    'harga_jual' => $hargaSatuan,
                    'qty' => $qty,
                    'stok_keluar' => $stokKeluar,
                ];
            }

            $bayar = $metode === 'QRIS' ? $total : $validated['bayar'];

            if ($bayar < $total) {
                abort(422, 'Uang Tunai Kurang!');
            }

            return Penjualan::create([
                'kode_transaksi' => 'TRX-' . substr((string) now()->getTimestampMs(), -6),
                'kasir' => $user['username'],
                'items' => $lineItems,
                'total' => $total,
                'bayar' => $bayar,
                'kembali' => $bayar - $total,
                'metode_pembayaran' => $metode,
                'waktu' => now('Asia/Jakarta')->format('d/m/Y, H.i.s'),
            ]);
        });

        $emailTerkirim = true;
        if (count($habisItems) > 0) {
            $emailTerkirim = $this->sendOutOfStockAlert(array_values(array_unique($habisItems)));
        }

        return response()->json([
            'message' => 'Transaksi berhasil diproses & stok otomatis diperbarui',
            'stok_habis' => $habisItems,
            'peringatan' => $emailTerkirim ? null : 'Transaksi berhasil, tetapi email pemberitahuan stok habis belum dapat dikirim.',
            'transaksi' => [
                'id' => $result->kode_transaksi,
                'waktu' => $result->waktu,
                'kasir' => $result->kasir,
                'items' => $result->items,
                'total' => $result->total,
                'bayar' => $result->bayar,
                'kembali' => $result->kembali,
                'metode_pembayaran' => $result->metode_pembayaran,
            ],
        ], 201);
    }

    public function addAbsensi(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || ! in_array($user['role'], ['kasir', 'owner'], true)) {
            return response()->json(['message' => 'Silakan masuk sebagai Kasir terlebih dahulu.'], 403);
        }

        return response()->json([
            'message' => 'Gunakan QR absensi Masuk atau Pulang yang disediakan pemilik toko.',
        ], 410);
    }

    public function absenQrPage(Request $request): View
    {
        $tipe = $request->query('tipe') === 'pulang' ? 'pulang' : 'masuk';
        $token = (string) $request->query('token', '');
        $expected = $this->qrTokenFor($tipe);

        return view('absen_qr', [
            'tipe' => $tipe,
            'token' => $token,
            'valid' => hash_equals($expected, $token),
            'currentUser' => $request->session()->get('pos_user'),
        ]);
    }

    public function submitAbsenQr(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tipe' => ['required', 'in:masuk,pulang'],
            'token' => ['required', 'string'],
            'karyawan' => ['required', 'string', 'max:100', 'regex:/^[\pL\pM0-9 .\'-]+$/u'],
            'kasir_code' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'akurasi' => ['required', 'integer', 'min:0'],
        ]);

        $sessionUser = $request->session()->get('pos_user');
        if (! $sessionUser || $sessionUser['role'] !== 'kasir') {
            $kasirCode = Setting::getValue('kasir_code', env('POS_KASIR_PIN', 'MJ-KASIR-2026'));
            if (($validated['kasir_code'] ?? '') !== $kasirCode) {
                return response()->json(['message' => 'Kode Masuk Kasir salah!'], 401);
            }
        }

        $karyawan = $sessionUser && $sessionUser['role'] === 'kasir'
            ? trim($sessionUser['username'])
            : trim($validated['karyawan']);
        $nowJkt = now('Asia/Jakarta');
        $tipe = $validated['tipe'];

        $absensi = DB::transaction(function () use ($validated, $karyawan, $nowJkt, $tipe) {
            $tokens = Setting::whereIn('key', ['qr_masuk_token', 'qr_pulang_token'])
                ->orderBy('key')
                ->lockForUpdate()
                ->get()
                ->keyBy('key');
            $expectedToken = $tokens->get($tipe === 'pulang' ? 'qr_pulang_token' : 'qr_masuk_token')?->value ?? '';

            if ($expectedToken === '' || ! hash_equals($expectedToken, $validated['token'])) {
                abort(403, 'QR Code tidak berlaku atau sudah diperbarui oleh Pemilik Toko.');
            }

            $today = $nowJkt->toDateString();
            $utcStart = $nowJkt->copy()->startOfDay()->utc();
            $utcEnd = $nowJkt->copy()->endOfDay()->utc();
            $todayRecords = Absensi::where('karyawan', $karyawan)
                ->where(function ($query) use ($today, $utcStart, $utcEnd) {
                    $query->whereDate('tanggal_kerja', $today)
                        ->orWhereBetween('created_at', [$utcStart, $utcEnd]);
                })
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $masuk = $todayRecords->firstWhere('tipe', 'masuk');

            if ($tipe === 'masuk') {
                if ($masuk || $todayRecords->contains('tipe', 'pulang')) {
                    abort(409, 'Absensi masuk hari ini sudah tercatat.');
                }

                $shift = $nowJkt->hour < 12 ? 'Shift 1 (09:00 - 12:00)' : 'Shift 2 (12:00 - 21:00)';
                $isLembur = false;
            } else {
                if (! $masuk) {
                    abort(422, 'Absensi pulang belum dapat dicatat karena absensi masuk hari ini belum ada.');
                }

                if ($todayRecords->contains('tipe', 'pulang')) {
                    abort(409, 'Absensi pulang hari ini sudah tercatat.');
                }

                $jamMasuk = $masuk->created_at->copy()->timezone('Asia/Jakarta');
                $durasiMenit = $jamMasuk->diffInMinutes($nowJkt);
                $masukAwal = $jamMasuk->format('H:i') <= '10:00';
                $pulangMalam = $nowJkt->format('H:i') >= '20:45';
                $isLembur = ($masukAwal && $pulangMalam) || $durasiMenit >= 660;
                $shift = $isLembur ? 'Full Day / Lembur (09:00 - 21:00)' : $masuk->shift;

                if ($isLembur) {
                    $masuk->is_lembur = true;
                    $masuk->shift = $shift;
                    $masuk->save();
                }
            }

            return Absensi::create([
                'karyawan' => $karyawan,
                'shift' => $shift,
                'tipe' => $tipe,
                'is_lembur' => $isLembur,
                'status' => 'Hadir',
                'catatan' => ($validated['catatan'] ?? '') ?: ('Scan QR ' . ucfirst($tipe)),
                'waktu' => $nowJkt->format('d/m/Y, H.i.s'),
                'tanggal_kerja' => $today,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'akurasi' => $validated['akurasi'],
            ]);
        });

        return response()->json([
            'message' => $absensi->is_lembur
                ? "Absen {$tipe} berhasil dicatat sebagai LEMBUR (09:00 - 21:00)!"
                : "Absen {$tipe} berhasil dicatat pada {$nowJkt->format('H:i:s')} WIB!",
            'absensi' => $absensi,
        ], 201);
    }

    public function regenerateQr(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            return response()->json(['message' => 'Akses ditolak. Khusus Pemilik Toko.'], 403);
        }

        $validated = $request->validate([
            'tipe' => ['required', 'in:masuk,pulang'],
        ]);

        $key = $validated['tipe'] === 'pulang' ? 'qr_pulang_token' : 'qr_masuk_token';
        $newToken = Str::random(48);

        Setting::setValue($key, $newToken);

        return response()->json([
            'message' => 'QR Absen ' . ucfirst($validated['tipe']) . ' berhasil diperbarui!',
            'token' => $newToken,
        ]);
    }

    private function qrTokenFor(string $tipe): string
    {
        $key = $tipe === 'pulang' ? 'qr_pulang_token' : 'qr_masuk_token';
        $token = Setting::getValue($key);
        $legacyToken = $tipe === 'pulang' ? 'MJ-QR-PULANG-PERMANEN' : 'MJ-QR-MASUK-PERMANEN';

        if ($token !== '' && ! hash_equals($legacyToken, $token)) {
            return $token;
        }

        $token = Str::random(48);
        Setting::setValue($key, $token);

        return $token;
    }

    private function sendOutOfStockAlert(array $productNames): bool
    {
        $ownerEmail = User::where('role', 'owner')->value('email');
        if (! $ownerEmail) {
            Log::warning('Out-of-stock email was not sent because no owner email is configured.', [
                'products' => $productNames,
            ]);

            return false;
        }

        $list = implode("\n- ", $productNames);

        try {
            Mail::raw(
                "Pemberitahuan Stok Habis - Toko Moro Jodho\n\nBarang berikut stoknya baru saja habis (0):\n- {$list}\n\nSilakan segera lakukan kulakan / isi ulang stok melalui halaman Pemilik Toko.",
                function ($message) use ($ownerEmail) {
                    $message->to($ownerEmail)
                        ->subject('Peringatan Stok Barang Habis - Toko Moro Jodho');
                }
            );
        } catch (TransportExceptionInterface $exception) {
            Log::error('Out-of-stock email delivery failed.', [
                'exception' => $exception,
                'products' => $productNames,
                'owner_email' => $ownerEmail,
            ]);

            return false;
        }

        return true;
    }

    public function exportExcel(Request $request): Response
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            abort(403, 'Akses ditolak. Khusus Pemilik Toko.');
        }

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $penjualanQuery = Penjualan::latest();
        $pembelianQuery = Pembelian::with('produk')->latest();

        if ($startDate) {
            $start = Carbon::parse($startDate, 'Asia/Jakarta')->startOfDay()->utc();
            $penjualanQuery->where('created_at', '>=', $start);
            $pembelianQuery->where('created_at', '>=', $start);
        }
        if ($endDate) {
            $end = Carbon::parse($endDate, 'Asia/Jakarta')->endOfDay()->utc();
            $penjualanQuery->where('created_at', '<=', $end);
            $pembelianQuery->where('created_at', '<=', $end);
        }

        $penjualans = $penjualanQuery->get();
        $pembelians = $pembelianQuery->get();

        $omset = $penjualans->sum('total');
        $kulakan = $pembelians->sum('total_harga');
        $profit = $omset - $kulakan;

        $periodeLabel = ($startDate || $endDate)
            ? ($startDate ?: 'Awal') . ' s/d ' . ($endDate ?: 'Sekarang')
            : 'Semua Tanggal';

        $summarySection = $this->buildExcelSection(
            "1. Ringkasan Keuangan ({$periodeLabel})",
            ['Keterangan', 'Jumlah Catatan', 'Nominal (Rp)'],
            [
                ['Total Omset Penjualan', $penjualans->count() . ' transaksi', $omset],
                ['Total Pengeluaran Kulakan', $pembelians->count() . ' catatan', $kulakan],
                ['Perkiraan Keuntungan Bersih', 'Omset - Kulakan', $profit],
            ]
        );

        $penjualanSection = $this->buildExcelSection(
            '2. Laporan Rincian Penjualan',
            ['Kode Transaksi', 'Waktu', 'Kasir', 'Metode Pembayaran', 'Daftar Barang', 'Total Tagihan (Rp)', 'Uang Bayar (Rp)', 'Kembalian (Rp)'],
            $penjualans->map(function ($r) {
                $itemsText = collect($r->items ?? [])->map(fn ($i) => "{$i['nama']} ({$i['qty']} x {$i['satuan']})")->implode(', ');
                return [$r->kode_transaksi, $r->waktu, $r->kasir, $r->metode_pembayaran ?? 'Cash', $itemsText, $r->total, $r->bayar, $r->kembali];
            })->all()
        );

        $kulakanSection = $this->buildExcelSection(
            '3. Laporan Rincian Kulakan Barang',
            ['Tanggal', 'Nama Barang', 'Kategori', 'Jumlah Stok Masuk', 'Satuan', 'Harga Modal (Rp)', 'Total Kulakan (Rp)', 'Catatan'],
            $pembelians->map(function ($r) {
                return [
                    $r->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
                    $r->produk->nama ?? '-',
                    $r->produk->kategori ?? '-',
                    $r->jumlah,
                    $r->produk->satuan ?? '-',
                    $r->harga_beli,
                    $r->total_harga,
                    $r->catatan ?? '-',
                ];
            })->all()
        );

        $html = '<html><head><meta charset="UTF-8"></head><body>'
            . '<h2>Laporan Lengkap Toko Moro Jodho (' . e($periodeLabel) . ')</h2>'
            . $summarySection . '<br>'
            . $penjualanSection . '<br>'
            . $kulakanSection
            . '</body></html>';

        $dateSuffix = now('Asia/Jakarta')->format('Y-m-d');
        $filename = "laporan-moro-jodho-{$dateSuffix}.xls";

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function buildExcelSection(string $title, array $headers, array $rows): string
    {
        $escapedTitle = e($title);
        $th = collect($headers)->map(fn ($h) => '<th style="background:#1a1a1a;color:#c6a052;font-weight:bold;border:1px solid #333;padding:6px;">' . e($h) . '</th>')->implode('');
        $tr = collect($rows)->map(function ($row) {
            $tds = collect($row)->map(fn ($cell) => '<td style="border:1px solid #ccc;padding:5px;">' . e((string) $cell) . '</td>')->implode('');
            return "<tr>{$tds}</tr>";
        })->implode('');

        return "<h3>{$escapedTitle}</h3><table border=\"1\"><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table>";
    }
}
