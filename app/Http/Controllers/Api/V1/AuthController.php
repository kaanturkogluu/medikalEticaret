<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Create token
        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Kayıt başarılı.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Verilen bilgiler kayıtlarımızla eşleşmiyor.'],
            ]);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Giriş başarılı.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Çıkış yapıldı.'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $request->user(),
            ]
        ]);
    }
}
