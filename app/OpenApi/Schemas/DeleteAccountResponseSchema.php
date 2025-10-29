<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeleteAccountResponse',
    title: 'Delete Account Response',
    description: 'Successful account deletion response',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Success message',
            type: 'string',
            example: 'Akun berhasil dihapus'
        ),
    ],
    type: 'object'
)]
class DeleteAccountResponseSchema
{
}

