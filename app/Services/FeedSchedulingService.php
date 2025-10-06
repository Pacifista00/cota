<?php

namespace App\Services;

use App\Enums\FeedExecutionStatus;
use App\Models\FeedSchedule;
use App\Models\FeedExecution;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class FeedSchedulingService
{
    /**
     * MQTT Configuration
     */
    private const MQTT_SERVER = 'chameleon.lmq.cloudamqp.com';
    private const MQTT_PORT = 8883;
    private const MQTT_USERNAME = 'anfvrqjy:anfvrqjy';
    private const MQTT_PASSWORD = 'V4OJdwnNv8d8nN2OmCbLrdBqDF5-WS5G';
    private const MQTT_TOPIC = 'cota/command/feed_all';

    /**
     * Create a new feed schedule
     */
    public function createSchedule(array $data): FeedSchedule
    {
        return DB::transaction(function () use ($data) {
            // Set defaults
            $scheduleData = [
                'user_id' => $data['user_id'] ?? null,
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'waktu_pakan' => $data['waktu_pakan'],
                'start_date' => $data['start_date'] ?? Carbon::today(),
                'end_date' => $data['end_date'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'frequency_type' => $data['frequency_type'] ?? 'daily',
                'frequency_data' => $data['frequency_data'] ?? null,
            ];

            return FeedSchedule::create($scheduleData);
        });
    }

    /**
     * Update an existing feed schedule
     */
    public function updateSchedule(FeedSchedule $schedule, array $data): FeedSchedule
    {
        return DB::transaction(function () use ($schedule, $data) {
            $schedule->update(array_filter($data, fn($value) => $value !== null));
            return $schedule->fresh();
        });
    }

    /**
     * Activate a schedule
     */
    public function activateSchedule(FeedSchedule $schedule): FeedSchedule
    {
        $schedule->update(['is_active' => true]);
        return $schedule->fresh();
    }

    /**
     * Deactivate a schedule
     */
    public function deactivateSchedule(FeedSchedule $schedule): FeedSchedule
    {
        $schedule->update(['is_active' => false]);
        return $schedule->fresh();
    }

    /**
     * Delete a schedule
     */
    public function deleteSchedule(FeedSchedule $schedule): bool
    {
        return DB::transaction(function () use ($schedule) {
            // Optionally, you might want to keep executions for historical purposes
            // $schedule->executions()->delete();
            
            return $schedule->delete();
        });
    }

    /**
     * Get all schedules that are ready to execute
     */
    public function getReadySchedules(): \Illuminate\Database\Eloquent\Collection
    {
        return FeedSchedule::readyToExecute()->get();
    }

    /**
     * Execute feed for a specific schedule
     */
    public function executeFeed(FeedSchedule $schedule): array
    {
        // Check if schedule is valid
        if (!$schedule->isValid()) {
            return [
                'success' => false,
                'message' => 'Jadwal tidak aktif atau sudah melewati masa berlaku.',
            ];
        }

        // Check if already executed today
        if ($schedule->wasExecutedToday()) {
            return [
                'success' => false,
                'message' => 'Jadwal ini sudah dieksekusi hari ini.',
            ];
        }

        return DB::transaction(function () use ($schedule) {
            try {
                // Send MQTT command
                $this->sendMqttFeedCommand();

                // Create execution record
                $execution = FeedExecution::create([
                    'feed_schedule_id' => $schedule->id,
                    'trigger_type' => 'scheduled',
                    'status' => FeedExecutionStatus::PENDING->value,
                    'executed_at' => now(),
                ]);

                // Mark schedule as executed
                $schedule->markAsExecuted();

                // Log success
                Log::info("Feed schedule executed successfully", [
                    'schedule_id' => $schedule->id,
                    'execution_id' => $execution->id,
                ]);

                return [
                    'success' => true,
                    'message' => 'Perintah pakan terjadwal berhasil dikirim!',
                    'execution' => $execution,
                ];
            } catch (Exception $e) {
                // Log error
                Log::error("Failed to execute feed schedule", [
                    'schedule_id' => $schedule->id,
                    'error' => $e->getMessage(),
                ]);

                // Create failed execution record
                FeedExecution::create([
                    'feed_schedule_id' => $schedule->id,
                    'trigger_type' => 'scheduled',
                    'status' => FeedExecutionStatus::FAILED->value,
                    'executed_at' => now(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal mengirim perintah pakan: ' . $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Execute all ready schedules
     */
    public function executeReadySchedules(): array
    {
        $readySchedules = $this->getReadySchedules();
        $results = [];

        foreach ($readySchedules as $schedule) {
            $results[] = [
                'schedule_id' => $schedule->id,
                'schedule_name' => $schedule->name ?? "Schedule #{$schedule->id}",
                'result' => $this->executeFeed($schedule),
            ];
        }

        return $results;
    }

    /**
     * Execute manual feed (not from schedule)
     */
    public function executeManualFeed(): array
    {
        return DB::transaction(function () {
            try {
                // Send MQTT command
                $this->sendMqttFeedCommand();

                // Create execution record
                $execution = FeedExecution::create([
                    'feed_schedule_id' => null,
                    'trigger_type' => 'manual',
                    'status' => FeedExecutionStatus::PENDING->value,
                    'executed_at' => now(),
                ]);

                Log::info("Manual feed executed successfully", [
                    'execution_id' => $execution->id,
                ]);

                return [
                    'success' => true,
                    'message' => 'Perintah pakan manual berhasil dikirim!',
                    'execution' => $execution,
                ];
            } catch (Exception $e) {
                Log::error("Failed to execute manual feed", [
                    'error' => $e->getMessage(),
                ]);

                // Create failed execution record
                FeedExecution::create([
                    'feed_schedule_id' => null,
                    'trigger_type' => 'manual',
                    'status' => FeedExecutionStatus::FAILED->value,
                    'executed_at' => now(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal mengirim perintah pakan: ' . $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Send MQTT feed command
     */
    private function sendMqttFeedCommand(): void
    {
        $clientId = 'laravel-client-' . rand();

        // Configure MQTT connection
        $connectionSettings = (new ConnectionSettings)
            ->setUseTls(true)
            ->setTlsSelfSignedAllowed(true)
            ->setUsername(self::MQTT_USERNAME)
            ->setPassword(self::MQTT_PASSWORD);

        // Connect and publish
        $mqtt = new MqttClient(self::MQTT_SERVER, self::MQTT_PORT, $clientId);
        $mqtt->connect($connectionSettings, true);
        $mqtt->publish(self::MQTT_TOPIC, 'FEED', MqttClient::QOS_AT_LEAST_ONCE);
        $mqtt->disconnect();
    }

    /**
     * Get schedule statistics
     */
    public function getScheduleStatistics(FeedSchedule $schedule): array
    {
        $totalExecutions = $schedule->executions()->count();
        $successfulExecutions = $schedule->executions()->successful()->count();
        $failedExecutions = $schedule->executions()->failed()->count();
        $successRate = $totalExecutions > 0 
            ? round(($successfulExecutions / $totalExecutions) * 100, 2)
            : 0;

        return [
            'total_executions' => $totalExecutions,
            'successful_executions' => $successfulExecutions,
            'failed_executions' => $failedExecutions,
            'success_rate' => $successRate,
            'last_executed_at' => $schedule->last_executed_at?->format('Y-m-d H:i:s'),
            'next_execution' => $schedule->next_execution?->format('Y-m-d H:i:s'),
            'remaining_days' => $schedule->remaining_days,
            'is_active' => $schedule->is_active,
            'is_valid' => $schedule->isValid(),
        ];
    }

    /**
     * Get user's active schedules
     */
    public function getUserActiveSchedules(?int $userId = null)
    {
        $query = FeedSchedule::active()->shouldRunToday();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->with(['executions' => function ($q) {
            $q->latest()->limit(5);
        }])->get();
    }

    /**
     * Check and auto-deactivate expired schedules
     */
    public function deactivateExpiredSchedules(): int
    {
        $today = Carbon::today();
        
        return FeedSchedule::active()
            ->whereNotNull('end_date')
            ->where('end_date', '<', $today)
            ->update(['is_active' => false]);
    }
}

