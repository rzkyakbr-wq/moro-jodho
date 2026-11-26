<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PosController extends Controller
{
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

    public function getAll(): JsonResponse
    {
        return response()->json([
            'produk' => Produk::orderBy('id')->get(),
            'absensi' => Absensi::latest()->limit(50)->get(),
            'penjualan' => Penjualan::latest()->limit(200)->get(),
            'pembelian' => Pembelian::with('produk:id,nama,satuan,kategori')->latest()->limit(200)->get(),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:kasir,owner'],
        ]);

        $pins = [
            'kasir' => env('POS_KASIR_PIN', 'kasir123'),
            'owner' => env('POS_OWNER_PIN', 'owner123'),
        ];

        if ($validated['password'] !== $pins[$validated['role']]) {
            return response()->json([
                'message' => 'PIN Akses Salah!',
            ], 401);
        }

        $user = [
            'username' => trim($validated['username']),
            'role' => $validated['role'],
        ];

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
            'gambar' => ['nullable', 'image', 'max:5120'],
        ]);

        $imagePath = $this->storeUploadedImage($request);

        $produk = DB::transaction(function () use ($validated, $imagePath) {
            $produk = Produk::create([
                'nama' => $validated['nama'],
                'kategori' => $validated['kategori'],
                'satuan' => $validated['satuan'],
                'stok' => $validated['stok'],
                'harga_beli' => $validated['harga_beli'],
                'harga_jual' => $validated['harga_jual'],
                'gambar' => $imagePath,
            ]);

            Pembelian::create([
                'produk_id' => $produk->id,
                'jumlah' => $validated['stok'],
                'harga_beli' => $validated['harga_beli'],
                'total_harga' => $validated['stok'] * $validated['harga_beli'],
                'catatan' => 'Kulakan produk baru',
            ]);

            return $produk;
        });

        return response()->json([
            'message' => 'Produk baru berhasil disimpan!',
            'produk' => $produk,
        ], 201);
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
            'gambar' => ['nullable', 'image', 'max:5120'],
        ]);

        $newImagePath = $this->storeUploadedImage($request);
        if ($newImagePath) {
            $this->deleteLocalImage($produk->gambar);
            $produk->gambar = $newImagePath;
        }

        $produk->nama = $validated['nama'];
        $produk->kategori = $validated['kategori'];
        $produk->satuan = $validated['satuan'];
        $produk->stok = $validated['stok'];
        $produk->harga_beli = $validated['harga_beli'];
        $produk->harga_jual = $validated['harga_jual'];
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
            'bayar' => ['required', 'integer', 'min:0'],
        ]);

        $result = DB::transaction(function () use ($validated, $user) {
            $total = 0;
            $lineItems = [];

            foreach ($validated['items'] as $entry) {
                $produk = Produk::lockForUpdate()->findOrFail($entry['id']);
                $qty = (int) $entry['qty'];

                if ($produk->stok < $qty) {
                    abort(422, "Stok {$produk->nama} tidak mencukupi (sisa {$produk->stok}).");
                }

                $produk->decrement('stok', $qty);
                $total += $produk->harga_jual * $qty;

                $lineItems[] = [
                    'id' => $produk->id,
                    'nama' => $produk->nama,
                    'satuan' => $produk->satuan,
                    'harga_jual' => $produk->harga_jual,
                    'qty' => $qty,
                ];
            }

            if ($validated['bayar'] < $total) {
                abort(422, 'Uang Tunai Kurang!');
            }

            return Penjualan::create([
                'kode_transaksi' => 'TRX-' . substr((string) now()->getTimestampMs(), -6),
                'kasir' => $user['username'],
                'items' => $lineItems,
                'total' => $total,
                'bayar' => $validated['bayar'],
                'kembali' => $validated['bayar'] - $total,
                'waktu' => now('Asia/Jakarta')->format('d/m/Y, H.i.s'),
            ]);
        });

        return response()->json([
            'message' => 'Transaksi berhasil diproses',
            'transaksi' => [
                'id' => $result->kode_transaksi,
                'waktu' => $result->waktu,
                'kasir' => $result->kasir,
                'items' => $result->items,
                'total' => $result->total,
                'bayar' => $result->bayar,
                'kembali' => $result->kembali,
            ],
        ], 201);
    }

    public function addAbsensi(Request $request): JsonResponse
    {
        $user = $request->session()->get('pos_user');
        if (! $user || ! in_array($user['role'], ['kasir', 'owner'], true)) {
            return response()->json(['message' => 'Silakan masuk sebagai Kasir terlebih dahulu.'], 403);
        }

        $validated = $request->validate([
            'shift' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $absensi = Absensi::create([
            'karyawan' => $user['username'],
            'shift' => $validated['shift'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?: '-',
            'waktu' => now('Asia/Jakarta')->format('d/m/Y, H.i.s'),
        ]);

        return response()->json([
            'message' => 'Absensi berhasil disimpan!',
            'absensi' => $absensi,
        ], 201);
    }

    public function exportExcel(Request $request, string $type): Response
    {
        $user = $request->session()->get('pos_user');
        if (! $user || $user['role'] !== 'owner') {
            abort(403, 'Akses ditolak. Khusus Pemilik Toko.');
        }

        $date = now('Asia/Jakarta')->format('Y-m-d');

        if ($type === 'penjualan') {
            $rows = Penjualan::latest()->get();
            $html = $this->buildExcelTable(
                'Laporan Data Penjualan - Toko Moro Jodho',
                ['Kode Transaksi', 'Waktu', 'Kasir', 'Daftar Barang', 'Total Tagihan (Rp)', 'Uang Bayar (Rp)', 'Kembalian (Rp)'],
                $rows->map(function ($r) {
                    $itemsText = collect($r->items ?? [])->map(fn ($i) => "{$i['nama']} ({$i['qty']} {$i['satuan']})")->implode(', ');
                    return [$r->kode_transaksi, $r->waktu, $r->kasir, $itemsText, $r->total, $r->bayar, $r->kembali];
                })->all()
            );
            $filename = "laporan-penjualan-{$date}.xls";
        } elseif ($type === 'kulakan') {
            $rows = Pembelian::with('produk')->latest()->get();
            $html = $this->buildExcelTable(
                'Laporan Data Kulakan - Toko Moro Jodho',
                ['Tanggal', 'Nama Barang', 'Kategori', 'Jumlah', 'Satuan', 'Harga Modal (Rp)', 'Total Kulakan (Rp)', 'Catatan'],
                $rows->map(function ($r) {
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
            $filename = "laporan-kulakan-{$date}.xls";
        } else {
            $omset = Penjualan::sum('total');
            $kulakan = Pembelian::sum('total_harga');
            $profit = $omset - $kulakan;
            $jumlahTrx = Penjualan::count();
            $jumlahKulakan = Pembelian::count();

            $html = $this->buildExcelTable(
                'Ringkasan Omset, Kulakan & Keuntungan - Toko Moro Jodho',
                ['Keterangan', 'Jumlah Transaksi / Catatan', 'Nominal (Rp)'],
                [
                    ['Total Omset Penjualan', "{$jumlahTrx} transaksi", $omset],
                    ['Total Pengeluaran Kulakan', "{$jumlahKulakan} catatan kulakan", $kulakan],
                    ['Perkiraan Keuntungan Bersih', 'Omset dikurangi Kulakan', $profit],
                ]
            );
            $filename = "ringkasan-omset-{$date}.xls";
        }

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function buildExcelTable(string $title, array $headers, array $rows): string
    {
        $escapedTitle = e($title);
        $th = collect($headers)->map(fn ($h) => '<th style="background:#1a1a1a;color:#c6a052;font-weight:bold;border:1px solid #333;padding:6px;">' . e($h) . '</th>')->implode('');
        $tr = collect($rows)->map(function ($row) {
            $tds = collect($row)->map(fn ($cell) => '<td style="border:1px solid #ccc;padding:5px;">' . e((string) $cell) . '</td>')->implode('');
            return "<tr>{$tds}</tr>";
        })->implode('');

        return "<html><head><meta charset=\"UTF-8\"></head><body><h3>{$escapedTitle}</h3><table border=\"1\"><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></body></html>";
    }
}
