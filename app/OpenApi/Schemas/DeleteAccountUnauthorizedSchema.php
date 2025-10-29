<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeleteAccountUnauthorized',
    title: 'Delete Account Unauthorized',
    description: 'Unauthorized response for account deletion (wrong password or invalid token)',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Error message',
            type: 'string',
            example: 'Password salah'
        ),
    ],
    type: 'object'
)]
class DeleteAccountUnauthorizedSchema
{
}

