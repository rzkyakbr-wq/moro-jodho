<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'karyawan',
        'shift',
        'tipe',
        'is_lembur',
        'tanggal_kerja',
        'status',
        'catatan',
        'waktu',
        'latitude',
        'longitude',
        'akurasi',
    ];

    protected function casts(): array
    {
        return [
            'is_lembur' => 'boolean',
            'tanggal_kerja' => 'date:Y-m-d',
        ];
    }
}
