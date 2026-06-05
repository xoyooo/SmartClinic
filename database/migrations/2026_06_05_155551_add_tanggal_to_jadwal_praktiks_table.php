<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_praktiks', function (Blueprint $table) {
            // Tambah kolom tanggal spesifik (nullable untuk backward compat)
            $table->date('tanggal')->nullable()->after('hari');
        });

        // Isi tanggal untuk data lama berdasarkan hari (isi dengan tanggal berikutnya)
        $hariToDay = [
            'Minggu' => 0, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
            'Kamis'  => 4, 'Jumat' => 5, 'Sabtu'  => 6,
        ];

        $jadwals = DB::table('jadwal_praktiks')->get();
        foreach ($jadwals as $j) {
            $targetDay = $hariToDay[$j->hari] ?? 1;
            $today = now();
            $diff = ($targetDay - $today->dayOfWeek + 7) % 7;
            $nextDate = $today->copy()->addDays($diff)->toDateString();
            DB::table('jadwal_praktiks')->where('id', $j->id)->update(['tanggal' => $nextDate]);
        }

        // Jadikan tanggal NOT NULL setelah diisi
        Schema::table('jadwal_praktiks', function (Blueprint $table) {
            $table->date('tanggal')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_praktiks', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
