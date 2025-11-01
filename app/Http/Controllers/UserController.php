<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Delete authenticated user's account
     * DELETE /api/user/account
     */
    #[OA\Delete(
        path: '/user/account',
        summary: 'Delete User Account',
        description: 'Permanently delete authenticated user\'s account with password confirmation',
        security: [['sanctum' => []]],
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Password confirmation for account deletion',
            content: new OA\JsonContent(ref: '#/components/schemas/DeleteAccountRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account deleted successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/DeleteAccountResponse')
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized - Wrong password or invalid token',
                content: new OA\JsonContent(ref: '#/components/schemas/DeleteAccountUnauthorized')
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')
            ),
        ]
    )]
    public function destroy(DeleteAccountRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Edge case: Check if user still exists in database
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Verify password
            if (!Hash::check($request->validated()['password'], $user->password)) {
                return response()->json([
                    'message' => 'Password salah'
                ], 401);
            }

            // Use transaction for atomicity
            DB::transaction(function () use ($user) {
                // Revoke all tokens (logout from all devices)
                $user->tokens()->delete();
                
                // Delete user (cascade will handle related data)
                $user->delete();
            });

            return response()->json([
                'message' => 'Akun berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus akun: ' . $e->getMessage()
            ], 500);
        }
    }
}

