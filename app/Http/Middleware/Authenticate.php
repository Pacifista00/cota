<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Untuk API request, return null (tidak redirect)
        // Ini akan trigger AuthenticationException yang di-handle oleh Handler
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }
        
        // Untuk web request, redirect ke login
        return route('login');
    }
}