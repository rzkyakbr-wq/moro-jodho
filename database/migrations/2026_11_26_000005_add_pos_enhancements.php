<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('role')->default('owner')->after('password');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->unsignedBigInteger('harga_setengah_ons')->nullable()->after('harga_jual');
        });

        Schema::table('absensis', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('waktu');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->integer('akurasi')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'akurasi']);
        });
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('harga_setengah_ons');
        });
        Schema::dropIfExists('settings');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
        });
    }
};
