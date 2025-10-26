<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegisterRequest',
    title: 'Register Request',
    description: 'User registration request payload',
    required: ['fullname', 'email', 'no_telepon', 'password', 'password_confirm'],
    properties: [
        new OA\Property(
            property: 'fullname',
            description: 'User full name (max 54 characters)',
            type: 'string',
            maxLength: 54,
            example: 'John Doe'
        ),
        new OA\Property(
            property: 'email',
            description: 'User email address (must be unique)',
            type: 'string',
            format: 'email',
            example: 'john.doe@example.com'
        ),
        new OA\Property(
            property: 'no_telepon',
            description: 'User phone number (must be unique)',
            type: 'string',
            example: '+6281234567890'
        ),
        new OA\Property(
            property: 'password',
            description: 'User password',
            type: 'string',
            format: 'password',
            example: 'SecurePassword123'
        ),
        new OA\Property(
            property: 'password_confirm',
            description: 'Password confirmation (must match password)',
            type: 'string',
            format: 'password',
            example: 'SecurePassword123'
        ),
    ],
    type: 'object'
)]
class RegisterRequestSchema
{
}

