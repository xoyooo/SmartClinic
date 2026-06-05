<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'pasien_id',
        'jadwal_id',
        'slot_waktu',
        'keluhan',
        'kode_booking',
        'qr_code_path',
        'status',
        'tanggal_booking',
        'expired_at',
    ];

    protected $casts = [
        'tanggal_booking' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalPraktik::class, 'jadwal_id');
    }

    public function pemeriksaan(): HasOne
    {
        return $this->hasOne(Pemeriksaan::class);
    }

    public function isExpired(): bool
    {
        return $this->status === 'pending' && now()->gt($this->expired_at);
    }

    public function nomorAntrian(): int
    {
        return self::where('jadwal_id', $this->jadwal_id)
            ->whereDate('tanggal_booking', $this->tanggal_booking->format('Y-m-d'))
            ->whereIn('status', ['pending', 'checked_in', 'selesai'])
            ->where('id', '<=', $this->id)
            ->count();
    }
}