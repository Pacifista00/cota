<?php

namespace App\Observers;

use App\Events\FeedExecutionCompleted;
use App\Models\FeedExecution;

class FeedExecutionObserver
{
    /**
     * Handle the FeedExecution "created" event.
     */
    public function created(FeedExecution $execution): void
    {
        // Fire event to trigger notification
        event(new FeedExecutionCompleted($execution));
    }

    /**
     * Handle the FeedExecution "updated" event.
     */
    public function updated(FeedExecution $execution): void
    {
        // Fire event if status changed
        if ($execution->wasChanged('status')) {
            event(new FeedExecutionCompleted($execution));
        }
    }
}
