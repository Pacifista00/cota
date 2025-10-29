<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateProfileRequest',
    title: 'Update Profile Request',
    description: 'Request payload for updating user profile (all fields optional for partial update)',
    properties: [
        new OA\Property(
            property: 'fullname',
            description: 'User full name (optional)',
            type: 'string',
            example: 'John Doe Updated'
        ),
        new OA\Property(
            property: 'email',
            description: 'User email address (optional)',
            type: 'string',
            example: 'john.new@example.com'
        ),
        new OA\Property(
            property: 'no_telepon',
            description: 'User phone number (optional)',
            type: 'string',
            example: '08987654321'
        ),
    ],
    type: 'object'
)]
class UpdateProfileRequestSchema
{
}

