<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang boleh diisi secara massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang di-cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Cek apakah user adalah fotografer (admin).
     */
    public function isFotografer(): bool
    {
        return $this->role === 'fotografer';
    }

    /**
     * Cek apakah user adalah klien (pelanggan).
     */
    public function isKlien(): bool
    {
        return $this->role === 'klien';
    }

    /**
     * Relasi: User (klien) memiliki banyak pesanan.
     */
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'klien_id');
    }
}
