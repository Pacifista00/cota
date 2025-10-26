<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DataQualityResponse',
    title: 'Data Quality Response',
    description: 'Sensor data quality statistics response',
    properties: [
        new OA\Property(
            property: 'message',
            description: 'Response message',
            type: 'string',
            example: 'Data quality statistics retrieved successfully.'
        ),
        new OA\Property(
            property: 'status',
            description: 'HTTP status code',
            type: 'integer',
            example: 200
        ),
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(property: 'total_records', type: 'integer', example: 1000),
                new OA\Property(property: 'real_time_records', type: 'integer', example: 850),
                new OA\Property(property: 'estimated_records', type: 'integer', example: 150),
                new OA\Property(property: 'data_quality_percentage', type: 'number', format: 'float', example: 85.0),
                new OA\Property(property: 'missing_data_gaps', type: 'integer', example: 5),
            ],
            type: 'object'
        ),
        new OA\Property(
            property: 'period',
            properties: [
                new OA\Property(property: 'start_date', type: 'string', format: 'date', nullable: true, example: '2024-01-01'),
                new OA\Property(property: 'end_date', type: 'string', format: 'date', nullable: true, example: '2024-01-15'),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class DataQualityResponseSchema
{
}

