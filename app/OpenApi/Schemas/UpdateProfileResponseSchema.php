<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateProfileResponse',
    title: 'Update Profile Response',
    description: 'Successful profile update response',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Success message',
            type: 'string',
            example: 'Profile berhasil diperbarui'
        ),
        new OA\Property(
            property: 'data',
            ref: '#/components/schemas/UserProfileResponse',
            description: 'Updated user profile data'
        ),
    ],
    type: 'object'
)]
class UpdateProfileResponseSchema
{
}

