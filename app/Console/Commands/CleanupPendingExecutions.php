<?php

namespace App\Console\Commands;

use App\Services\FeedStatusUpdaterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupPendingExecutions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:cleanup-pending
                            {--timeout= : Timeout in minutes (default: from config)}
                            {--dry-run : Show what would be updated without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old pending feed executions (fallback safety net)';

    /**
     * The feed status updater service
     */
    protected FeedStatusUpdaterService $statusUpdater;

    /**
     * Create a new command instance.
     */
    public function __construct(FeedStatusUpdaterService $statusUpdater)
    {
        parent::__construct();
        $this->statusUpdater = $statusUpdater;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧹 Starting cleanup of pending feed executions...');
        $this->newLine();

        try {
            // Get timeout from option or config
            $timeoutMinutes = $this->option('timeout')
                ?? config('feed.execution.cleanup_pending_after', 10);

            $dryRun = $this->option('dry-run');

            if ($dryRun) {
                $this->warn('⚠️  DRY RUN MODE - No actual updates will be performed');
                $this->newLine();
            }

            $this->info("⏰ Timeout threshold: {$timeoutMinutes} minutes");
            $this->newLine();

            // Get statistics before cleanup
            $statsBefore = $this->statusUpdater->getExecutionStatistics(1);
            $this->displayStatistics('Before Cleanup', $statsBefore);
            $this->newLine();

            if ($dryRun) {
                $this->info('✅ Dry run completed - no changes made');
                return Command::SUCCESS;
            }

            // Perform cleanup
            $this->info('🔄 Processing timeout executions...');
            $updatedCount = $this->statusUpdater->handleTimeoutExecutions($timeoutMinutes);

            $this->newLine();

            if ($updatedCount > 0) {
                $this->info("✅ Updated {$updatedCount} execution(s) from PENDING to SUCCESS");

                // Get statistics after cleanup
                $statsAfter = $this->statusUpdater->getExecutionStatistics(1);
                $this->newLine();
                $this->displayStatistics('After Cleanup', $statsAfter);

                Log::info('Cleanup pending executions completed', [
                    'updated_count' => $updatedCount,
                    'timeout_minutes' => $timeoutMinutes,
                    'stats_before' => $statsBefore,
                    'stats_after' => $statsAfter,
                ]);
            } else {
                $this->info('✅ No pending executions found that need cleanup');

                Log::info('Cleanup pending executions completed (no updates needed)', [
                    'timeout_minutes' => $timeoutMinutes,
                    'stats' => $statsBefore,
                ]);
            }

            $this->newLine();
            $this->info('🎉 Cleanup completed successfully!');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error during cleanup: ' . $e->getMessage());

            Log::error('Cleanup pending executions failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Display execution statistics in a formatted table
     *
     * @param string $title
     * @param array $stats
     * @return void
     */
    protected function displayStatistics(string $title, array $stats): void
    {
        $this->line("📊 <fg=cyan>{$title} Statistics:</>");

        $tableData = [
            ['Total Executions', $stats['total_executions']],
            ['Pending', "<fg=yellow>{$stats['pending']}</>"],
            ['Success', "<fg=green>{$stats['success']}</>"],
            ['Failed', "<fg=red>{$stats['failed']}</>"],
            ['Success Rate', "{$stats['success_rate']}%"],
            ['Avg Update Time', "{$stats['avg_update_seconds']}s"],
        ];

        $this->table(['Metric', 'Value'], $tableData);
    }
}
