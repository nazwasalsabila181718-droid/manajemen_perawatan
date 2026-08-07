<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluhan_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // pelapor
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->text('keluhan');
            $table->enum('tingkat_urgensi', ['ringan', 'sedang', 'berat'])->default('sedang');
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->text('catatan_penanganan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keluhan_kendaraans');
    }
};