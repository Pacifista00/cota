# Swagger Documentation - Practical Examples

This guide provides copy-paste ready examples for common API documentation scenarios in the COTA project.

## Table of Contents

1. [Authentication Endpoints](#authentication-endpoints)
2. [CRUD Operations](#crud-operations)
3. [Custom Actions](#custom-actions)
4. [Protected Endpoints](#protected-endpoints)
5. [Paginated Responses](#paginated-responses)
6. [File Uploads](#file-uploads)

## Authentication Endpoints

### Login (POST) - Already Implemented ✅

```php
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/login',
    summary: 'User Login',
    description: 'Authenticate user with email and password, and generate access token',
    tags: ['Authentication'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Login successful',
            content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')
        ),
        new OA\Response(
            response: 401,
            description: 'Invalid credentials',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
        ),
    ]
)]
public function login(Request $request) { }
```

### Register (POST) - Template

```php
#[OA\Post(
    path: '/register',
    summary: 'User Registration',
    description: 'Register a new user account',
    tags: ['Authentication'],
    requestBody: new OA\RequestBody(
        required: true,
        description: 'User registration data',
        content: new OA\JsonContent(
            required: ['fullname', 'email', 'no_telepon', 'password', 'password_confirm'],
            properties: [
                new OA\Property(
                    property: 'fullname',
                    description: 'User full name',
                    type: 'string',
                    maxLength: 54,
                    example: 'John Doe'
                ),
                new OA\Property(
                    property: 'email',
                    description: 'User email address',
                    type: 'string',
                    format: 'email',
                    example: 'john@example.com'
                ),
                new OA\Property(
                    property: 'no_telepon',
                    description: 'User phone number',
                    type: 'string',
                    example: '+6281234567890'
                ),
                new OA\Property(
                    property: 'password',
                    description: 'User password',
                    type: 'string',
                    format: 'password',
                    example: 'SecurePass123'
                ),
                new OA\Property(
                    property: 'password_confirm',
                    description: 'Password confirmation',
                    type: 'string',
                    format: 'password',
                    example: 'SecurePass123'
                ),
            ],
            type: 'object'
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Registration successful',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'data',
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'Register berhasil!'),
                            new OA\Property(property: 'status', type: 'integer', example: 201),
                        ],
                        type: 'object'
                    ),
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
        ),
        new OA\Response(
            response: 500,
            description: 'Registration failed',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
    ]
)]
public function register(Request $request) { }
```

### Logout (POST) - Template

```php
#[OA\Post(
    path: '/logout',
    summary: 'User Logout',
    description: 'Invalidate user authentication token',
    security: [['sanctum' => []]],
    tags: ['Authentication'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Logout successful',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'data',
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'Logout berhasil!'),
                            new OA\Property(property: 'status', type: 'integer', example: 200),
                        ],
                        type: 'object'
                    ),
                ]
            )
        ),
        new OA\Response(
            response: 400,
            description: 'User not logged in',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthorized',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
    ]
)]
public function logout(Request $request) { }
```

## CRUD Operations

### List Resources (GET)

```php
#[OA\Get(
    path: '/feed-schedule',
    summary: 'List Feed Schedules',
    description: 'Get a list of all feed schedules for the authenticated user',
    security: [['sanctum' => []]],
    tags: ['Feed Schedule'],
    parameters: [
        new OA\Parameter(
            name: 'status',
            description: 'Filter by status',
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'string',
                enum: ['active', 'inactive'],
                example: 'active'
            )
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'List of feed schedules',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Daftar jadwal pakan berhasil dimuat.'),
                    new OA\Property(property: 'status', type: 'integer', example: 200),
                    new OA\Property(
                        property: 'data',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Morning Feed'),
                                new OA\Property(property: 'waktu_pakan', type: 'string', example: '08:00:00'),
                                new OA\Property(property: 'is_active', type: 'boolean', example: true),
                            ]
                        )
                    ),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthorized',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
    ]
)]
public function index(Request $request) { }
```

### Get Single Resource (GET)

```php
#[OA\Get(
    path: '/feed-schedule/{id}',
    summary: 'Get Feed Schedule Details',
    description: 'Get detailed information about a specific feed schedule',
    security: [['sanctum' => []]],
    tags: ['Feed Schedule'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'Feed schedule ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer', example: 1)
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Feed schedule details',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Detail jadwal pakan berhasil dimuat.'),
                    new OA\Property(property: 'status', type: 'integer', example: 200),
                    new OA\Property(
                        property: 'data',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Morning Feed'),
                            new OA\Property(property: 'description', type: 'string', example: 'Daily morning feeding'),
                            new OA\Property(property: 'waktu_pakan', type: 'string', example: '08:00:00'),
                            new OA\Property(property: 'is_active', type: 'boolean', example: true),
                        ],
                        type: 'object'
                    ),
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Feed schedule not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthorized',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
    ]
)]
public function show($id) { }
```

### Create Resource (POST)

```php
#[OA\Post(
    path: '/feed-schedule/create',
    summary: 'Create Feed Schedule',
    description: 'Create a new automated feeding schedule',
    security: [['sanctum' => []]],
    tags: ['Feed Schedule'],
    requestBody: new OA\RequestBody(
        required: true,
        description: 'Feed schedule data',
        content: new OA\JsonContent(
            required: ['waktu_pakan'],
            properties: [
                new OA\Property(
                    property: 'name',
                    description: 'Schedule name',
                    type: 'string',
                    maxLength: 255,
                    example: 'Morning Feed'
                ),
                new OA\Property(
                    property: 'description',
                    description: 'Schedule description',
                    type: 'string',
                    maxLength: 1000,
                    example: 'Daily morning feeding schedule'
                ),
                new OA\Property(
                    property: 'waktu_pakan',
                    description: 'Feeding time (HH:MM:SS format)',
                    type: 'string',
                    format: 'time',
                    example: '08:00:00'
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
                    description: 'Active status',
                    type: 'boolean',
                    example: true
                ),
                new OA\Property(
                    property: 'frequency_type',
                    description: 'Frequency type',
                    type: 'string',
                    enum: ['daily', 'weekly', 'monthly'],
                    example: 'daily'
                ),
            ],
            type: 'object'
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Feed schedule created successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Jadwal pakan berhasil disimpan!'),
                    new OA\Property(property: 'status', type: 'integer', example: 201),
                    new OA\Property(property: 'data', type: 'object'),
                ]
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthorized',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
    ]
)]
public function store(Request $request) { }
```

### Update Resource (PUT)

```php
#[OA\Put(
    path: '/feed-schedule/{id}',
    summary: 'Update Feed Schedule',
    description: 'Update an existing feed schedule',
    security: [['sanctum' => []]],
    tags: ['Feed Schedule'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'Feed schedule ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer', example: 1)
        ),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Updated Morning Feed'),
                new OA\Property(property: 'waktu_pakan', type: 'string', example: '09:00:00'),
                new OA\Property(property: 'is_active', type: 'boolean', example: true),
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Feed schedule updated successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Jadwal pakan berhasil diubah!'),
                    new OA\Property(property: 'status', type: 'integer', example: 200),
                    new OA\Property(property: 'data', type: 'object'),
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Feed schedule not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
        ),
    ]
)]
public function update(Request $request, $id) { }
```

### Delete Resource (DELETE)

```php
#[OA\Delete(
    path: '/feed-schedule/{id}',
    summary: 'Delete Feed Schedule',
    description: 'Delete an existing feed schedule',
    security: [['sanctum' => []]],
    tags: ['Feed Schedule'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'Feed schedule ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer', example: 1)
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Feed schedule deleted successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Jadwal pakan berhasil dihapus!'),
                    new OA\Property(property: 'status', type: 'integer', example: 200),
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Feed schedule not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthorized',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
    ]
)]
public function destroy($id) { }
```

## Custom Actions

### Activate Resource (PATCH)

```php
#[OA\Patch(
    path: '/feed-schedule/{id}/activate',
    summary: 'Activate Feed Schedule',
    description: 'Activate a specific feed schedule',
    security: [['sanctum' => []]],
    tags: ['Feed Schedule'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            description: 'Feed schedule ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer', example: 1)
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Feed schedule activated successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Jadwal pakan berhasil diaktifkan!'),
                    new OA\Property(property: 'status', type: 'integer', example: 200),
                    new OA\Property(property: 'data', type: 'object'),
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Feed schedule not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
    ]
)]
public function activate($id) { }
```

### Get Statistics (GET)

```php
#[OA\Get(
    path: '/notifications/statistics',
    summary: 'Get Notification Statistics',
    description: 'Get notification statistics for the authenticated user',
    security: [['sanctum' => []]],
    tags: ['Notifications'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Notification statistics',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'data',
                        properties: [
                            new OA\Property(property: 'total', type: 'integer', example: 50),
                            new OA\Property(property: 'unread', type: 'integer', example: 5),
                            new OA\Property(property: 'read', type: 'integer', example: 45),
                        ],
                        type: 'object'
                    ),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthorized',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
    ]
)]
public function statistics(Request $request) { }
```

## Protected Endpoints

### Simple Protected Endpoint

```php
#[OA\Get(
    path: '/sensor-data/latest',
    summary: 'Get Latest Sensor Data',
    description: 'Get the most recent sensor readings',
    security: [['sanctum' => []]],  // This makes it protected
    tags: ['Sensor'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Latest sensor data',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'temperature', type: 'number', example: 28.5),
                    new OA\Property(property: 'ph', type: 'number', example: 7.2),
                    new OA\Property(property: 'timestamp', type: 'string', example: '2024-01-01 12:00:00'),
                ]
            )
        ),
        new OA\Response(
            response: 401,
            description: 'Unauthorized',
            content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse')
        ),
    ]
)]
public function latest() { }
```

## Paginated Responses

```php
#[OA\Get(
    path: '/feed/history',
    summary: 'Get Feed History',
    description: 'Get paginated feed execution history',
    security: [['sanctum' => []]],
    tags: ['Feed'],
    parameters: [
        new OA\Parameter(
            name: 'page',
            description: 'Page number',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 1, example: 1)
        ),
        new OA\Parameter(
            name: 'per_page',
            description: 'Items per page',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 15, example: 15)
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Paginated feed history',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'data',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'scheduled_time', type: 'string', example: '08:00:00'),
                                new OA\Property(property: 'executed_at', type: 'string', example: '2024-01-01 08:00:05'),
                                new OA\Property(property: 'status', type: 'string', example: 'completed'),
                            ]
                        )
                    ),
                    new OA\Property(
                        property: 'meta',
                        properties: [
                            new OA\Property(property: 'current_page', type: 'integer', example: 1),
                            new OA\Property(property: 'per_page', type: 'integer', example: 15),
                            new OA\Property(property: 'total', type: 'integer', example: 100),
                            new OA\Property(property: 'last_page', type: 'integer', example: 7),
                        ],
                        type: 'object'
                    ),
                ]
            )
        ),
    ]
)]
public function history(Request $request) { }
```

## Public Endpoints (No Auth Required)

```php
#[OA\Get(
    path: '/sensor-data/quality',
    summary: 'Get Sensor Data Quality',
    description: 'Get water quality metrics (public endpoint)',
    tags: ['Sensor'],
    // NOTE: No security parameter = public endpoint
    responses: [
        new OA\Response(
            response: 200,
            description: 'Water quality data',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'quality', type: 'string', example: 'Good'),
                    new OA\Property(property: 'score', type: 'integer', example: 85),
                ]
            )
        ),
    ]
)]
public function dataQuality() { }
```

## Tips

### 1. Always Import OpenAPI Attributes

```php
use OpenApi\Attributes as OA;
```

### 2. Use Existing Schemas

```php
content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
```

### 3. Mark Protected Endpoints

```php
security: [['sanctum' => []]]
```

### 4. Provide Realistic Examples

```php
example: 'user@example.com'  // Good
example: 'string'             // Bad
```

### 5. Document All Responses

Include 200, 401, 404, 422, 500 as appropriate.

### 6. After Documenting

```bash
php artisan l5-swagger:generate
```

---

**Need more examples?** Check [SWAGGER-IMPLEMENTATION-GUIDE.md](./SWAGGER-IMPLEMENTATION-GUIDE.md) for detailed explanations.
