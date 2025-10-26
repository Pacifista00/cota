<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotificationStatisticsResponse',
    title: 'Notification Statistics Response',
    description: 'Notification statistics response with counts',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Statistik notifikasi berhasil dimuat.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(
                    property: 'total',
                    description: 'Total notifications count',
                    type: 'integer',
                    example: 50
                ),
                new OA\Property(
                    property: 'unread',
                    description: 'Unread notifications count',
                    type: 'integer',
                    example: 5
                ),
                new OA\Property(
                    property: 'read',
                    description: 'Read notifications count',
                    type: 'integer',
                    example: 45
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class NotificationStatisticsResponseSchema
{
}

