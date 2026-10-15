<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     */
    protected $table = 'pesanans';

    /**
     * Atribut yang boleh diisi secara massal.
     */
    protected $fillable = [
        'klien_id',
        'paket_id',
        'tanggal',
        'lokasi',
        'status',
        'catatan_klien',
        'catatan_fotografer',
        'bukti_pembayaran',
    ];

    /**
     * Relasi: Pesanan dimiliki oleh satu klien (User).
     */
    public function klien()
    {
        return $this->belongsTo(User::class, 'klien_id');
    }

    /**
     * Relasi: Pesanan menggunakan satu paket foto.
     */
    public function paketFoto()
    {
        return $this->belongsTo(PaketFoto::class, 'paket_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'pesanan_id');
    }
}
