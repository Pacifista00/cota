<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/register',
        summary: 'User Registration',
        description: 'Register a new user account with email, password, and profile information',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User registration data',
            content: new OA\JsonContent(ref: '#/components/schemas/RegisterRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Registration successful',
                content: new OA\JsonContent(ref: '#/components/schemas/RegisterResponse')
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
    public function register(Request $request){
        try {
            $request->validate([
                'fullname' => 'required|unique:users|max:54',
                'email' => 'required|email|unique:users',
                'no_telepon' => 'required|unique:users',
                'password' => 'required',
                'password_confirm' => 'required|same:password'
            ]);

            $user = User::create([
                'fullname' => $request->fullname,
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'password' => bcrypt($request->password),
                'role_id' => 'user',
            ]);

            return response()->json([
                "data" => [
                    "message" => "Register berhasil!",
                    "status" => 201
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                // 'errors' => $e->errors(),
                'status' => 422
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registrasi gagal',
                // 'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

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
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('token');

            return response()->json([
                "data" => [
                    "message" => "Login berhasil!",
                    "status" => 200,
                    "token" => $token->plainTextToken
                ]
            ],200);
        }

        return response()->json([
            "data" => [
                "message" => "Login Failed!",
                "status" => 401
            ]
        ],401);
    }
    
    #[OA\Post(
        path: '/logout',
        summary: 'User Logout',
        description: 'Invalidate the current user authentication token',
        security: [['sanctum' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout successful',
                content: new OA\JsonContent(ref: '#/components/schemas/LogoutResponse')
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
            new OA\Response(
                response: 500,
                description: 'Logout failed',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')
            ),
        ]
    )]
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user || !$user->currentAccessToken()) {
                return response()->json([
                    "data" => [
                        "message" => "Logout gagal. Kamu belum login!",
                        "status" => 400
                    ]
                ], 400);
            }

            $user->currentAccessToken()->delete();

            return response()->json([
                "data" => [
                    "message" => "Logout berhasil!",
                    "status" => 200
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "data" => [
                    "message" => "Logout fgagal. " . $e->getMessage(),
                    "status" => 500
                ]
            ], 500);
        }
    }

}
