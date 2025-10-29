<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RegisterResponse',
    title: 'Register Response',
    description: 'Successful registration response',
    properties: [
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(
                    property: 'message',
                    description: 'Response message',
                    type: 'string',
                    example: 'Register berhasil!'
                ),
                new OA\Property(
                    property: 'status',
                    description: 'HTTP status code',
                    type: 'integer',
                    example: 201
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class RegisterResponseSchema
{
}

