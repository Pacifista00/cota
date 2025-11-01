<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserProfileResponse',
    title: 'User Profile Response',
    description: 'Authenticated user profile information',
    properties: [
        new OA\Property(
            property: 'id',
            description: 'User ID',
            type: 'integer',
            example: 1
        ),
        new OA\Property(
            property: 'fullname',
            description: 'User full name',
            type: 'string',
            example: 'John Doe'
        ),
        new OA\Property(
            property: 'email',
            description: 'User email address',
            type: 'string',
            example: 'john@example.com'
        ),
        new OA\Property(
            property: 'no_telepon',
            description: 'User phone number',
            type: 'string',
            example: '08123456789'
        ),
        new OA\Property(
            property: 'role',
            description: 'User role',
            type: 'string',
            example: 'user',
            enum: ['super-admin', 'admin', 'user']
        ),
        new OA\Property(
            property: 'created_at',
            description: 'Account creation timestamp',
            type: 'string',
            example: '2024-01-15T08:30:00Z'
        ),
        new OA\Property(
            property: 'updated_at',
            description: 'Account last update timestamp',
            type: 'string',
            example: '2024-10-20T10:15:00Z'
        ),
    ],
    type: 'object'
)]
class UserProfileResponseSchema
{
}

