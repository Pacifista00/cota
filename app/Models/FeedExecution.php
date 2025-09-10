<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedExecution extends Model
{
    use HasFactory;

    protected $casts = [
        'executed_at' => 'datetime',
    ];

    protected $fillable = ['status', 'executed_at'];

    public function schedule()
    {
        return $this->belongsTo(FeedSchedule::class);
    }
}
