<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
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
}
