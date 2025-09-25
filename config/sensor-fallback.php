<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sensor Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the sensor fallback
    | mechanism that handles missing or delayed sensor data.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Fallback Trigger Settings
    |--------------------------------------------------------------------------
    */

    // How long to wait (in minutes) before triggering fallback mechanism
    'trigger_delay_minutes' => env('SENSOR_FALLBACK_TRIGGER_DELAY', 2),

    // Data interval expectation (in minutes) - how often data should arrive
    'expected_interval_minutes' => env('SENSOR_EXPECTED_INTERVAL', 1),

    /*
    |--------------------------------------------------------------------------
    | Historical Data Settings
    |--------------------------------------------------------------------------
    */

    // Number of days to look back for historical patterns
    'historical_days' => env('SENSOR_HISTORICAL_DAYS', 7),

    // Time window around target time for historical data (±minutes)
    'time_window_minutes' => env('SENSOR_TIME_WINDOW', 5),

    // Minimum historical data days required before using historical fallback
    'minimum_historical_days' => env('SENSOR_MIN_HISTORICAL_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Late Data Handling
    |--------------------------------------------------------------------------
    */

    // Threshold for determining late data handling strategy (in minutes)
    // < threshold = replace fallback data
    // > threshold = insert as backfilled data
    'late_data_threshold_minutes' => env('SENSOR_LATE_DATA_THRESHOLD', 5),

    /*
    |--------------------------------------------------------------------------
    | Default Fallback Values
    |--------------------------------------------------------------------------
    |
    | These values are used when insufficient historical data is available
    | (first few days of operation or during bootstrap phase)
    |
    */

    'default_values' => [
        'kekeruhan' => [
            'min' => 40.0,
            'max' => 50.0,
        ],
        'keasaman' => [
            'min' => 6.8,
            'max' => 7.2,
        ],
        'suhu' => [
            'min' => 28.0,
            'max' => 30.0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Quality Settings
    |--------------------------------------------------------------------------
    */

    // Add small noise to fallback data for realism (percentage)
    'noise_percentage' => env('SENSOR_NOISE_PERCENTAGE', 0.05), // 5%

    // Enable/disable logging of fallback events
    'enable_logging' => env('SENSOR_FALLBACK_LOGGING', true),

    /*
    |--------------------------------------------------------------------------
    | Job Configuration
    |--------------------------------------------------------------------------
    */

    // Queue name for fallback jobs
    'queue_name' => env('SENSOR_FALLBACK_QUEUE', 'default'),

    // Job timeout in seconds
    'job_timeout' => env('SENSOR_FALLBACK_JOB_TIMEOUT', 300), // 5 minutes

];