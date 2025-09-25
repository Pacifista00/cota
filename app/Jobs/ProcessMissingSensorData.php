<?php

namespace App\Jobs;

use App\Models\Sensor;
use App\Services\SensorDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessMissingSensorData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout;
    private SensorDataService $sensorDataService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue(config('sensor-fallback.queue_name', 'default'));
        $this->timeout = config('sensor-fallback.job_timeout', 300);
        $this->sensorDataService = new SensorDataService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $config = config('sensor-fallback');
        $triggerDelayMinutes = $config['trigger_delay_minutes'];
        $expectedIntervalMinutes = $config['expected_interval_minutes'];

        // Get current time and check for missing data
        $now = Carbon::now();
        $checkTime = $now->subMinutes($triggerDelayMinutes);

        // Find missing data slots
        $missingSlots = $this->findMissingDataSlots($checkTime, $expectedIntervalMinutes);

        foreach ($missingSlots as $missingTime) {
            $this->generateFallbackData($missingTime, $config);
        }

        if (count($missingSlots) > 0 && $config['enable_logging']) {
            Log::info('Sensor fallback processed', [
                'missing_slots_count' => count($missingSlots),
                'check_time' => $checkTime->toDateTimeString(),
            ]);
        }
    }

    /**
     * Find missing data time slots
     */
    private function findMissingDataSlots(Carbon $checkTime, int $intervalMinutes): array
    {
        $missingSlots = [];

        // Check last few expected data points
        for ($i = 1; $i <= 3; $i++) {
            $expectedTime = $checkTime->copy()->subMinutes($intervalMinutes * $i);

            // Round to nearest minute for consistent checking
            $expectedTime->second(0);

            $exists = Sensor::where('created_at', '>=', $expectedTime->copy()->subSeconds(30))
                           ->where('created_at', '<=', $expectedTime->copy()->addSeconds(30))
                           ->exists();

            if (!$exists) {
                $missingSlots[] = $expectedTime->copy();
            }
        }

        return $missingSlots;
    }

    /**
     * Generate fallback data for missing time slot
     */
    private function generateFallbackData(Carbon $missingTime, array $config): void
    {
        // Use smart historical average calculation
        $fallbackData = $this->sensorDataService->calculateSmartHistoricalAverage($missingTime);

        if ($fallbackData === null) {
            $fallbackData = $this->getDefaultFallbackValues($config);
        }

        // Create sensor record with fallback data
        Sensor::create([
            'kekeruhan' => $fallbackData['kekeruhan'],
            'keasaman' => $fallbackData['keasaman'],
            'suhu' => $fallbackData['suhu'],
            'data_source' => 'FALLBACK',
            'is_estimated' => true,
            'reading_timestamp' => $missingTime,
            'created_at' => $missingTime,
            'updated_at' => Carbon::now(),
        ]);
    }


    /**
     * Get default fallback values when insufficient historical data
     */
    private function getDefaultFallbackValues(array $config): array
    {
        $defaults = $config['default_values'];

        return [
            'kekeruhan' => mt_rand(
                $defaults['kekeruhan']['min'] * 100,
                $defaults['kekeruhan']['max'] * 100
            ) / 100,
            'keasaman' => mt_rand(
                $defaults['keasaman']['min'] * 100,
                $defaults['keasaman']['max'] * 100
            ) / 100,
            'suhu' => mt_rand(
                $defaults['suhu']['min'] * 100,
                $defaults['suhu']['max'] * 100
            ) / 100,
        ];
    }

}
