<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SensorHistoryResponse',
    title: 'Sensor History Response',
    description: 'Sensor data history response with pagination',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Data history sensor berhasil dimuat.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
        new OA\Property(
            property: 'data',
            description: 'Array of sensor readings',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'kekeruhan', type: 'number', format: 'float', example: 45.5),
                    new OA\Property(property: 'keasaman', type: 'number', format: 'float', example: 7.2),
                    new OA\Property(property: 'suhu', type: 'number', format: 'float', example: 28.5),
                    new OA\Property(property: 'waktu', type: 'string', format: 'date-time', example: '2024-01-01 12:00:00'),
                    new OA\Property(property: 'data_source', type: 'string', example: 'REAL_TIME'),
                    new OA\Property(property: 'is_estimated', type: 'boolean', example: false),
                ],
                type: 'object'
            )
        ),
        new OA\Property(
            property: 'page',
            properties: [
                new OA\Property(property: 'order', type: 'string', example: 'desc'),
                new OA\Property(property: 'limit', type: 'integer', example: 1000),
                new OA\Property(property: 'next_cursor', type: 'string', nullable: true, example: null),
                new OA\Property(property: 'has_more', type: 'boolean', example: false),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class SensorHistoryResponseSchema
{
}

