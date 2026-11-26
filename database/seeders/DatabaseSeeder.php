<?php

namespace Database\Seeders;

use App\Models\Pembelian;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Produk::count() > 0) {
            return;
        }

        $items = [
            [
                'nama' => 'Tembakau Mole Madu Premium',
                'kategori' => 'tembako olahan',
                'satuan' => 'gram',
                'stok' => 500,
                'harga_beli' => 450,
                'harga_jual' => 700,
                'gambar' => null,
            ],
            [
                'nama' => 'Tembakau Mole Vanilla Gold',
                'kategori' => 'tembako olahan',
                'satuan' => 'gram',
                'stok' => 400,
                'harga_beli' => 500,
                'harga_jual' => 800,
                'gambar' => null,
            ],
            [
                'nama' => 'Virginia Flue-Cured Original',
                'kategori' => 'original',
                'satuan' => 'gram',
                'stok' => 600,
                'harga_beli' => 400,
                'harga_jual' => 650,
                'gambar' => null,
            ],
            [
                'nama' => 'Temanggung Srinthil Asli',
                'kategori' => 'original',
                'satuan' => 'gram',
                'stok' => 300,
                'harga_beli' => 650,
                'harga_jual' => 1000,
                'gambar' => null,
            ],
            [
                'nama' => 'Racikan Kretek Halus Moro Jodho',
                'kategori' => 'racik',
                'satuan' => 'gram',
                'stok' => 450,
                'harga_beli' => 550,
                'harga_jual' => 850,
                'gambar' => null,
            ],
            [
                'nama' => 'Racikan Mild Nusantara',
                'kategori' => 'racik',
                'satuan' => 'gram',
                'stok' => 350,
                'harga_beli' => 500,
                'harga_jual' => 750,
                'gambar' => null,
            ],
            [
                'nama' => 'Papir Manis Cap Semar',
                'kategori' => 'papir',
                'satuan' => 'pack',
                'stok' => 120,
                'harga_beli' => 2500,
                'harga_jual' => 4000,
                'gambar' => null,
            ],
            [
                'nama' => 'Papir Putih Polos 100 Lembar',
                'kategori' => 'papir',
                'satuan' => 'pack',
                'stok' => 150,
                'harga_beli' => 2000,
                'harga_jual' => 3500,
                'gambar' => null,
            ],
            [
                'nama' => 'Cengkeh Rajang Aromatik Super',
                'kategori' => 'cengkeh',
                'satuan' => 'gram',
                'stok' => 300,
                'harga_beli' => 300,
                'harga_jual' => 500,
                'gambar' => null,
            ],
            [
                'nama' => 'Filter Busa Regular (Isi 100)',
                'kategori' => 'aksesoris',
                'satuan' => 'pack',
                'stok' => 80,
                'harga_beli' => 6000,
                'harga_jual' => 10000,
                'gambar' => null,
            ],
            [
                'nama' => 'Kotak Kaleng Tembakau Vintage',
                'kategori' => 'aksesoris',
                'satuan' => 'pics',
                'stok' => 40,
                'harga_beli' => 18000,
                'harga_jual' => 28000,
                'gambar' => null,
            ],
            [
                'nama' => 'Alat Linting Kayu Manual',
                'kategori' => 'alat',
                'satuan' => 'pics',
                'stok' => 35,
                'harga_beli' => 15000,
                'harga_jual' => 25000,
                'gambar' => null,
            ],
        ];

        foreach ($items as $item) {
            $produk = Produk::create($item);

            Pembelian::create([
                'produk_id' => $produk->id,
                'jumlah' => $produk->stok,
                'harga_beli' => $produk->harga_beli,
                'total_harga' => $produk->stok * $produk->harga_beli,
                'catatan' => 'Stok awal toko',
            ]);
        }
    }
}
