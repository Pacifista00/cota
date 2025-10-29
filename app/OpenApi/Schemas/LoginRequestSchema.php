<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginRequest',
    title: 'Login Request',
    description: 'Login request payload',
    required: ['email', 'password'],
    properties: [
        new OA\Property(
            property: 'email',
            description: 'User email address',
            type: 'string',
            format: 'email',
            example: 'user@example.com'
        ),
        new OA\Property(
            property: 'password',
            description: 'User password',
            type: 'string',
            format: 'password',
            example: 'password123'
        ),
    ],
    type: 'object'
)]
class LoginRequestSchema
{
}

