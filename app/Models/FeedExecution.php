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

    protected $fillable = ['feed_schedule_id', 'status'];

    public function schedule()
    {
        return $this->belongsTo(FeedSchedule::class);
    }
}
