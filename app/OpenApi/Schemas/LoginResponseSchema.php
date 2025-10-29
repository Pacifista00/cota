<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginResponse',
    title: 'Login Response',
    description: 'Successful login response',
    properties: [
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(
                    property: 'message',
                    description: 'Response message',
                    type: 'string',
                    example: 'Login berhasil!'
                ),
                new OA\Property(
                    property: 'status',
                    description: 'HTTP status code',
                    type: 'integer',
                    example: 200
                ),
                new OA\Property(
                    property: 'token',
                    description: 'Sanctum authentication token',
                    type: 'string',
                    example: '1|abcdefghijklmnopqrstuvwxyz123456'
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class LoginResponseSchema
{
}

