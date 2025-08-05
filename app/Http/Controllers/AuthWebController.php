<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthWebController extends Controller
{
    public function registerForm(){
        return view('register');
    }
    public function loginForm(){
        return view('login');
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:54|unique:users,fullname',
            'email' => 'required|email|unique:users,email',
            'no_telepon' => 'required|string|unique:users,no_telepon',
            'password' => 'required|min:6',
            'password_confirm' => 'required|same:password'
        ]);

        $user = User::create([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'],
            'password' => bcrypt($validated['password']),
            'role_id' => 'user',
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil! Silahkan login');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // untuk keamanan sesi
            return redirect()->intended('/')->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Logout berhasil!');
    }
}
