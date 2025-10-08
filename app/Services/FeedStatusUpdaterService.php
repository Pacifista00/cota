<?php

namespace App\Services;

use App\Enums\FeedExecutionStatus;
use App\Models\FeedExecution;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FeedStatusUpdaterService
{
    /**
     * Update the status of a single feed execution
     *
     * @param FeedExecution $execution
     * @param FeedExecutionStatus $status
     * @param array $metadata Additional metadata to log
     * @return bool
     */
    public function updateExecutionStatus(
        FeedExecution $execution,
        FeedExecutionStatus $status,
        array $metadata = []
    ): bool {
        try {
            return DB::transaction(function () use ($execution, $status, $metadata) {
                $oldStatus = $execution->status;

                // Update the status
                $execution->update(['status' => $status->value]);

                // Log the status change
                Log::info('Feed execution status updated', [
                    'execution_id' => $execution->id,
                    'feed_schedule_id' => $execution->feed_schedule_id,
                    'trigger_type' => $execution->trigger_type,
                    'old_status' => $oldStatus?->value,
                    'new_status' => $status->value,
                    'executed_at' => $execution->executed_at?->toIso8601String(),
                    'updated_at' => $execution->updated_at?->toIso8601String(),
                    'time_to_update_seconds' => $execution->created_at->diffInSeconds($execution->updated_at),
                    'metadata' => $metadata,
                ]);

                return true;
            });
        } catch (Exception $e) {
            Log::error('Failed to update feed execution status', [
                'execution_id' => $execution->id,
                'target_status' => $status->value,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Update all pending executions that are older than specified seconds
     *
     * @param int $olderThanSeconds Minimum age in seconds
     * @return int Number of executions updated
     */
    public function updatePendingExecutions(int $olderThanSeconds = 3): int
    {
        $threshold = Carbon::now()->subSeconds($olderThanSeconds);
        $updatedCount = 0;

        Log::info('Starting batch update of pending executions', [
            'older_than_seconds' => $olderThanSeconds,
            'threshold' => $threshold->toIso8601String(),
        ]);

        try {
            // Get all pending executions older than threshold
            $pendingExecutions = FeedExecution::where('status', FeedExecutionStatus::PENDING->value)
                ->where('created_at', '<=', $threshold)
                ->get();

            Log::info('Found pending executions to update', [
                'count' => $pendingExecutions->count(),
                'execution_ids' => $pendingExecutions->pluck('id')->toArray(),
            ]);

            foreach ($pendingExecutions as $execution) {
                if ($this->shouldUpdateExecution($execution)) {
                    $success = $this->updateExecutionStatus(
                        $execution,
                        FeedExecutionStatus::SUCCESS,
                        [
                            'update_type' => 'batch_auto_update',
                            'age_seconds' => $execution->created_at->diffInSeconds(now()),
                        ]
                    );

                    if ($success) {
                        $updatedCount++;
                    }
                }
            }

            Log::info('Batch update completed', [
                'updated_count' => $updatedCount,
                'total_found' => $pendingExecutions->count(),
            ]);

            return $updatedCount;
        } catch (Exception $e) {
            Log::error('Failed to batch update pending executions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $updatedCount;
        }
    }

    /**
     * Handle timeout executions (older than timeout threshold)
     * Updates them to SUCCESS as fallback
     *
     * @param int $timeoutMinutes Timeout in minutes
     * @return int Number of executions updated
     */
    public function handleTimeoutExecutions(int $timeoutMinutes = 10): int
    {
        $threshold = Carbon::now()->subMinutes($timeoutMinutes);
        $updatedCount = 0;

        Log::warning('Handling timeout executions (fallback cleanup)', [
            'timeout_minutes' => $timeoutMinutes,
            'threshold' => $threshold->toIso8601String(),
        ]);

        try {
            // Get all pending executions older than timeout
            $timeoutExecutions = FeedExecution::where('status', FeedExecutionStatus::PENDING->value)
                ->where('created_at', '<=', $threshold)
                ->get();

            Log::warning('Found timeout executions', [
                'count' => $timeoutExecutions->count(),
                'execution_ids' => $timeoutExecutions->pluck('id')->toArray(),
            ]);

            foreach ($timeoutExecutions as $execution) {
                $success = $this->updateExecutionStatus(
                    $execution,
                    FeedExecutionStatus::SUCCESS,
                    [
                        'update_type' => 'timeout_fallback_cleanup',
                        'age_minutes' => $execution->created_at->diffInMinutes(now()),
                        'reason' => 'Execution exceeded timeout threshold, assuming success',
                    ]
                );

                if ($success) {
                    $updatedCount++;
                }
            }

            Log::info('Timeout handling completed', [
                'updated_count' => $updatedCount,
                'total_found' => $timeoutExecutions->count(),
            ]);

            return $updatedCount;
        } catch (Exception $e) {
            Log::error('Failed to handle timeout executions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $updatedCount;
        }
    }

    /**
     * Check if an execution should be updated
     * (not already updated, old enough, etc.)
     *
     * @param FeedExecution $execution
     * @return bool
     */
    public function shouldUpdateExecution(FeedExecution $execution): bool
    {
        // Only update if status is still PENDING
        if ($execution->status !== FeedExecutionStatus::PENDING) {
            Log::debug('Execution already updated, skipping', [
                'execution_id' => $execution->id,
                'current_status' => $execution->status->value,
            ]);
            return false;
        }

        // Check if enough time has passed
        $minAge = config('feed.execution.status_update_delay', 3);
        $age = $execution->created_at->diffInSeconds(now());

        if ($age < $minAge) {
            Log::debug('Execution too recent, skipping', [
                'execution_id' => $execution->id,
                'age_seconds' => $age,
                'min_age_seconds' => $minAge,
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get statistics about execution statuses
     *
     * @param int $days Number of days to look back
     * @return array
     */
    public function getExecutionStatistics(int $days = 7): array
    {
        $since = Carbon::now()->subDays($days);

        $stats = FeedExecution::where('created_at', '>=', $since)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed,
                AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_update_seconds
            ', [
                FeedExecutionStatus::PENDING->value,
                FeedExecutionStatus::SUCCESS->value,
                FeedExecutionStatus::FAILED->value,
            ])
            ->first();

        return [
            'total_executions' => $stats->total ?? 0,
            'pending' => $stats->pending ?? 0,
            'success' => $stats->success ?? 0,
            'failed' => $stats->failed ?? 0,
            'success_rate' => $stats->total > 0
                ? round(($stats->success / $stats->total) * 100, 2)
                : 0,
            'avg_update_seconds' => round($stats->avg_update_seconds ?? 0, 2),
            'period_days' => $days,
        ];
    }

    /**
     * Update a single execution by ID (for use in jobs)
     *
     * @param int $executionId
     * @param FeedExecutionStatus $status
     * @param array $metadata
     * @return bool
     */
    public function updateExecutionById(
        int $executionId,
        FeedExecutionStatus $status = FeedExecutionStatus::SUCCESS,
        array $metadata = []
    ): bool {
        try {
            $execution = FeedExecution::find($executionId);

            if (!$execution) {
                Log::warning('Execution not found for status update', [
                    'execution_id' => $executionId,
                    'target_status' => $status->value,
                ]);
                return false;
            }

            return $this->updateExecutionStatus($execution, $status, $metadata);
        } catch (Exception $e) {
            Log::error('Failed to update execution by ID', [
                'execution_id' => $executionId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
