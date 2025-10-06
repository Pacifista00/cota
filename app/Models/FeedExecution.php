<?php

namespace App\Models;

use App\Enums\FeedExecutionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'feed_schedule_id',
        'trigger_type',
        'status',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'status' => FeedExecutionStatus::class,
    ];

    /**
     * Get the schedule that owns this execution
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(FeedSchedule::class, 'feed_schedule_id');
    }

    /**
     * Scope to get only successful executions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', FeedExecutionStatus::SUCCESS->value);
    }

    /**
     * Scope to get only failed executions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', FeedExecutionStatus::FAILED->value);
    }

    /**
     * Scope to get only pending executions
     */
    public function scopePending($query)
    {
        return $query->where('status', FeedExecutionStatus::PENDING->value);
    }

    /**
     * Scope to get scheduled executions
     */
    public function scopeScheduled($query)
    {
        return $query->where('trigger_type', 'scheduled');
    }

    /**
     * Scope to get manual executions
     */
    public function scopeManual($query)
    {
        return $query->where('trigger_type', 'manual');
    }

    /**
     * Check if execution is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === FeedExecutionStatus::SUCCESS;
    }

    /**
     * Check if execution failed
     */
    public function isFailed(): bool
    {
        return $this->status === FeedExecutionStatus::FAILED;
    }

    /**
     * Check if execution is pending
     */
    public function isPending(): bool
    {
        return $this->status === FeedExecutionStatus::PENDING;
    }
}

