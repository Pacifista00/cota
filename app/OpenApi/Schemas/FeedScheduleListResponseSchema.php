<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FeedScheduleListResponse',
    title: 'Feed Schedule List Response',
    description: 'Feed schedule list response with collection of items',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Daftar jadwal pakan berhasil dimuat.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
        new OA\Property(
            property: 'data',
            description: 'Array of feed schedules',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Morning Feed'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Daily morning feeding'),
                    new OA\Property(property: 'waktu_pakan', type: 'string', example: '08:00:00'),
                    new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2024-01-01'),
                    new OA\Property(property: 'end_date', type: 'string', format: 'date', nullable: true, example: '2024-12-31'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                    new OA\Property(property: 'frequency_type', type: 'string', example: 'daily'),
                    new OA\Property(property: 'frequency_type_label', type: 'string', example: 'Daily'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01 00:00:00'),
                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01 00:00:00'),
                ],
                type: 'object'
            )
        ),
    ],
    type: 'object'
)]
class FeedScheduleListResponseSchema
{
}

