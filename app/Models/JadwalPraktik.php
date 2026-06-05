<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPraktik extends Model
{
    const SLOT_DURATION = 30; // menit per pasien
    const SLOT_GAP      = 5;  // menit jeda antar pasien
    const BREAK_START   = '12:30';
    const BREAK_END     = '14:00';

    protected $table = 'jadwal_praktiks';

    protected $fillable = [
        'dokter_id',
        'poli_id',
        'hari',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'kuota',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'jadwal_id');
    }

    /**
     * Generate semua slot waktu berdasarkan jam_mulai & jam_selesai.
     * Setiap slot = SLOT_DURATION menit, jeda SLOT_GAP menit.
     * Slot yang overlap dengan jam istirahat (BREAK_START–BREAK_END) dilewati.
     */
    public function generateSlots(): array
    {
        $slots    = [];
        $interval = self::SLOT_DURATION + self::SLOT_GAP; // 35 menit
        $breakStart = Carbon::createFromTimeString(self::BREAK_START);
        $breakEnd   = Carbon::createFromTimeString(self::BREAK_END);
        $end        = Carbon::createFromTimeString($this->jam_selesai);

        $current = Carbon::createFromTimeString($this->jam_mulai);

        while (true) {
            $slotEnd = $current->copy()->addMinutes(self::SLOT_DURATION);

            // Hentikan jika akhir slot melewati jam_selesai
            if ($slotEnd->gt($end)) break;

            // Skip slot yang bersinggungan dengan jam istirahat
            $overlapsBreak = $current->lt($breakEnd) && $slotEnd->gt($breakStart);

            if (!$overlapsBreak) {
                $slots[] = $current->format('H:i');
            }

            // Lompat ke slot berikutnya
            $current->addMinutes($interval);

            // Jika setelah istirahat belum melewati breakEnd, langsung loncat ke breakEnd
            if ($current->gte($breakStart) && $current->lt($breakEnd)) {
                $current = $breakEnd->copy();
            }
        }

        return $slots;
    }

    /**
     * Slot yang tersedia = generateSlots() dikurangi yang sudah dipesan pada tanggal tertentu.
     * Jika tanggal = hari ini, slot yang sudah lewat juga difilter.
     */
    public function getAvailableSlots(string $date = null): array
    {
        $all    = $this->generateSlots();
        $date   = $date ?? now()->toDateString();

        $booked = $this->bookings()
            ->whereDate('tanggal_booking', $date)
            ->whereIn('status', ['pending', 'checked_in'])
            ->pluck('slot_waktu')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();

        $available = array_filter($all, fn($s) => !in_array($s, $booked));

        // Jika booking untuk hari ini, filter slot yang sudah lewat
        if ($date === now()->toDateString()) {
            $nowTime  = now()->format('H:i');
            $available = array_filter($available, fn($s) => $s > $nowTime);
        }

        return array_values($available);
    }

    public function sisaKuota(string $date = null): int
    {
        return count($this->getAvailableSlots($date));
    }
}