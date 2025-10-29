<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PondResponse',
    title: 'Pond Response',
    description: 'Pond response with data',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Tambak berhasil disimpan!'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 201
        ),
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                new OA\Property(property: 'nama', type: 'string', example: 'Tambak Utama'),
                new OA\Property(property: 'lokasi', type: 'string', example: 'Jl. Pantai Selatan No. 123'),
                new OA\Property(property: 'token_tambak', type: 'string', example: 'a1b2c3d4e5f6g7h8'),
                new OA\Property(property: 'status_koneksi', type: 'string', example: 'pending'),
                new OA\Property(property: 'status_perangkat', type: 'string', example: 'off'),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class PondResponseSchema
{
}

