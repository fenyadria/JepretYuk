<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketFoto extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     */
    protected $table = 'paket_fotos';

    /**
     * Atribut yang boleh diisi secara massal.
     */
    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'harga',
        'foto',
    ];

    /**
     * Relasi: Satu paket foto memiliki banyak pesanan.
     */
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'paket_id');
    }
}
