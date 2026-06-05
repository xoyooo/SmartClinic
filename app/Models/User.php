<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'no_hp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function dokter(): HasOne
    {
        return $this->hasOne(Dokter::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'pasien_id');
    }

    public function notifikasis(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDokter(): bool
    {
        return $this->role === 'dokter';
    }

    public function isPasien(): bool
    {
        return $this->role === 'pasien';
    }
}