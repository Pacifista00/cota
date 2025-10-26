<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MarkAllNotificationResponse',
    title: 'Mark All Notifications Response',
    description: 'Response for marking all notifications as read',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message with count',
            type: 'string',
            example: '5 notifikasi berhasil ditandai sebagai sudah dibaca.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
        new OA\Property(
            property: 'marked_count',
            description: 'Number of notifications marked as read',
            type: 'integer',
            example: 5
        ),
    ],
    type: 'object'
)]
class MarkAllNotificationResponseSchema
{
}

