<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $fillable = [
        'nama',
        'kategori',
        'satuan',
        'stok',
        'harga_beli',
        'harga_jual',
        'gambar',
    ];

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }
}
