<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_page_renders_search_filters_and_map_absensi(): void
    {
        $response = $this->get('/kasir');

        $response->assertStatus(200);
        $response->assertSee('Toko Moro Jodho');
        $response->assertSee('Cari nama barang');
        $response->assertSee('1 Ons (100 gram)');
        $response->assertSee('1/2 Ons (50 gram)');
        $response->assertSee('Periksa Lokasi');
    }

    public function test_owner_page_hides_password_and_supports_gmail_login_and_reset(): void
    {
        $this->seed();

        $page = $this->get('/owner');
        $page->assertStatus(200)
            ->assertSee('Email Gmail Pemilik')
            ->assertSee('Lupa kata sandi?')
            ->assertDontSee('MoroJodho#2026!');

        $this->postJson('/api/pos/login', [
            'username' => 'morojodho.owner@gmail.com',
            'password' => 'salah123',
            'role' => 'owner',
        ])->assertStatus(401);

        $this->postJson('/api/pos/login', [
            'username' => 'morojodho.owner@gmail.com',
            'password' => 'MoroJodho#2026!',
            'role' => 'owner',
        ])->assertStatus(200)
            ->assertJsonPath('user.role', 'owner');

        $forgot = $this->postJson('/api/pos/owner/forgot-password', [
            'email' => 'morojodho.owner@gmail.com',
        ])->assertStatus(200);

        $code = $forgot->json('dev_hint');
        $this->postJson('/api/pos/owner/reset-password', [
            'email' => 'morojodho.owner@gmail.com',
            'code' => $code,
            'password' => 'SandiBaru#2026!',
        ])->assertStatus(200);

        $this->postJson('/api/pos/login', [
            'username' => 'morojodho.owner@gmail.com',
            'password' => 'SandiBaru#2026!',
            'role' => 'owner',
        ])->assertStatus(200);
    }

    public function test_owner_can_set_kasir_code_and_kasir_logs_in_with_it(): void
    {
        $this->seed();

        $this->postJson('/api/pos/login', [
            'username' => 'morojodho.owner@gmail.com',
            'password' => 'MoroJodho#2026!',
            'role' => 'owner',
        ])->assertStatus(200);

        $this->postJson('/api/pos/kasir-code', [
            'kasir_code' => 'MJ-2026',
        ])->assertStatus(200);

        $this->postJson('/api/pos/logout')->assertStatus(200);

        $this->postJson('/api/pos/login', [
            'username' => 'Kasir Andi',
            'password' => 'kasir123',
            'role' => 'kasir',
        ])->assertStatus(401);

        $this->postJson('/api/pos/login', [
            'username' => 'Kasir Andi',
            'password' => 'MJ-2026',
            'role' => 'kasir',
        ])->assertStatus(200);
    }

    public function test_tobacco_auto_gram_ons_checkout_restock_gps_and_excel(): void
    {
        $this->seed();

        $this->postJson('/api/pos/login', [
            'username' => 'morojodho.owner@gmail.com',
            'password' => 'MoroJodho#2026!',
            'role' => 'owner',
        ])->assertStatus(200);

        $createRes = $this->postJson('/api/pos/produk', [
            'nama' => 'Tembakau Uji Ons',
            'kategori' => 'tembako olahan',
            'satuan' => 'pics',
            'stok' => 500,
            'harga_beli' => 40000,
            'harga_jual' => 70000,
        ]);

        $createRes->assertStatus(201)
            ->assertJsonPath('produk.satuan', 'gram')
            ->assertJsonPath('produk.harga_setengah_ons', 35000);

        $produkId = $createRes->json('produk.id');

        $this->postJson("/api/pos/produk/{$produkId}/restock", [
            'jumlah' => 200,
            'harga_beli' => 42000,
            'catatan' => 'Tambah stok',
        ])->assertStatus(200);

        $this->assertEquals(700, Produk::findOrFail($produkId)->stok);

        $trxRes = $this->postJson('/api/pos/penjualan', [
            'items' => [
                ['id' => $produkId, 'qty' => 1, 'takaran' => '1_ons'],
                ['id' => $produkId, 'qty' => 1, 'takaran' => 'setengah_ons'],
            ],
            'bayar' => 120000,
        ]);

        $trxRes->assertStatus(201)
            ->assertJsonPath('transaksi.total', 105000)
            ->assertJsonPath('transaksi.kembali', 15000);

        $this->assertEquals(550, Produk::findOrFail($produkId)->stok);

        $this->postJson('/api/pos/absensi', [
            'shift' => 'Full Day / Lembur (09:00 - 21:00)',
            'status' => 'Hadir',
            'catatan' => 'Lembur',
            'latitude' => -7.0051453,
            'longitude' => 110.4381254,
            'akurasi' => 12,
        ])->assertStatus(201)
            ->assertJsonPath('absensi.akurasi', 12);

        $this->get('/owner/export?start_date=2026-01-01&end_date=2026-12-31')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
    }
}
