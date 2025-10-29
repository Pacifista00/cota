<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FeedHistoryResponse',
    title: 'Feed History Response',
    description: 'Feed execution history response',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Histori feed berhasil dimuat.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
        new OA\Property(
            property: 'data',
            description: 'Array of feed executions',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 1),
                    new OA\Property(property: 'feed_schedule_id', type: 'integer', nullable: true, example: 1),
                    new OA\Property(property: 'scheduled_time', type: 'string', format: 'time', example: '08:00:00'),
                    new OA\Property(property: 'executed_at', type: 'string', format: 'date-time', example: '2024-01-01 08:00:05'),
                    new OA\Property(property: 'status', type: 'string', example: 'completed'),
                    new OA\Property(property: 'execution_type', type: 'string', example: 'scheduled'),
                    new OA\Property(property: 'delay_seconds', type: 'integer', nullable: true, example: 5),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01 08:00:00'),
                ],
                type: 'object'
            )
        ),
    ],
    type: 'object'
)]
class FeedHistoryResponseSchema
{
}

