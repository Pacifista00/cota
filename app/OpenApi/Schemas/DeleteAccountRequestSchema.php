<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeleteAccountRequest',
    title: 'Delete Account Request',
    description: 'Request payload for deleting user account',
    required: ['password'],
    properties: [
        new OA\Property(
            property: 'password',
            description: 'User password for confirmation',
            type: 'string',
            example: 'user_password'
        ),
    ],
    type: 'object'
)]
class DeleteAccountRequestSchema
{
}

