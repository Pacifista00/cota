<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'COTA API Documentation',
    description: 'API documentation for COTA - Smart Fish Feeding System. This API provides endpoints for managing fish pond feeding schedules, monitoring sensor data, and handling user authentication.',
    contact: new OA\Contact(
        name: 'COTA Support',
        email: 'support@cota.com'
    )
)]
#[OA\Server(
    url: '/api',
    description: 'COTA API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    description: 'Laravel Sanctum authentication. Enter your token without the Bearer prefix.',
    name: 'Authorization',
    in: 'header',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Tag(
    name: 'Authentication',
    description: 'User authentication and authorization endpoints'
)]
#[OA\Tag(
    name: 'Feed Schedule',
    description: 'Feed schedule management endpoints for automated fish feeding'
)]
#[OA\Tag(
    name: 'Feed',
    description: 'Feed control and history endpoints'
)]
#[OA\Tag(
    name: 'Sensor',
    description: 'Sensor data monitoring and management endpoints'
)]
#[OA\Tag(
    name: 'Pond',
    description: 'Pond management endpoints'
)]
#[OA\Tag(
    name: 'Notifications',
    description: 'Notification management endpoints'
)]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
