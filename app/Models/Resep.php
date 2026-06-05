<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resep extends Model
{
    protected $fillable = [
        'pemeriksaan_id',
        'nama_obat',
        'dosis',
        'aturan_pakai',
    ];

    public function pemeriksaan(): BelongsTo
    {
        return $this->belongsTo(Pemeriksaan::class);
    }
}