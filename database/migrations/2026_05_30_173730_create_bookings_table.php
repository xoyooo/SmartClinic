<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal_praktiks')->cascadeOnDelete();
            $table->text('keluhan');
            $table->string('kode_booking')->unique();
            $table->string('qr_code_path')->nullable();
            $table->enum('status', ['pending','checked_in','selesai','expired'])->default('pending');
            $table->dateTime('tanggal_booking');
            $table->dateTime('expired_at');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bookings');
    }
};