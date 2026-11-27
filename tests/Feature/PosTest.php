<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_page_renders_encyclopedia_pagination_qris_and_eceran(): void
    {
        $response = $this->get('/kasir');

        $response->assertStatus(200);
        $response->assertSee('Toko Moro Jodho');
        $response->assertSee('Sabang sampai Merauke');
        $response->assertSee('Tembakau Gayo');
        $response->assertSee('Tembakau Lembah Baliem');
        $response->assertSee('QRIS');
        $response->assertSee('Beli Bebas');
    }

    public function test_owner_page_hides_password_and_separates_kulakan_and_absensi(): void
    {
        $this->seed();

        $page = $this->get('/owner');
        $page->assertStatus(200)
            ->assertSee('Email Gmail Pemilik')
            ->assertSee('Riwayat Kulakan')
            ->assertSee('Absensi &amp; Lokasi Kasir', false)
            ->assertDontSee('MoroJodho#2026!');
    }

    public function test_kasir_login_auto_records_attendance(): void
    {
        $this->seed();

        $this->postJson('/pos-api/login', [
            'username' => 'Kasir Budi',
            'password' => 'MJ-KASIR-2026',
            'role' => 'kasir',
            'latitude' => -7.0051453,
            'longitude' => 110.4381254,
            'akurasi' => 10,
        ])->assertStatus(200);

        $absen = Absensi::where('karyawan', 'Kasir Budi')->first();
        $this->assertNotNull($absen);
        $this->assertEquals('Hadir', $absen->status);
        $this->assertEquals(10, $absen->akurasi);
    }

    public function test_qris_custom_eceran_and_out_of_stock_detection(): void
    {
        $this->seed();

        $this->postJson('/pos-api/login', [
            'username' => 'morojodho.owner@gmail.com',
            'password' => 'MoroJodho#2026!',
            'role' => 'owner',
        ])->assertStatus(200);

        $createRes = $this->postJson('/pos-api/produk', [
            'nama' => 'Tembakau Uji Eceran',
            'kategori' => 'tembako olahan',
            'satuan' => 'gram',
            'stok' => 125,
            'harga_beli' => 40000,
            'harga_jual' => 70000,
            'harga_setengah_ons' => 36000,
        ]);

        $createRes->assertStatus(201)
            ->assertJsonPath('produk.harga_setengah_ons', 36000);

        $produkId = $createRes->json('produk.id');

        $trxRes = $this->postJson('/pos-api/penjualan', [
            'items' => [
                ['id' => $produkId, 'qty' => 1, 'takaran' => '1_ons'],
                ['id' => $produkId, 'qty' => 1, 'takaran' => 'custom', 'custom_gram' => 25, 'custom_harga' => 18000],
            ],
            'metode_pembayaran' => 'QRIS',
            'bayar' => 0,
        ]);

        $trxRes->assertStatus(201)
            ->assertJsonPath('transaksi.total', 88000)
            ->assertJsonPath('transaksi.bayar', 88000)
            ->assertJsonPath('transaksi.kembali', 0)
            ->assertJsonPath('transaksi.metode_pembayaran', 'QRIS')
            ->assertJsonPath('stok_habis.0', 'Tembakau Uji Eceran');

        $this->assertEquals(0, Produk::findOrFail($produkId)->stok);
    }
}
