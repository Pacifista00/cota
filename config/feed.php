<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feed Execution Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for feed execution and status management.
    | You can customize these values via environment variables.
    |
    */

    'execution' => [

        /*
        |--------------------------------------------------------------------------
        | Status Update Delay
        |--------------------------------------------------------------------------
        |
        | The delay (in seconds) before automatically updating a PENDING execution
        | to SUCCESS. This simulates the time needed for the device to process
        | the feed command.
        |
        | Default: 3 seconds
        |
        */
        'status_update_delay' => env('FEED_STATUS_UPDATE_DELAY', 3),

        /*
        |--------------------------------------------------------------------------
        | Status Update Timeout
        |--------------------------------------------------------------------------
        |
        | The maximum time (in seconds) to wait before considering an execution
        | as timed out. Executions older than this will be handled differently.
        |
        | Default: 300 seconds (5 minutes)
        |
        */
        'status_update_timeout' => env('FEED_STATUS_UPDATE_TIMEOUT', 300),

        /*
        |--------------------------------------------------------------------------
        | Status Update Retries
        |--------------------------------------------------------------------------
        |
        | The number of times to retry updating an execution status if the job fails.
        | Uses exponential backoff between retries.
        |
        | Default: 3 retries
        |
        */
        'status_update_retries' => env('FEED_STATUS_UPDATE_RETRIES', 3),

        /*
        |--------------------------------------------------------------------------
        | Cleanup Pending After
        |--------------------------------------------------------------------------
        |
        | The time (in minutes) after which pending executions should be cleaned up
        | by the fallback cleanup command. This acts as a safety net.
        |
        | Default: 10 minutes
        |
        */
        'cleanup_pending_after' => env('FEED_CLEANUP_PENDING_AFTER', 10),

        /*
        |--------------------------------------------------------------------------
        | Cleanup Schedule
        |--------------------------------------------------------------------------
        |
        | The cron expression for running the cleanup command. This command acts
        | as a fallback to update any executions that might have been missed.
        |
        | Default: every 5 minutes (cron: * /5 * * * *)
        |
        */
        'cleanup_schedule' => env('FEED_CLEANUP_SCHEDULE', '*/5 * * * *'),

        /*
        |--------------------------------------------------------------------------
        | Enable Auto Status Update
        |--------------------------------------------------------------------------
        |
        | Enable or disable automatic status updates for feed executions.
        | When disabled, statuses must be updated manually or via UI polling.
        |
        | Default: true
        |
        */
        'enable_auto_status_update' => env('FEED_ENABLE_AUTO_STATUS_UPDATE', true),

    ],

    /*
    |--------------------------------------------------------------------------
    | MQTT Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for MQTT connection and timeouts.
    |
    */

    'mqtt' => [

        /*
        |--------------------------------------------------------------------------
        | MQTT Publish Timeout
        |--------------------------------------------------------------------------
        |
        | The maximum time (in seconds) to wait for an MQTT publish operation.
        |
        | Default: 10 seconds
        |
        */
        'publish_timeout' => env('MQTT_PUBLISH_TIMEOUT', 10),

        /*
        |--------------------------------------------------------------------------
        | MQTT Connection Timeout
        |--------------------------------------------------------------------------
        |
        | The maximum time (in seconds) to wait for MQTT connection.
        |
        | Default: 30 seconds
        |
        */
        'connection_timeout' => env('MQTT_CONNECTION_TIMEOUT', 30),

    ],

];
