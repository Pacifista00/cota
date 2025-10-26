<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorResponse',
    title: 'Error Response',
    description: 'Standard error response structure',
    properties: [
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(
                    property: 'message',
                    description: 'Error message',
                    type: 'string',
                    example: 'An error occurred'
                ),
                new OA\Property(
                    property: 'status',
                    description: 'HTTP status code',
                    type: 'integer',
                    example: 400
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class ErrorResponseSchema
{
}

