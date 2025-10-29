<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotificationListResponse',
    title: 'Notification List Response',
    description: 'Notification list response with collection of notifications',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Daftar notifikasi berhasil dimuat.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
        new OA\Property(
            property: 'data',
            description: 'Array of notifications',
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(property: 'id', type: 'string', example: '9d3e461f-5a7c-4c5e-8b5d-7f9c8d5e6a4b'),
                    new OA\Property(property: 'type', type: 'string', example: 'App\\Notifications\\FeedExecutionNotification'),
                    new OA\Property(property: 'title', type: 'string', example: 'Feed Execution Success'),
                    new OA\Property(property: 'message', type: 'string', example: 'Pemberian pakan berhasil dilakukan'),
                    new OA\Property(property: 'status', type: 'string', example: 'success'),
                    new OA\Property(property: 'icon', type: 'string', nullable: true, example: 'check-circle'),
                    new OA\Property(property: 'color', type: 'string', nullable: true, example: 'success'),
                    new OA\Property(property: 'action_url', type: 'string', nullable: true, example: '/feed/history'),
                    new OA\Property(property: 'feed_execution', type: 'object', nullable: true),
                    new OA\Property(property: 'read_at', type: 'string', format: 'date-time', nullable: true, example: '2024-01-15T10:30:00+00:00'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-15T08:00:00+00:00'),
                    new OA\Property(property: 'created_at_human', type: 'string', example: '2 hours ago'),
                ],
                type: 'object'
            )
        ),
        new OA\Property(
            property: 'unread_count',
            description: 'Total unread notifications count',
            type: 'integer',
            example: 5
        ),
    ],
    type: 'object'
)]
class NotificationListResponseSchema
{
}

