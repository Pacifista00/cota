<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UnauthorizedResponse',
    title: 'Unauthorized Response',
    description: 'Unauthorized access response',
    properties: [
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(
                    property: 'message',
                    description: 'Error message',
                    type: 'string',
                    example: 'Login Failed!'
                ),
                new OA\Property(
                    property: 'status',
                    description: 'HTTP status code',
                    type: 'integer',
                    example: 401
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class UnauthorizedResponseSchema
{
}

