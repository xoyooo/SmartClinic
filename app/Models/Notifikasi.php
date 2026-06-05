<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'pesan',
        'is_read',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}