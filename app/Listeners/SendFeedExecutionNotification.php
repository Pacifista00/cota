<?php

namespace App\Listeners;

use App\Events\FeedExecutionCompleted;
use App\Notifications\FeedExecutionNotification;
use Illuminate\Support\Facades\Log;

class SendFeedExecutionNotification
{
    /**
     * Handle the event.
     */
    public function handle(FeedExecutionCompleted $event): void
    {
        $execution = $event->execution;

        // Get user from schedule (if exists)
        if ($execution->schedule && $execution->schedule->user) {
            $user = $execution->schedule->user;

            // Send notification to user
            $user->notify(new FeedExecutionNotification($execution));

            Log::info('Feed execution notification sent', [
                'user_id' => $user->id,
                'execution_id' => $execution->id,
                'schedule_id' => $execution->feed_schedule_id,
                'status' => $execution->status->value,
            ]);
        } else {
            Log::warning('Feed execution notification skipped - no user found', [
                'execution_id' => $execution->id,
                'schedule_id' => $execution->feed_schedule_id,
            ]);
        }
    }
}
