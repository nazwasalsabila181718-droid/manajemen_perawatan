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
        Schema::create('checklist_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Teknisi/User yg input
            $table->date('tanggal_cek');
            
            // Cairan
            $table->enum('cairan_oli_mesin', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('cairan_coolant', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('cairan_minyak_rem', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('cairan_wiper', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');

            // Kaki-kaki
            $table->enum('kaki_tekanan_ban', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('kaki_keausan_ban', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('kaki_rem', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');

            // Kelistrikan
            $table->enum('listrik_lampu_utama', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('listrik_lampu_sein', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('listrik_lampu_rem', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('listrik_klakson', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('listrik_ac', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');

            // Kebersihan
            $table->enum('kebersihan_interior', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');
            $table->enum('kebersihan_eksterior', ['Baik', 'Perlu Perhatian', 'Buruk'])->default('Baik');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_kendaraans');
    }
};
