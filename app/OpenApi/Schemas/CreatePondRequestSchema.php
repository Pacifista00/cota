<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreatePondRequest',
    title: 'Create Pond Request',
    description: 'Request payload for creating a new pond',
    required: ['nama', 'lokasi'],
    properties: [
        new OA\Property(
            property: 'nama',
            description: 'Pond name (required)',
            type: 'string',
            example: 'Tambak Utama'
        ),
        new OA\Property(
            property: 'lokasi',
            description: 'Pond location (required)',
            type: 'string',
            example: 'Jl. Pantai Selatan No. 123'
        ),
    ],
    type: 'object'
)]
class CreatePondRequestSchema
{
}

