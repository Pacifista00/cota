# Swagger Documentation Quick Reference

Quick reference guide for documenting API endpoints in the COTA project.

## Table of Contents
- [Setup](#setup)
- [Common Imports](#common-imports)
- [HTTP Methods Cheat Sheet](#http-methods-cheat-sheet)
- [Request/Response Patterns](#requestresponse-patterns)
- [Existing Schemas](#existing-schemas)
- [Tips & Tricks](#tips--tricks)

## Setup

### Import Statement
```php
use OpenApi\Attributes as OA;
```

### Generate Documentation
```bash
php artisan l5-swagger:generate
```

### View Documentation
```
http://your-domain/api/documentation
```

## HTTP Methods Cheat Sheet

### GET - Retrieve Data
```php
#[OA\Get(
    path: '/resource',
    summary: 'Brief description',
    tags: ['TagName'],
    responses: [
        new OA\Response(response: 200, description: 'Success'),
    ]
)]
```

### POST - Create Resource
```php
#[OA\Post(
    path: '/resource',
    summary: 'Create new resource',
    tags: ['TagName'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/SchemaName')
    ),
    responses: [
        new OA\Response(response: 201, description: 'Created'),
    ]
)]
```

### PUT - Update Resource (Full)
```php
#[OA\Put(
    path: '/resource/{id}',
    summary: 'Update resource',
    tags: ['TagName'],
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
        content: new OA\JsonContent(ref: '#/components/schemas/SchemaName')
    ),
    responses: [
        new OA\Response(response: 200, description: 'Updated'),
    ]
)]
```

### PATCH - Partial Update
```php
#[OA\Patch(
    path: '/resource/{id}/action',
    summary: 'Perform partial update',
    tags: ['TagName'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Updated'),
    ]
)]
```

### DELETE - Remove Resource
```php
#[OA\Delete(
    path: '/resource/{id}',
    summary: 'Delete resource',
    tags: ['TagName'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Deleted'),
    ]
)]
```

## Request/Response Patterns

### Path Parameter
```php
parameters: [
    new OA\Parameter(
        name: 'id',
        description: 'Resource ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer', example: 1)
    ),
]
```

### Query Parameter
```php
parameters: [
    new OA\Parameter(
        name: 'limit',
        description: 'Number of records',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 10)
    ),
]
```

### Request Body
```php
requestBody: new OA\RequestBody(
    required: true,
    description: 'Request payload',
    content: new OA\JsonContent(ref: '#/components/schemas/SchemaName')
)
```

### Simple Response
```php
new OA\Response(
    response: 200,
    description: 'Success message',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'message', type: 'string', example: 'Success!'),
            new OA\Property(property: 'status', type: 'integer', example: 200),
        ]
    )
)
```

### Response with Schema Reference
```php
new OA\Response(
    response: 200,
    description: 'Success',
    content: new OA\JsonContent(ref: '#/components/schemas/SchemaName')
)
```

### Array Response
```php
new OA\Response(
    response: 200,
    description: 'List of resources',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(ref: '#/components/schemas/ResourceSchema')
            ),
        ]
    )
)
```

### Protected Endpoint
Add this to enable authentication:
```php
security: [['sanctum' => []]]
```

## Existing Schemas

Reference these schemas in your endpoints (located in `app/OpenApi/Schemas/`):

### Authentication
- `LoginRequest` - Login credentials
- `LoginResponse` - Login success response
- `UnauthorizedResponse` - 401 error response

### Common Responses
- `ErrorResponse` - Generic error response
- `ValidationErrorResponse` - 422 validation error

### Usage Example
```php
content: new OA\JsonContent(ref: '#/components/schemas/LoginRequest')
```

## Available Tags

Use these predefined tags:
- `Authentication`
- `Feed Schedule`
- `Feed`
- `Sensor`
- `Pond`
- `Notifications`

## Complete Endpoint Example

```php
#[OA\Post(
    path: '/feed-schedule',
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
                    example: 'Morning Feed'
                ),
                new OA\Property(
                    property: 'waktu_pakan',
                    description: 'Feeding time',
                    type: 'string',
                    format: 'time',
                    example: '08:00:00'
                ),
                new OA\Property(
                    property: 'is_active',
                    description: 'Schedule active status',
                    type: 'boolean',
                    example: true
                ),
            ],
            type: 'object'
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Schedule created successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string'),
                    new OA\Property(property: 'status', type: 'integer'),
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
public function store(Request $request)
{
    // Implementation
}
```

## Creating New Schema

### Simple Schema
```php
<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ResourceName',
    title: 'Resource Title',
    description: 'Description of the resource',
    required: ['field1', 'field2'],
    properties: [
        new OA\Property(
            property: 'field1',
            description: 'Field description',
            type: 'string',
            example: 'example value'
        ),
        new OA\Property(
            property: 'field2',
            type: 'integer',
            example: 123
        ),
    ],
    type: 'object'
)]
class ResourceName
{
}
```

## Common Data Types

| Type | Example |
|------|---------|
| `string` | `type: 'string'` |
| `integer` | `type: 'integer'` |
| `number` | `type: 'number'` (float/double) |
| `boolean` | `type: 'boolean'` |
| `array` | `type: 'array', items: new OA\Items(...)` |
| `object` | `type: 'object', properties: [...]` |

### String Formats
```php
format: 'date'        // 2024-01-01
format: 'date-time'   // 2024-01-01T12:00:00Z
format: 'email'       // user@example.com
format: 'password'    // Hidden in UI
format: 'time'        // 08:00:00
```

## Common Response Status Codes

| Code | Meaning | When to Use |
|------|---------|-------------|
| 200 | OK | Successful GET, PUT, PATCH, DELETE |
| 201 | Created | Successful POST (resource created) |
| 204 | No Content | Successful DELETE (no response body) |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Authentication required or failed |
| 403 | Forbidden | Authenticated but not authorized |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Internal Server Error | Server error |

## Tips & Tricks

### 1. Copy-Paste Template
Keep a template endpoint in your snippets:
```php
#[OA\Get(
    path: '/path',
    summary: 'Summary',
    tags: ['Tag'],
    responses: [
        new OA\Response(response: 200, description: 'Success'),
    ]
)]
```

### 2. Validate Before Commit
Always run after documenting:
```bash
php artisan l5-swagger:generate
```
Check for errors in terminal output.

### 3. Test in Swagger UI
After documenting, test the endpoint in Swagger UI to ensure:
- Request schema is correct
- Response examples are helpful
- Authentication works

### 4. Reuse Schemas
Don't repeat yourself! If you're documenting similar responses, create a schema.

### 5. Meaningful Examples
Use realistic data in examples:
```php
example: 'user@example.com'  // ✅ Good
example: 'string'             // ❌ Bad
```

### 6. Document ALL Responses
Include all possible HTTP status codes your endpoint can return.

## Troubleshooting Quick Fixes

### Can't See New Endpoint
```bash
php artisan cache:clear
php artisan l5-swagger:generate
```

### Schema Not Found Error
1. Check schema name matches exactly (case-sensitive)
2. Ensure schema class exists in `app/OpenApi/Schemas/`
3. Regenerate docs

### Authentication Not Working
In Swagger UI, click 🔓 and enter token **without** "Bearer" prefix.

## Keyboard Shortcuts in Swagger UI

- `Ctrl + F` - Search endpoints
- Click any request - Opens "Try it out"
- Click schema name - Jumps to schema definition

## Useful Commands

```bash
# Generate documentation
php artisan l5-swagger:generate

# Clear caches
php artisan cache:clear
php artisan config:clear

# View routes
php artisan route:list
```

---

**Need More Help?** See [SWAGGER-IMPLEMENTATION-GUIDE.md](./SWAGGER-IMPLEMENTATION-GUIDE.md)

