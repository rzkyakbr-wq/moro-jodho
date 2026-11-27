<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'kode_transaksi',
        'kasir',
        'items',
        'total',
        'bayar',
        'kembali',
        'metode_pembayaran',
        'waktu',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }
}
