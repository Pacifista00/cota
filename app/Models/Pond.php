<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pond extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama', 'lokasi', 'token_tambak',
        'status_koneksi', 'status_perangkat'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
