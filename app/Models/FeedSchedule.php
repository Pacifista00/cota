<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['waktu_pakan', 'last_executed_at'];

    public function executions()
    {
        return $this->hasMany(FeedExecution::class);
    }
}
