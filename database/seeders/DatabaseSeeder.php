<?php

namespace Database\Seeders;

use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ownerEmail = env('POS_OWNER_EMAIL', 'morojodho.owner@gmail.com');
        $ownerPass = env('POS_OWNER_PASSWORD', 'MoroJodho#2026!');

        if (! User::where('role', 'owner')->exists()) {
            User::create([
                'name' => 'Pemilik Moro Jodho',
                'username' => 'owner',
                'email' => $ownerEmail,
                'password' => Hash::make($ownerPass),
                'role' => 'owner',
            ]);
        }

        if (! Setting::where('key', 'kasir_code')->exists()) {
            Setting::setValue('kasir_code', env('POS_KASIR_PIN', 'MJ-KASIR-2026'));
        }
        if (! Setting::where('key', 'qr_masuk_token')->exists()) {
            Setting::setValue('qr_masuk_token', Str::random(48));
        }
        if (! Setting::where('key', 'qr_pulang_token')->exists()) {
            Setting::setValue('qr_pulang_token', Str::random(48));
        }

        if (Produk::count() > 0) {
            return;
        }

        $items = [
            [
                'nama' => 'Tembakau Mole Madu Premium',
                'kategori' => 'tembako olahan',
                'satuan' => 'gram',
                'stok' => 1000,
                'harga_beli' => 45000,
                'harga_jual' => 70000,
                'harga_setengah_ons' => 35000,
                'gambar' => null,
            ],
            [
                'nama' => 'Tembakau Mole Vanilla Gold',
                'kategori' => 'tembako olahan',
                'satuan' => 'gram',
                'stok' => 800,
                'harga_beli' => 50000,
                'harga_jual' => 80000,
                'harga_setengah_ons' => 40000,
                'gambar' => null,
            ],
            [
                'nama' => 'Virginia Flue-Cured Original',
                'kategori' => 'original',
                'satuan' => 'gram',
                'stok' => 1200,
                'harga_beli' => 40000,
                'harga_jual' => 65000,
                'harga_setengah_ons' => 33000,
                'gambar' => null,
            ],
            [
                'nama' => 'Temanggung Srinthil Asli',
                'kategori' => 'original',
                'satuan' => 'gram',
                'stok' => 600,
                'harga_beli' => 65000,
                'harga_jual' => 100000,
                'harga_setengah_ons' => 50000,
                'gambar' => null,
            ],
            [
                'nama' => 'Racikan Kretek Halus Moro Jodho',
                'kategori' => 'racik',
                'satuan' => 'gram',
                'stok' => 900,
                'harga_beli' => 55000,
                'harga_jual' => 85000,
                'harga_setengah_ons' => 43000,
                'gambar' => null,
            ],
            [
                'nama' => 'Racikan Mild Nusantara',
                'kategori' => 'racik',
                'satuan' => 'gram',
                'stok' => 700,
                'harga_beli' => 50000,
                'harga_jual' => 75000,
                'harga_setengah_ons' => 38000,
                'gambar' => null,
            ],
            [
                'nama' => 'Papir Manis Cap Semar',
                'kategori' => 'papir',
                'satuan' => 'pack',
                'stok' => 120,
                'harga_beli' => 2500,
                'harga_jual' => 4000,
                'harga_setengah_ons' => null,
                'gambar' => null,
            ],
            [
                'nama' => 'Papir Putih Polos 100 Lembar',
                'kategori' => 'papir',
                'satuan' => 'pack',
                'stok' => 150,
                'harga_beli' => 2000,
                'harga_jual' => 3500,
                'harga_setengah_ons' => null,
                'gambar' => null,
            ],
            [
                'nama' => 'Cengkeh Rajang Aromatik Super',
                'kategori' => 'cengkeh',
                'satuan' => 'gram',
                'stok' => 300,
                'harga_beli' => 300,
                'harga_jual' => 500,
                'harga_setengah_ons' => null,
                'gambar' => null,
            ],
            [
                'nama' => 'Filter Busa Regular (Isi 100)',
                'kategori' => 'aksesoris',
                'satuan' => 'pack',
                'stok' => 80,
                'harga_beli' => 6000,
                'harga_jual' => 10000,
                'harga_setengah_ons' => null,
                'gambar' => null,
            ],
            [
                'nama' => 'Kotak Kaleng Tembakau Vintage',
                'kategori' => 'aksesoris',
                'satuan' => 'pics',
                'stok' => 40,
                'harga_beli' => 18000,
                'harga_jual' => 28000,
                'harga_setengah_ons' => null,
                'gambar' => null,
            ],
            [
                'nama' => 'Alat Linting Kayu Manual',
                'kategori' => 'alat',
                'satuan' => 'pics',
                'stok' => 35,
                'harga_beli' => 15000,
                'harga_jual' => 25000,
                'harga_setengah_ons' => null,
                'gambar' => null,
            ],
        ];

        $tobaccoCats = ['tembako olahan', 'original', 'racik'];

        foreach ($items as $item) {
            $produk = Produk::create($item);

            $totalKulakan = in_array($produk->kategori, $tobaccoCats, true)
                ? (int) round(($produk->stok / 100) * $produk->harga_beli)
                : $produk->stok * $produk->harga_beli;

            Pembelian::create([
                'produk_id' => $produk->id,
                'jumlah' => $produk->stok,
                'harga_beli' => $produk->harga_beli,
                'total_harga' => $totalKulakan,
                'catatan' => 'Stok awal toko',
            ]);
        }
    }
}
