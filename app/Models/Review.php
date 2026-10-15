<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'user_id',
        'photographer_id',
        'rating',
        'review',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function photographer()
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}