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
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->integer('servis_terakhir_km')->nullable();
            $table->date('servis_terakhir_tanggal')->nullable();
            $table->integer('interval_servis_km')->default(5000); // Tiap kelipatan 5000 km
            $table->integer('interval_servis_bulan')->default(3); // Tiap 3 bulan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->dropColumn([
                'servis_terakhir_km',
                'servis_terakhir_tanggal',
                'interval_servis_km',
                'interval_servis_bulan'
            ]);
        });
    }
};
