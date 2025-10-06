<?php

namespace App\Models;

use App\Enums\ScheduleFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class FeedSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'waktu_pakan',
        'start_date',
        'end_date',
        'is_active',
        'frequency_type',
        'frequency_data',
        'last_executed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'frequency_data' => 'array',
        'last_executed_at' => 'date',
        'frequency_type' => ScheduleFrequency::class,
    ];

    /**
     * Get the user that owns this schedule
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all executions for this schedule
     */
    public function executions(): HasMany
    {
        return $this->hasMany(FeedExecution::class);
    }

    /**
     * Scope to get only active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get schedules that should run today
     */
    public function scopeShouldRunToday($query)
    {
        $today = Carbon::today();
        
        return $query->active()
            ->where(function ($q) use ($today) {
                // Start date is null or in the past
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                // End date is null or in the future
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            });
    }

    /**
     * Scope to get schedules ready to execute now
     */
    public function scopeReadyToExecute($query)
    {
        $now = Carbon::now();
        $oneMinuteAgo = $now->copy()->subMinute();
        $today = $now->toDateString();

        return $query->shouldRunToday()
            ->whereTime('waktu_pakan', '<=', $now->format('H:i:s'))
            ->whereTime('waktu_pakan', '>=', $oneMinuteAgo->format('H:i:s'))
            ->where(function ($q) use ($today) {
                $q->whereNull('last_executed_at')
                    ->orWhereDate('last_executed_at', '<>', $today);
            });
    }

    /**
     * Check if this schedule is currently valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();

        // Check start date
        if ($this->start_date && $this->start_date->gt($today)) {
            return false;
        }

        // Check end date
        if ($this->end_date && $this->end_date->lt($today)) {
            return false;
        }

        return true;
    }

    /**
     * Check if this schedule should run on a given date
     */
    public function shouldRunOnDate(Carbon $date): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check if already executed today
        if ($this->last_executed_at && $this->last_executed_at->isSameDay($date)) {
            return false;
        }

        // For daily frequency, always return true if valid
        if ($this->frequency_type === ScheduleFrequency::DAILY) {
            return true;
        }

        // For other frequency types, check frequency_data
        // This can be extended based on needs
        return true;
    }

    /**
     * Mark this schedule as executed
     */
    public function markAsExecuted(): void
    {
        $this->update([
            'last_executed_at' => Carbon::today(),
        ]);
    }

    /**
     * Get the next execution time
     */
    public function getNextExecutionAttribute(): ?Carbon
    {
        if (!$this->isValid()) {
            return null;
        }

        $today = Carbon::today();
        $time = Carbon::parse($this->waktu_pakan);

        $nextExecution = $today->copy()
            ->setHour($time->hour)
            ->setMinute($time->minute)
            ->setSecond($time->second);

        // If the time has passed today and not yet executed, it's today
        if ($nextExecution->isFuture() && !$this->wasExecutedToday()) {
            return $nextExecution;
        }

        // Otherwise, it's tomorrow
        return $nextExecution->addDay();
    }

    /**
     * Check if schedule was executed today
     */
    public function wasExecutedToday(): bool
    {
        return $this->last_executed_at && $this->last_executed_at->isToday();
    }

    /**
     * Get remaining days for this schedule
     */
    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->end_date) {
            return null; // Unlimited
        }

        $today = Carbon::today();
        if ($this->end_date->lt($today)) {
            return 0;
        }

        return $today->diffInDays($this->end_date) + 1;
    }
}

