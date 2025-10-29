<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
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

    /**
     * Update authenticated user's profile
     * PUT /api/user/profile
     */
    #[OA\Put(
        path: '/user/profile',
        summary: 'Update User Profile',
        description: 'Update authenticated user\'s profile information (partial update supported)',
        security: [['sanctum' => []]],
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Profile update data (all fields optional)',
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateProfileRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/UpdateProfileResponse')
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
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/UpdateProfileValidationError')
            ),
        ]
    )]
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        // Edge case: Check if user still exists in database
        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Get only the fields that are present in the request
        $updateData = [];
        
        if ($request->filled('fullname')) {
            $updateData['fullname'] = $request->validated()['fullname'];
        }
        
        if ($request->filled('email')) {
            $updateData['email'] = $request->validated()['email'];
        }
        
        if ($request->filled('no_telepon')) {
            $updateData['no_telepon'] = $request->validated()['no_telepon'];
        }

        // Update user with partial data
        $user->update($updateData);

        // Refresh user to get updated timestamps
        $user->refresh();

        return response()->json([
            'message' => 'Profile berhasil diperbarui',
            'data' => new UserResource($user)
        ]);
    }
}

