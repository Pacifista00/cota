# COTA API - Swagger Documentation

## Overview

The COTA API now includes comprehensive Swagger/OpenAPI documentation that provides:

-   Interactive API testing interface
-   Detailed endpoint documentation
-   Request/response schemas
-   Authentication support
-   Real-time testing capabilities

## Accessing the Documentation

### Local Development

```
http://localhost:8000/api/documentation
```

### Production

```
https://your-domain.com/api/documentation
```

## Quick Start

### 1. View Documentation

Simply navigate to the documentation URL in your browser to see the interactive Swagger UI.

### 2. Test Authentication

1. Click the **"Authorize"** 🔓 button in the top right
2. Enter your authentication token (without "Bearer" prefix)
3. Click **"Authorize"**
4. Test protected endpoints

### 3. Test Endpoints

1. Select an endpoint
2. Click **"Try it out"**
3. Fill in required parameters
4. Click **"Execute"**
5. View the response

## Current Documentation Status

### ✅ Implemented

-   **Base Configuration**: OpenAPI 3.0 specification setup
-   **Authentication**: Laravel Sanctum security scheme
-   **Login Endpoint**: Fully documented with request/response schemas
-   **Schema Library**: Reusable schemas for common responses
-   **Developer Guides**: Complete implementation and quick reference guides

### 📋 Available Schemas

-   `LoginRequest` - Login credentials structure
-   `LoginResponse` - Successful login response
-   `ErrorResponse` - Generic error response
-   `ValidationErrorResponse` - Validation error structure
-   `UnauthorizedResponse` - 401 unauthorized response

### 🏷️ Available Tags

-   Authentication
-   Feed Schedule
-   Feed
-   Sensor
-   Pond
-   Notifications

## For Developers

### Adding Documentation to New Endpoints

See the comprehensive guides:

-   **[Implementation Guide](./SWAGGER-IMPLEMENTATION-GUIDE.md)** - Detailed documentation guide
-   **[Quick Reference](./SWAGGER-QUICK-REFERENCE.md)** - Cheat sheet and quick examples

### Quick Example

```php
use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/your-endpoint',
    summary: 'Endpoint summary',
    tags: ['YourTag'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: '#/components/schemas/YourSchema')
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Success',
            content: new OA\JsonContent(ref: '#/components/schemas/ResponseSchema')
        ),
    ]
)]
public function yourMethod(Request $request)
{
    // Implementation
}
```

### Regenerate Documentation

After adding or modifying documentation:

```bash
php artisan l5-swagger:generate
```

## Architecture

### Directory Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Controller.php          # Base OpenAPI configuration
└── OpenApi/
    └── Schemas/                     # Reusable schema definitions
        ├── LoginRequestSchema.php
        ├── LoginResponseSchema.php
        ├── ErrorResponseSchema.php
        ├── ValidationErrorResponseSchema.php
        └── UnauthorizedResponseSchema.php

config/
└── l5-swagger.php                   # Swagger configuration

docs/
├── SWAGGER-README.md                # This file
├── SWAGGER-IMPLEMENTATION-GUIDE.md  # Detailed implementation guide
└── SWAGGER-QUICK-REFERENCE.md       # Quick reference cheat sheet

storage/
└── api-docs/
    └── api-docs.json                # Generated OpenAPI specification
```

### Design Principles

The implementation follows:

-   **SOLID**: Single responsibility, composable schemas
-   **DRY**: Reusable schema classes
-   **KISS**: Simple, clear attribute syntax
-   **YAGNI**: Only implement what's needed

## Configuration

### Key Settings

Location: `config/l5-swagger.php`

```php
'title' => 'COTA API Documentation',
'base' => '/api',
'generate_always' => true, // Auto-regenerate in development
```

### Security Scheme

Laravel Sanctum authentication is configured:

-   Type: HTTP Bearer
-   Scheme: Bearer
-   Format: JWT

## Next Steps

### Recommended Documentation Priority

1. ✅ Authentication endpoints (Login - **Done**)
2. 🔄 Authentication endpoints (Register, Logout)
3. 🔄 Feed Schedule CRUD operations
4. 🔄 Feed control endpoints
5. 🔄 Sensor data endpoints
6. 🔄 Pond management
7. 🔄 Notification endpoints

### Maintenance

-   Document new endpoints when created
-   Update schemas when response structures change
-   Keep examples realistic and helpful
-   Test documentation in Swagger UI
-   Review and update guides as needed

## Benefits

### For Frontend Developers

-   Clear API contract
-   Interactive testing
-   Request/response examples
-   No need to read code

### For Backend Developers

-   Documentation lives with code
-   Auto-generated from attributes
-   Single source of truth
-   Easy to maintain

### For Team

-   Reduced communication overhead
-   Faster onboarding
-   Fewer API-related bugs
-   Better collaboration

## Troubleshooting

### Documentation Not Updating

```bash
php artisan cache:clear
php artisan config:clear
php artisan l5-swagger:generate
```

### Can't Access Swagger UI

1. Check route: `php artisan route:list | grep documentation`
2. Verify `storage/api-docs/` directory permissions
3. Check web server configuration

### Authentication Not Working

-   Enter token without "Bearer" prefix
-   Ensure token is valid and not expired
-   Check Sanctum configuration

## Support

-   **Implementation Guide**: [SWAGGER-IMPLEMENTATION-GUIDE.md](./SWAGGER-IMPLEMENTATION-GUIDE.md)
-   **Quick Reference**: [SWAGGER-QUICK-REFERENCE.md](./SWAGGER-QUICK-REFERENCE.md)
-   **OpenAPI Docs**: https://swagger.io/specification/
-   **L5-Swagger**: https://github.com/DarkaOnLine/L5-Swagger

---

**Version**: 1.0.0  
**Last Updated**: October 2024  
**Maintained By**: COTA Development Team
