<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            // 1. Identitas Fisik
            $table->string('jenis_kendaraan'); // Mobil boks, mobil dinas, motor
            $table->string('merek');           // Toyota, Honda, dll
            $table->string('tipe');            // Avanza, Gran Max, dll
            $table->string('nomor_polisi');     // Plat nomor kendaraan

            // 2. Manajemen Operasional
            $table->string('pool_lokasi');     // Lokasi parkir/pool utama
            $table->string('nama_driver');     // Nama supir utamanya

            // 3. Legalitas & Pajak
            $table->date('tanggal_stnk');      // Tanggal jatuh tempo STNK
            $table->date('pajak_tahunan');     // Tanggal jatuh tempo pajak tahunan
            $table->date('pajak_5_tahunan');   // Tanggal jatuh tempo Pajak 5 Tahunan
            $table->date('kir_bengkel')->nullable(); // Khusus mobil barang (boleh kosong)

            // 4. Kondisi Aktual
            $table->integer('odometer_terakhir'); // Angka KM terakhir kendaraan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
