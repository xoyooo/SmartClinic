<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemeriksaan extends Model
{
    protected $fillable = [
        'booking_id',
        'dokter_id',
        'diagnosis',
        'catatan',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function reseps(): HasMany
    {
        return $this->hasMany(Resep::class);
    }
}