<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jadwal_praktiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->enum('hari', ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedInteger('kuota')->default(20);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('jadwal_praktiks');
    }
};