<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $this->validateLogin($request);

            if (!Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => ['Credenciales inválidas.'],
                ]);
            }

            /** @var User $user */
            $user = $request->user();

            $token = $user->createToken('spa-access');

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token->plainTextToken,
                    'user' => [
                        'name' => $user->name,
                        'role' => $user->role,
                    ],
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesión.',
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            /** @var User|null $user */
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();
            }

            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión.',
            ], 500);
        }
    }

    private function validateLogin(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string'],
        ]);
    }
}
