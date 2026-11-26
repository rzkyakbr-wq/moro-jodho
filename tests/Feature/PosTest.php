<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_customer_and_kasir_only(): void
    {
        $response = $this->get('/kasir');

        $response->assertStatus(200);
        $response->assertSee('Toko Tembakau Moro Jodho');
        $response->assertSee('Katalog &amp; Pesan', false);
        $response->assertSee('Kasir');
        $response->assertDontSee('Owner Panel');
        $response->assertSee('Shift 1 (09:00 - 12:00)');
        $response->assertSee('Shift 2 (12:00 - 21:00)');
        $response->assertSee('Full Day / Lembur (09:00 - 21:00)');
    }

    public function test_owner_page_is_separated_at_owner_route(): void
    {
        $response = $this->get('/owner');

        $response->assertStatus(200);
        $response->assertSee('Moro Jodho — Pemilik');
        $response->assertSee('Masuk Halaman Pemilik');
    }

    public function test_api_all_returns_seeded_data(): void
    {
        $this->seed();

        $response = $this->getJson('/api/pos/all');

        $response->assertStatus(200)
            ->assertJsonStructure(['produk', 'absensi', 'penjualan', 'pembelian']);

        $this->assertGreaterThan(0, count($response->json('produk')));
    }

    public function test_login_validates_pin_and_sets_session(): void
    {
        $this->postJson('/api/pos/login', [
            'username' => 'Budi',
            'password' => 'salah',
            'role' => 'kasir',
        ])->assertStatus(401);

        $this->postJson('/api/pos/login', [
            'username' => 'Budi',
            'password' => 'kasir123',
            'role' => 'kasir',
        ])->assertStatus(200)
            ->assertJsonPath('user.role', 'kasir');
    }

    public function test_owner_crud_export_and_kasir_checkout(): void
    {
        $this->postJson('/api/pos/login', [
            'username' => 'Rizky',
            'password' => 'owner123',
            'role' => 'owner',
        ])->assertStatus(200);

        $createRes = $this->postJson('/api/pos/produk', [
            'nama' => 'Tembakau Uji Coba',
            'kategori' => 'original',
            'satuan' => 'gram',
            'stok' => 100,
            'harga_beli' => 400,
            'harga_jual' => 600,
        ]);

        $createRes->assertStatus(201);
        $produkId = $createRes->json('produk.id');

        $this->postJson("/api/pos/produk/{$produkId}", [
            'nama' => 'Tembakau Uji Coba Edit',
            'kategori' => 'racik',
            'satuan' => 'gram',
            'stok' => 80,
            'harga_beli' => 450,
            'harga_jual' => 700,
        ])->assertStatus(200)
            ->assertJsonPath('produk.nama', 'Tembakau Uji Coba Edit');

        $trxRes = $this->postJson('/api/pos/penjualan', [
            'items' => [['id' => $produkId, 'qty' => 10]],
            'bayar' => 10000,
        ]);

        $trxRes->assertStatus(201)
            ->assertJsonPath('transaksi.total', 7000)
            ->assertJsonPath('transaksi.kembali', 3000);

        $this->assertEquals(70, Produk::findOrFail($produkId)->stok);

        $this->postJson('/api/pos/absensi', [
            'shift' => 'Full Day / Lembur (09:00 - 21:00)',
            'status' => 'Hadir',
            'catatan' => 'Lembur toko',
        ])->assertStatus(201);

        $this->get('/owner/export/penjualan')->assertStatus(200)->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->get('/owner/export/kulakan')->assertStatus(200)->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $this->get('/owner/export/omset')->assertStatus(200)->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

        $this->deleteJson("/api/pos/produk/{$produkId}")->assertStatus(200);
        $this->assertNull(Produk::find($produkId));
    }
}
