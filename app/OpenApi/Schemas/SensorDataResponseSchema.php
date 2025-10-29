<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SensorDataResponse',
    title: 'Sensor Data Response',
    description: 'Sensor data response with single reading',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Data sensor berhasil ditambahkan!'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 201
        ),
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'kekeruhan', type: 'number', format: 'float', example: 45.5),
                new OA\Property(property: 'keasaman', type: 'number', format: 'float', example: 7.2),
                new OA\Property(property: 'suhu', type: 'number', format: 'float', example: 28.5),
                new OA\Property(property: 'waktu', type: 'string', format: 'date-time', example: '2024-01-15 08:00:00'),
                new OA\Property(property: 'data_source', type: 'string', example: 'REAL_TIME'),
                new OA\Property(property: 'is_estimated', type: 'boolean', example: false),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class SensorDataResponseSchema
{
}

