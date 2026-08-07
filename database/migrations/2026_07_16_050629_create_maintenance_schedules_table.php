<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->cascadeOnDelete();
            $table->string('jenis_perawatan'); // Oli Mesin, Oli Gardan, Aki, Ban, dst
            $table->integer('interval_km')->nullable();   // misal tiap 5000 km
            $table->integer('interval_bulan')->nullable(); // misal tiap 6 bulan
            $table->integer('km_terakhir')->nullable();   // odometer saat terakhir ganti
            $table->date('tanggal_terakhir')->nullable(); // tanggal terakhir ganti
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};