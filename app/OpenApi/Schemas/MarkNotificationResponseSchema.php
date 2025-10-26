<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MarkNotificationResponse',
    title: 'Mark Notification Response',
    description: 'Response for marking notification as read',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Notifikasi berhasil ditandai sebagai sudah dibaca.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
    ],
    type: 'object'
)]
class MarkNotificationResponseSchema
{
}

