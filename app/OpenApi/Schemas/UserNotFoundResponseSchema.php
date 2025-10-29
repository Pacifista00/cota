<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserNotFoundResponse',
    title: 'User Not Found Response',
    description: 'User not found error response',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Error message',
            type: 'string',
            example: 'User tidak ditemukan'
        ),
    ],
    type: 'object'
)]
class UserNotFoundResponseSchema
{
}

