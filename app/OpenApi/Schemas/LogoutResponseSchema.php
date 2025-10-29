<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LogoutResponse',
    title: 'Logout Response',
    description: 'Successful logout response',
    properties: [
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(
                    property: 'message',
                    description: 'Response message',
                    type: 'string',
                    example: 'Logout berhasil!'
                ),
                new OA\Property(
                    property: 'status',
                    description: 'HTTP status code',
                    type: 'integer',
                    example: 200
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class LogoutResponseSchema
{
}

