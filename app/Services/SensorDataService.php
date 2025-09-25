<?php

namespace App\Services;

use App\Models\Sensor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class SensorDataService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('sensor-fallback');
    }

    /**
     * Handle delayed data arrival
     */
    public function handleDelayedData(array $sensorData, Carbon $actualReadingTime): void
    {
        $delayMinutes = $actualReadingTime->diffInMinutes(Carbon::now());
        $threshold = $this->config['late_data_threshold_minutes'];

        if ($delayMinutes <= $threshold) {
            // Replace existing fallback data if exists
            $this->replaceFallbackData($sensorData, $actualReadingTime);
        } else {
            // Insert as backfilled data
            $this->insertBackfilledData($sensorData, $actualReadingTime);
        }

        if ($this->config['enable_logging']) {
            Log::info('Delayed sensor data processed', [
                'delay_minutes' => $delayMinutes,
                'handling_strategy' => $delayMinutes <= $threshold ? 'replace' : 'backfill',
                'reading_time' => $actualReadingTime->toDateTimeString(),
            ]);
        }
    }

    /**
     * Replace fallback data with real delayed data
     */
    private function replaceFallbackData(array $sensorData, Carbon $actualReadingTime): void
    {
        // Find existing fallback data within ±30 seconds of the actual reading time
        $existingFallback = Sensor::where('data_source', 'FALLBACK')
            ->where('created_at', '>=', $actualReadingTime->copy()->subSeconds(30))
            ->where('created_at', '<=', $actualReadingTime->copy()->addSeconds(30))
            ->first();

        if ($existingFallback) {
            // Update existing record with real data
            $existingFallback->update([
                'kekeruhan' => $sensorData['kekeruhan'],
                'keasaman' => $sensorData['keasaman'],
                'suhu' => $sensorData['suhu'],
                'data_source' => 'REAL_TIME',
                'is_estimated' => false,
                'reading_timestamp' => $actualReadingTime,
                'updated_at' => Carbon::now(),
            ]);
        } else {
            // No fallback found, insert as real-time data
            $this->insertRealTimeData($sensorData, $actualReadingTime);
        }
    }

    /**
     * Insert backfilled data (very late arrival)
     */
    private function insertBackfilledData(array $sensorData, Carbon $actualReadingTime): void
    {
        Sensor::create([
            'kekeruhan' => $sensorData['kekeruhan'],
            'keasaman' => $sensorData['keasaman'],
            'suhu' => $sensorData['suhu'],
            'data_source' => 'BACKFILLED',
            'is_estimated' => false,
            'reading_timestamp' => $actualReadingTime,
            'created_at' => $actualReadingTime,
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Insert real-time data
     */
    private function insertRealTimeData(array $sensorData, Carbon $readingTime): void
    {
        Sensor::create([
            'kekeruhan' => $sensorData['kekeruhan'],
            'keasaman' => $sensorData['keasaman'],
            'suhu' => $sensorData['suhu'],
            'data_source' => 'REAL_TIME',
            'is_estimated' => false,
            'reading_timestamp' => $readingTime,
            'created_at' => $readingTime,
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Enhanced historical average calculation with refined time window
     */
    public function calculateSmartHistoricalAverage(Carbon $targetTime): ?array
    {
        $historicalDays = $this->config['historical_days'];
        $minimumDays = $this->config['minimum_historical_days'];

        // Refined time window: ±5 minutes with priority for exact time matches
        $historicalData = $this->getHistoricalDataWithPriority($targetTime, $historicalDays);

        // Check if we have enough historical data
        $uniqueDays = $historicalData->groupBy(function($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d');
        })->count();

        if ($uniqueDays < $minimumDays) {
            return null; // Not enough historical data
        }

        // Weight data based on time proximity and day of week similarity
        $weightedData = $this->calculateWeightedAverages($historicalData, $targetTime);

        return $this->applySmartNoise($weightedData, $historicalData);
    }

    /**
     * Get historical data with priority for closer time matches
     */
    private function getHistoricalDataWithPriority(Carbon $targetTime, int $days): Collection
    {
        $data = collect();

        // Priority 1: Exact minute matches (±30 seconds)
        $exactMatches = $this->getHistoricalDataInWindow($targetTime, $days, 0, 0.5);
        $data = $data->merge($exactMatches);

        // Priority 2: ±2 minute window
        if ($data->count() < 7) { // If we don't have enough exact matches
            $closeMatches = $this->getHistoricalDataInWindow($targetTime, $days, 2, 2);
            $data = $data->merge($closeMatches);
        }

        // Priority 3: ±5 minute window
        if ($data->count() < 5) { // Still not enough data
            $wideMatches = $this->getHistoricalDataInWindow($targetTime, $days, 5, 5);
            $data = $data->merge($wideMatches);
        }

        return $data->unique('id');
    }

    /**
     * Get historical data within a specific time window
     */
    private function getHistoricalDataInWindow(Carbon $targetTime, int $days, int $beforeMinutes, int $afterMinutes): Collection
    {
        $startTime = $targetTime->copy()->subMinutes($beforeMinutes);
        $endTime = $targetTime->copy()->addMinutes($afterMinutes);

        return Sensor::where('data_source', 'REAL_TIME')
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('created_at', '<', $targetTime) // Don't include future data
            ->whereTime('created_at', '>=', $startTime->format('H:i:s'))
            ->whereTime('created_at', '<=', $endTime->format('H:i:s'))
            ->get();
    }

    /**
     * Calculate weighted averages based on time proximity and day similarity
     */
    private function calculateWeightedAverages(Collection $historicalData, Carbon $targetTime): array
    {
        $totalWeight = 0;
        $weightedSums = ['kekeruhan' => 0, 'keasaman' => 0, 'suhu' => 0];

        foreach ($historicalData as $record) {
            $recordTime = Carbon::parse($record->created_at);

            // Time proximity weight (higher for closer times)
            $timeDiffMinutes = abs($targetTime->diffInMinutes($recordTime->setDate($targetTime->year, $targetTime->month, $targetTime->day)));
            $timeWeight = max(0.1, 1 - ($timeDiffMinutes / 60)); // Decay over 1 hour

            // Day of week similarity weight
            $dayWeight = $recordTime->dayOfWeek === $targetTime->dayOfWeek ? 1.2 : 1.0;

            // Recency weight (more recent data is more relevant)
            $daysDiff = $targetTime->diffInDays($recordTime);
            $recencyWeight = max(0.5, 1 - ($daysDiff / 30)); // Decay over 30 days

            $combinedWeight = $timeWeight * $dayWeight * $recencyWeight;
            $totalWeight += $combinedWeight;

            $weightedSums['kekeruhan'] += $record->kekeruhan * $combinedWeight;
            $weightedSums['keasaman'] += $record->keasaman * $combinedWeight;
            $weightedSums['suhu'] += $record->suhu * $combinedWeight;
        }

        if ($totalWeight == 0) {
            return ['kekeruhan' => 0, 'keasaman' => 0, 'suhu' => 0];
        }

        return [
            'kekeruhan' => $weightedSums['kekeruhan'] / $totalWeight,
            'keasaman' => $weightedSums['keasaman'] / $totalWeight,
            'suhu' => $weightedSums['suhu'] / $totalWeight,
        ];
    }

    /**
     * Apply smart noise injection based on historical variance
     */
    private function applySmartNoise(array $averages, Collection $historicalData): array
    {
        $variance = $this->calculateVariance($historicalData);

        return [
            'kekeruhan' => $this->addAdaptiveNoise($averages['kekeruhan'], $variance['kekeruhan']),
            'keasaman' => $this->addAdaptiveNoise($averages['keasaman'], $variance['keasaman']),
            'suhu' => $this->addAdaptiveNoise($averages['suhu'], $variance['suhu']),
        ];
    }

    /**
     * Calculate variance for each sensor parameter
     */
    private function calculateVariance(Collection $historicalData): array
    {
        return [
            'kekeruhan' => $this->getVariance($historicalData->pluck('kekeruhan')),
            'keasaman' => $this->getVariance($historicalData->pluck('keasaman')),
            'suhu' => $this->getVariance($historicalData->pluck('suhu')),
        ];
    }

    /**
     * Calculate variance for a collection of values
     */
    private function getVariance(Collection $values): float
    {
        if ($values->count() < 2) return 0.1; // Default small variance

        $mean = $values->avg();
        $squaredDiffs = $values->map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        });

        return sqrt($squaredDiffs->avg()); // Standard deviation
    }

    /**
     * Add adaptive noise based on historical variance
     */
    private function addAdaptiveNoise(float $value, float $variance): float
    {
        // Use historical variance to determine noise level, but cap it
        $noiseLevel = min($variance * 0.3, $value * 0.1); // Max 10% of value or 30% of variance
        $noise = $noiseLevel * (mt_rand(-100, 100) / 100);

        return round($value + $noise, 2);
    }

    /**
     * Get data quality statistics
     */
    public function getDataQualityStats(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Sensor::query();

        if ($startDate) $query->where('created_at', '>=', $startDate);
        if ($endDate) $query->where('created_at', '<=', $endDate);

        $total = $query->count();
        $realTime = $query->clone()->where('data_source', 'REAL_TIME')->count();
        $fallback = $query->clone()->where('data_source', 'FALLBACK')->count();
        $backfilled = $query->clone()->where('data_source', 'BACKFILLED')->count();

        return [
            'total_records' => $total,
            'real_time_percentage' => $total > 0 ? round(($realTime / $total) * 100, 2) : 0,
            'fallback_percentage' => $total > 0 ? round(($fallback / $total) * 100, 2) : 0,
            'backfilled_percentage' => $total > 0 ? round(($backfilled / $total) * 100, 2) : 0,
            'data_quality_score' => $total > 0 ? round((($realTime + $backfilled) / $total) * 100, 2) : 0,
        ];
    }
}