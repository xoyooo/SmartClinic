<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    protected $fillable = [
        'user_id',
        'spesialis',
        'foto',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalPraktiks(): HasMany
    {
        return $this->hasMany(JadwalPraktik::class);
    }

    public function pemeriksaans(): HasMany
    {
        return $this->hasMany(Pemeriksaan::class);
    }
}