<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['waktu_pakan'];

    public function executions()
    {
        return $this->hasMany(FeedExecution::class);
    }
}
