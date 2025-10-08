<?php

namespace App\Jobs;

use App\Enums\FeedExecutionStatus;
use App\Services\FeedStatusUpdaterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateFeedExecutionStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [10, 30, 60];

    /**
     * The feed execution ID to update
     *
     * @var int
     */
    protected int $executionId;

    /**
     * The target status
     *
     * @var FeedExecutionStatus
     */
    protected FeedExecutionStatus $targetStatus;

    /**
     * Additional metadata
     *
     * @var array
     */
    protected array $metadata;

    /**
     * Create a new job instance.
     *
     * @param int $executionId
     * @param FeedExecutionStatus $targetStatus
     * @param array $metadata
     */
    public function __construct(
        int $executionId,
        FeedExecutionStatus $targetStatus = FeedExecutionStatus::SUCCESS,
        array $metadata = []
    ) {
        $this->executionId = $executionId;
        $this->targetStatus = $targetStatus;
        $this->metadata = array_merge($metadata, [
            'update_source' => 'queued_job',
            'job_class' => self::class,
        ]);

        // Get retry count from config
        $this->tries = config('feed.execution.status_update_retries', 3);

        Log::info('UpdateFeedExecutionStatusJob created', [
            'execution_id' => $this->executionId,
            'target_status' => $this->targetStatus->value,
            'tries' => $this->tries,
            'metadata' => $this->metadata,
        ]);
    }

    /**
     * Execute the job.
     *
     * @param FeedStatusUpdaterService $service
     * @return void
     */
    public function handle(FeedStatusUpdaterService $service): void
    {
        Log::info('UpdateFeedExecutionStatusJob starting', [
            'execution_id' => $this->executionId,
            'target_status' => $this->targetStatus->value,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
        ]);

        try {
            $success = $service->updateExecutionById(
                $this->executionId,
                $this->targetStatus,
                array_merge($this->metadata, [
                    'job_attempt' => $this->attempts(),
                    'job_id' => $this->job->uuid(),
                ])
            );

            if ($success) {
                Log::info('UpdateFeedExecutionStatusJob completed successfully', [
                    'execution_id' => $this->executionId,
                    'target_status' => $this->targetStatus->value,
                    'attempt' => $this->attempts(),
                ]);
            } else {
                Log::warning('UpdateFeedExecutionStatusJob failed to update', [
                    'execution_id' => $this->executionId,
                    'target_status' => $this->targetStatus->value,
                    'attempt' => $this->attempts(),
                    'reason' => 'Service returned false',
                ]);

                // If not successful and we have more attempts, throw exception to retry
                if ($this->attempts() < $this->tries) {
                    throw new \Exception('Failed to update execution status, will retry');
                }
            }
        } catch (Throwable $e) {
            Log::error('UpdateFeedExecutionStatusJob encountered error', [
                'execution_id' => $this->executionId,
                'target_status' => $this->targetStatus->value,
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        Log::error('UpdateFeedExecutionStatusJob failed permanently', [
            'execution_id' => $this->executionId,
            'target_status' => $this->targetStatus->value,
            'attempts' => $this->attempts(),
            'max_tries' => $this->tries,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Optionally: Send notification, create alert, etc.
        // For now, we just log the permanent failure
        // The fallback cleanup command will handle this execution later
    }

    /**
     * Get the tags for the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return [
            'feed-execution',
            'status-update',
            "execution:{$this->executionId}",
            "status:{$this->targetStatus->value}",
        ];
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(5);
    }
}
