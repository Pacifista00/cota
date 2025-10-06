<?php

namespace App\Console\Commands;

use App\Services\FeedSchedulingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExecuteScheduledFeeds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feed:execute-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all feed schedules that are ready to run';

    /**
     * The feed scheduling service
     */
    protected FeedSchedulingService $feedSchedulingService;

    /**
     * Create a new command instance.
     */
    public function __construct(FeedSchedulingService $feedSchedulingService)
    {
        parent::__construct();
        $this->feedSchedulingService = $feedSchedulingService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Checking for ready feed schedules...');

        try {
            // First, deactivate expired schedules
            $deactivatedCount = $this->feedSchedulingService->deactivateExpiredSchedules();
            if ($deactivatedCount > 0) {
                $this->info("⏰ Deactivated {$deactivatedCount} expired schedule(s)");
            }

            // Execute ready schedules
            $results = $this->feedSchedulingService->executeReadySchedules();

            // Execute missed schedules (catch-up)
            $missedResults = $this->feedSchedulingService->executeMissedSchedules();
            $results = array_merge($results, $missedResults);

            if (empty($results)) {
                $this->info('✅ No schedules ready to execute at this time');
                return Command::SUCCESS;
            }

            $this->info('📋 Found ' . count($results) . ' schedule(s) ready to execute:');
            $this->newLine();

            // Display results in a table
            $tableData = [];
            $successCount = 0;
            $failureCount = 0;

            foreach ($results as $result) {
                $success = $result['result']['success'];
                $status = $success ? '✅ Success' : '❌ Failed';

                if ($success) {
                    $successCount++;
                } else {
                    $failureCount++;
                }

                // Add "MISSED" indicator if this was a catch-up execution
                $scheduleName = $result['schedule_name'];
                if (isset($result['was_missed']) && $result['was_missed']) {
                    $scheduleName .= ' (MISSED - Catch-up)';
                }

                $tableData[] = [
                    $result['schedule_id'],
                    $scheduleName,
                    $status,
                    $result['result']['message'],
                ];
            }

            $this->table(
                ['ID', 'Schedule Name', 'Status', 'Message'],
                $tableData
            );

            $this->newLine();
            $this->info("✅ Successful: {$successCount}");
            if ($failureCount > 0) {
                $this->warn("❌ Failed: {$failureCount}");
            }

            Log::info('Scheduled feeds executed', [
                'total' => count($results),
                'successful' => $successCount,
                'failed' => $failureCount,
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error executing scheduled feeds: ' . $e->getMessage());
            Log::error('Error executing scheduled feeds', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return Command::FAILURE;
        }
    }
}

