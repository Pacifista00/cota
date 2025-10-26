# Swagger API Documentation Implementation Guide

## Table of Contents
1. [Overview](#overview)
2. [Getting Started](#getting-started)
3. [Basic Concepts](#basic-concepts)
4. [Documenting Endpoints](#documenting-endpoints)
5. [Schema Definitions](#schema-definitions)
6. [Authentication](#authentication)
7. [Best Practices](#best-practices)
8. [Common Patterns](#common-patterns)
9. [Troubleshooting](#troubleshooting)

## Overview

This project uses **darkaonline/l5-swagger** (based on OpenAPI 3.0) to generate interactive API documentation. The documentation is generated from PHP 8 attributes (annotations) in your code.

### Key Benefits
- **Type-safe**: Uses PHP 8 attributes with IDE support
- **Single Source of Truth**: Documentation lives with your code
- **Interactive**: Swagger UI allows testing endpoints directly
- **Maintainable**: Changes to code prompt documentation updates

## Getting Started

### Viewing Documentation

Access the Swagger UI at:
```
http://your-domain/api/documentation
```

### Generating Documentation

After making changes to annotations:
```bash
php artisan l5-swagger:generate
```

> **Note**: In development, documentation auto-regenerates on each request (configured in `config/l5-swagger.php`).

## Basic Concepts

### OpenAPI Attributes

We use PHP 8 attributes to document our API. All attributes are from the `OpenApi\Attributes` namespace, imported as `OA`.

```php
use OpenApi\Attributes as OA;
```

### Core Attributes

1. **`#[OA\Post]`** / **`#[OA\Get]`** / **`#[OA\Put]`** / **`#[OA\Delete]`** / **`#[OA\Patch]`**
   - Defines an API endpoint
   - Parameters: `path`, `summary`, `description`, `tags`, `requestBody`, `responses`

2. **`#[OA\Schema]`**
   - Defines a reusable data structure
   - Used for request/response models

3. **`#[OA\RequestBody]`**
   - Defines the structure of request data

4. **`#[OA\Response]`**
   - Defines response structure for specific HTTP status codes

## Documenting Endpoints

### Step-by-Step Guide

#### 1. Import OpenAPI Attributes

At the top of your controller:
```php
use OpenApi\Attributes as OA;
```

#### 2. Add Endpoint Documentation

Place attributes directly above your controller method:

```php
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
public function login(Request $request)
{
    // Your implementation
}
```

### Available Tags

Defined in `app/Http/Controllers/Controller.php`:
- `Authentication` - Auth-related endpoints
- `Feed Schedule` - Feed scheduling operations
- `Feed` - Feed control and history
- `Sensor` - Sensor data operations
- `Pond` - Pond management
- `Notifications` - Notification operations

### Path Parameters

For routes with parameters (e.g., `/feed-schedule/{id}`):

```php
#[OA\Get(
    path: '/feed-schedule/{id}',
    summary: 'Get Feed Schedule by ID',
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
        // ... response definitions
    ]
)]
public function show($id)
{
    // Your implementation
}
```

### Query Parameters

For query string parameters:

```php
#[OA\Get(
    path: '/feed/history',
    summary: 'Get Feed History',
    tags: ['Feed'],
    parameters: [
        new OA\Parameter(
            name: 'limit',
            description: 'Number of records to return',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 10, example: 20)
        ),
        new OA\Parameter(
            name: 'offset',
            description: 'Number of records to skip',
            in: 'query',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 0, example: 0)
        ),
    ],
    responses: [
        // ... response definitions
    ]
)]
public function history(Request $request)
{
    // Your implementation
}
```

## Schema Definitions

### Creating Schema Classes

Create schema classes in `app/OpenApi/Schemas/` directory:

```php
<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'YourSchemaName',
    title: 'Your Schema Title',
    description: 'Description of your schema',
    required: ['field1', 'field2'],
    properties: [
        new OA\Property(
            property: 'field1',
            description: 'Description of field1',
            type: 'string',
            example: 'example value'
        ),
        new OA\Property(
            property: 'field2',
            description: 'Description of field2',
            type: 'integer',
            example: 123
        ),
    ],
    type: 'object'
)]
class YourSchemaName
{
}
```

### Nested Objects

For nested object structures:

```php
#[OA\Schema(
    schema: 'UserResponse',
    properties: [
        new OA\Property(
            property: 'data',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                new OA\Property(
                    property: 'profile',
                    properties: [
                        new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                        new OA\Property(property: 'phone', type: 'string', example: '+62812345678'),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class UserResponse
{
}
```

### Arrays

For array responses:

```php
#[OA\Schema(
    schema: 'FeedScheduleList',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/FeedSchedule')
        ),
        new OA\Property(property: 'total', type: 'integer', example: 25),
    ],
    type: 'object'
)]
class FeedScheduleList
{
}
```

## Authentication

### Protected Endpoints

For endpoints requiring authentication, add the `security` parameter:

```php
#[OA\Get(
    path: '/feed-schedule',
    summary: 'Get Feed Schedules',
    security: [['sanctum' => []]],
    tags: ['Feed Schedule'],
    responses: [
        // ... response definitions
    ]
)]
public function index(Request $request)
{
    // Your implementation
}
```

### Testing with Authentication

In Swagger UI:
1. Click the **Authorize** button (🔓 icon)
2. Enter your token (without "Bearer" prefix)
3. Click **Authorize**
4. Test authenticated endpoints

## Best Practices

### 1. DRY Principle - Reuse Schemas

❌ **Bad** - Repeating schema definitions:
```php
content: new OA\JsonContent(
    properties: [
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'status', type: 'integer'),
    ]
)
```

✅ **Good** - Reference existing schema:
```php
content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
```

### 2. Consistent Response Structures

Always use the same response structure for similar operations:
- Success responses: `{ "data": { "message": "...", "status": 200, ... } }`
- Error responses: `{ "data": { "message": "...", "status": 4xx } }`

### 3. Meaningful Examples

Provide realistic examples in your schemas:

❌ **Bad**:
```php
example: 'string'
```

✅ **Good**:
```php
example: 'user@example.com'
```

### 4. Complete Response Documentation

Document ALL possible responses:
- 200/201 - Success
- 400 - Bad Request
- 401 - Unauthorized
- 403 - Forbidden
- 404 - Not Found
- 422 - Validation Error
- 500 - Server Error

### 5. Descriptive Summaries and Descriptions

❌ **Bad**:
```php
summary: 'Login'
```

✅ **Good**:
```php
summary: 'User Login',
description: 'Authenticate user with email and password, and generate access token'
```

## Common Patterns

### CRUD Operations

#### Create (POST)

```php
#[OA\Post(
    path: '/resource',
    summary: 'Create Resource',
    security: [['sanctum' => []]],
    tags: ['Resource'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/CreateResourceRequest')
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Resource created successfully',
            content: new OA\JsonContent(ref: '#/components/schemas/ResourceResponse')
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error',
            content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
        ),
    ]
)]
public function store(Request $request) { }
```

#### Read (GET)

```php
#[OA\Get(
    path: '/resource/{id}',
    summary: 'Get Resource by ID',
    security: [['sanctum' => []]],
    tags: ['Resource'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Resource details',
            content: new OA\JsonContent(ref: '#/components/schemas/ResourceResponse')
        ),
        new OA\Response(
            response: 404,
            description: 'Resource not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
    ]
)]
public function show($id) { }
```

#### Update (PUT/PATCH)

```php
#[OA\Put(
    path: '/resource/{id}',
    summary: 'Update Resource',
    security: [['sanctum' => []]],
    tags: ['Resource'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/UpdateResourceRequest')
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Resource updated successfully',
            content: new OA\JsonContent(ref: '#/components/schemas/ResourceResponse')
        ),
        new OA\Response(
            response: 404,
            description: 'Resource not found',
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

#### Delete (DELETE)

```php
#[OA\Delete(
    path: '/resource/{id}',
    summary: 'Delete Resource',
    security: [['sanctum' => []]],
    tags: ['Resource'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Resource deleted successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'message',
                        type: 'string',
                        example: 'Resource berhasil dihapus!'
                    ),
                ]
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Resource not found',
            content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
        ),
    ]
)]
public function destroy($id) { }
```

### Pagination

```php
#[OA\Schema(
    schema: 'PaginatedResponse',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(type: 'object')
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
)]
class PaginatedResponse
{
}
```

## Troubleshooting

### Documentation Not Updating

1. Clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

2. Regenerate documentation:
   ```bash
   php artisan l5-swagger:generate
   ```

3. Check file permissions on `storage/api-docs/`

### "Unable to render this definition"

- **Cause**: Syntax error in OpenAPI annotations
- **Solution**: Check console for errors, validate schema references

### Schema Not Found

- **Issue**: `ref: '#/components/schemas/YourSchema'` returns 404
- **Solution**: 
  1. Ensure schema class exists in `app/OpenApi/Schemas/`
  2. Verify schema name in `#[OA\Schema(schema: 'YourSchema')]` matches reference
  3. Regenerate docs

### Attributes Not Being Picked Up

- **Cause**: Namespace not scanned
- **Solution**: Check `config/l5-swagger.php` → `paths.annotations` includes your directory

### Testing Authentication Fails

- **Issue**: Getting 401 even with valid token
- **Solution**: 
  1. Ensure token is entered without "Bearer" prefix in Swagger UI
  2. Check token is not expired
  3. Verify `sanctum` security scheme in `Controller.php`

## Additional Resources

- [OpenAPI Specification](https://swagger.io/specification/)
- [Swagger-PHP Documentation](https://zircote.github.io/swagger-php/)
- [L5-Swagger Documentation](https://github.com/DarkaOnLine/L5-Swagger)

## Need Help?

If you encounter issues not covered here:
1. Check the [SWAGGER-QUICK-REFERENCE.md](./SWAGGER-QUICK-REFERENCE.md)
2. Review existing documented endpoints in the codebase
3. Consult the team lead or technical documentation maintainer

---

**Last Updated**: 2024
**Maintainer**: COTA Development Team

