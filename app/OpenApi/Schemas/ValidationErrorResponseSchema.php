<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ValidationErrorResponse',
    title: 'Validation Error Response',
    description: 'Validation error response structure',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Error message',
            type: 'string',
            example: 'Validasi gagal'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 422
        ),
    ],
    type: 'object'
)]
class ValidationErrorResponseSchema
{
}

