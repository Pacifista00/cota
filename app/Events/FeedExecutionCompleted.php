<?php

namespace App\Events;

use App\Models\FeedExecution;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedExecutionCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public FeedExecution $execution
    ) {}
}
