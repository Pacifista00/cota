<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    /**
     * Get authenticated user's profile
     * GET /api/user/profile
     */
    #[OA\Get(
        path: '/user/profile',
        summary: 'Get User Profile',
        description: 'Retrieve authenticated user\'s profile information',
        security: [['sanctum' => []]],
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User profile retrieved successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/UserProfileResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized - Token tidak valid atau expired',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Token tidak valid atau expired'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found',
                content: new OA\JsonContent(ref: '#/components/schemas/UserNotFoundResponse')
            ),
        ]
    )]
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        // Edge case: Check if user still exists in database
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json(
            new UserResource($user)
        );
    }
}

