<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateFeedScheduleRequest',
    title: 'Update Feed Schedule Request',
    description: 'Request payload for updating an existing feed schedule (all fields are optional)',
    properties: [
        new OA\Property(
            property: 'name',
            description: 'Schedule name',
            type: 'string',
            maxLength: 255,
            example: 'Updated Morning Feed'
        ),
        new OA\Property(
            property: 'description',
            description: 'Schedule description',
            type: 'string',
            maxLength: 1000,
            example: 'Updated description'
        ),
        new OA\Property(
            property: 'waktu_pakan',
            description: 'Feeding time in HH:MM:SS format',
            type: 'string',
            format: 'time',
            example: '09:00:00'
        ),
        new OA\Property(
            property: 'start_date',
            description: 'Schedule start date',
            type: 'string',
            format: 'date',
            example: '2024-01-01'
        ),
        new OA\Property(
            property: 'end_date',
            description: 'Schedule end date',
            type: 'string',
            format: 'date',
            example: '2024-12-31'
        ),
        new OA\Property(
            property: 'is_active',
            description: 'Schedule active status',
            type: 'boolean',
            example: false
        ),
        new OA\Property(
            property: 'frequency_type',
            description: 'Frequency type',
            type: 'string',
            enum: ['daily', 'weekly', 'monthly'],
            example: 'weekly'
        ),
        new OA\Property(
            property: 'frequency_data',
            description: 'Additional frequency data',
            type: 'object',
            example: ['days' => [1, 3, 5]]
        ),
    ],
    type: 'object'
)]
class UpdateFeedScheduleRequestSchema
{
}

