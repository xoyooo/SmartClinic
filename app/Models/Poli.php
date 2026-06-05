<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poli extends Model
{
    protected $fillable = [
        'nama_poli',
        'deskripsi',
    ];

    public function jadwalPraktiks(): HasMany
    {
        return $this->hasMany(JadwalPraktik::class);
    }
}