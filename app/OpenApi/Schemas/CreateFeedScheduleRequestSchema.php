<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateFeedScheduleRequest',
    title: 'Create Feed Schedule Request',
    description: 'Request payload for creating a new feed schedule',
    required: ['waktu_pakan'],
    properties: [
        new OA\Property(
            property: 'name',
            description: 'Schedule name (optional, max 255 characters)',
            type: 'string',
            maxLength: 255,
            example: 'Morning Feed Schedule'
        ),
        new OA\Property(
            property: 'description',
            description: 'Schedule description (optional, max 1000 characters)',
            type: 'string',
            maxLength: 1000,
            example: 'Daily morning feeding schedule for fish pond'
        ),
        new OA\Property(
            property: 'waktu_pakan',
            description: 'Feeding time in HH:MM:SS format (required)',
            type: 'string',
            format: 'time',
            example: '08:00:00'
        ),
        new OA\Property(
            property: 'start_date',
            description: 'Schedule start date (optional, defaults to today)',
            type: 'string',
            format: 'date',
            example: '2024-01-01'
        ),
        new OA\Property(
            property: 'end_date',
            description: 'Schedule end date (optional, must be after or equal to start_date)',
            type: 'string',
            format: 'date',
            example: '2024-12-31'
        ),
        new OA\Property(
            property: 'is_active',
            description: 'Schedule active status (optional, defaults to true)',
            type: 'boolean',
            example: true
        ),
        new OA\Property(
            property: 'frequency_type',
            description: 'Frequency type (optional, defaults to daily)',
            type: 'string',
            enum: ['daily', 'weekly', 'monthly'],
            example: 'daily'
        ),
        new OA\Property(
            property: 'frequency_data',
            description: 'Additional frequency data (optional)',
            type: 'object',
            example: ['days' => [1, 2, 3, 4, 5]]
        ),
    ],
    type: 'object'
)]
class CreateFeedScheduleRequestSchema
{
}

