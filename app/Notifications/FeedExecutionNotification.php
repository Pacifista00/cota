<?php

namespace App\Notifications;

use App\Enums\FeedExecutionStatus;
use App\Models\FeedExecution;
use Illuminate\Notifications\Notification;

class FeedExecutionNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        private FeedExecution $execution
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database']; // Can add 'broadcast' later for real-time
    }

    /**
     * Get the array representation of the notification for database.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'type' => 'feed_execution',
            'status' => $this->execution->status->value,
            'feed_execution' => [
                'id' => $this->execution->id,
                'schedule_id' => $this->execution->feed_schedule_id,
                'schedule_name' => $this->execution->schedule?->name,
                'trigger_type' => $this->execution->trigger_type,
                'executed_at' => $this->execution->executed_at->format('Y-m-d H:i:s'),
            ],
            'action_url' => "/riwayat/pakan?execution_id={$this->execution->id}",
            'icon' => $this->getIcon(),
            'color' => $this->execution->status->color(),
        ];
    }

    /**
     * Get notification title based on status
     */
    private function getTitle(): string
    {
        return match($this->execution->status) {
            FeedExecutionStatus::SUCCESS => 'Jadwal Pakan Berhasil Dieksekusi',
            FeedExecutionStatus::FAILED => 'Jadwal Pakan Gagal Dieksekusi',
            FeedExecutionStatus::PENDING => 'Jadwal Pakan Sedang Diproses',
        };
    }

    /**
     * Get notification message based on status
     */
    private function getMessage(): string
    {
        $scheduleName = $this->execution->schedule?->name ?? 'Pakan Manual';
        $time = $this->execution->executed_at->format('H:i');

        return match($this->execution->status) {
            FeedExecutionStatus::SUCCESS => "Jadwal '{$scheduleName}' berhasil dieksekusi pada pukul {$time}",
            FeedExecutionStatus::FAILED => "Jadwal '{$scheduleName}' gagal dieksekusi pada pukul {$time}",
            FeedExecutionStatus::PENDING => "Jadwal '{$scheduleName}' sedang diproses pada pukul {$time}",
        };
    }

    /**
     * Get icon based on status
     */
    private function getIcon(): string
    {
        return match($this->execution->status) {
            FeedExecutionStatus::SUCCESS => 'check-bold',
            FeedExecutionStatus::FAILED => 'fat-remove',
            FeedExecutionStatus::PENDING => 'time-alarm',
        };
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
