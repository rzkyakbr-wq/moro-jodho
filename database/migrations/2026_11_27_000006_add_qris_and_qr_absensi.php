<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('metode_pembayaran', 30)->default('Cash')->after('kembali');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->string('tipe', 20)->default('masuk')->after('shift');
            $table->boolean('is_lembur')->default(false)->after('tipe');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'is_lembur']);
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
        });
    }
};
