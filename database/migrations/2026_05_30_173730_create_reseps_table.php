<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_id')->constrained('pemeriksaans')->cascadeOnDelete();
            $table->string('nama_obat');
            $table->string('dosis');
            $table->string('aturan_pakai');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reseps');
    }
};