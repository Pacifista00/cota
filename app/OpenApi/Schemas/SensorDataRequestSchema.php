<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SensorDataRequest',
    title: 'Sensor Data Request',
    description: 'Request payload for inserting sensor data',
    required: ['kekeruhan', 'keasaman', 'suhu'],
    properties: [
        new OA\Property(
            property: 'kekeruhan',
            description: 'Turbidity value (NTU)',
            type: 'number',
            format: 'float',
            example: 45.5
        ),
        new OA\Property(
            property: 'keasaman',
            description: 'pH value',
            type: 'number',
            format: 'float',
            example: 7.2
        ),
        new OA\Property(
            property: 'suhu',
            description: 'Temperature (°C)',
            type: 'number',
            format: 'float',
            example: 28.5
        ),
        new OA\Property(
            property: 'reading_timestamp',
            description: 'Timestamp when sensor reading was taken (ISO 8601)',
            type: 'string',
            format: 'date-time',
            example: '2024-01-15T08:00:00Z'
        ),
    ],
    type: 'object'
)]
class SensorDataRequestSchema
{
}

