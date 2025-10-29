<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateProfileValidationError',
    title: 'Update Profile Validation Error',
    description: 'Validation error response for profile update',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Error message',
            type: 'string',
            example: 'Validasi gagal'
        ),
        new OA\Property(
            property: 'errors',
            description: 'Field-specific validation errors',
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'email',
                    description: 'Email validation errors',
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                    example: ['Format email tidak valid']
                ),
                new OA\Property(
                    property: 'no_telepon',
                    description: 'Phone number validation errors',
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                    example: ['Nomor telepon sudah digunakan']
                ),
            ]
        ),
    ],
    type: 'object'
)]
class UpdateProfileValidationErrorSchema
{
}

