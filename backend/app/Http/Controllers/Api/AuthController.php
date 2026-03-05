<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Registro de usuario + creación de token
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password, // se cifra automáticamente (cast 'hashed')
            'phone' => $request->phone,
            'role' => 'guest',
            'is_active' => true,
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    /**
     * Login + creación de token
     */
    public function login(LoginRequest $request)
    {
        $credenciales = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credenciales)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }
    /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Usuario inactivo',
            ], 403);
        }

        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login correcto',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    /**
     * Revoca el token actual
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token revocado correctamente',
        ]);
    }

    /**
     * Revoca todos los tokens del usuario
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Todos los tokens han sido revocados',
        ]);
    }

    /**
     * Devuelve el usuario autenticado por token
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}