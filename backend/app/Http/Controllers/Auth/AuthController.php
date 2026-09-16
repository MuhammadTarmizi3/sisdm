<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['USERNAME' => $request->USERNAME, 'password' => $request->PASSWORD])) {
            $user = Auth::user();

            if (!$user->IS_ACTIVE) {
                Auth::logout();
                return response()->json(['message' => 'Akun tidak aktif'], 403);
            }

            return response()->json([
                'message' => 'Login berhasil',
                'data' => new UserResource($user),
            ]);
        }

        return response()->json(['message' => 'Username atau password salah'], 401);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout berhasil']);
    }

    public function me()
    {
        return response()->json([
            'data' => new UserResource(Auth::user()),
        ]);
    }
}
