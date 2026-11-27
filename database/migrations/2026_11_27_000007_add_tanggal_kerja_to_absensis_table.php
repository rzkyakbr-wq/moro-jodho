<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->date('tanggal_kerja')->nullable()->after('is_lembur');
            $table->index(
                ['karyawan', 'tanggal_kerja', 'tipe'],
                'absensi_karyawan_tanggal_tipe_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('absensi_karyawan_tanggal_tipe_idx');
            $table->dropColumn('tanggal_kerja');
        });
    }
};
