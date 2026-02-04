<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth; // Import the Facade

class AuthController extends Controller
{
    /**
     * Register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'gender'         => 'required|string|max:255',
            'date_of_birth'  => 'required|date',
            'phone'          => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8',
        ]);

        $user = User::create([
            'first_name'     => $validated['first_name'],
            'last_name'      => $validated['last_name'],
            'gender'         => $validated['gender'],
            'date_of_birth'  => $validated['date_of_birth'],
            'phone'          => $validated['phone'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
        ]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token, 'User registered successfully', 201);
    }
    //Register Admin
    public function registerAdmin(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'gender'         => 'required|string|max:255',
            'date_of_birth'  => 'required|date',
            'phone'          => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:8',
            'role'           => 'required|string|in:admin',
        ]);

        $user = User::create([
            'first_name'     => $validated['first_name'],
            'last_name'      => $validated['last_name'],
            'gender'         => $validated['gender'],
            'date_of_birth'  => $validated['date_of_birth'],
            'phone'          => $validated['phone'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => $validated['role'],
        ]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token, 'User registered successfully', 201);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        return $this->respondWithToken($token, 'Login successful');
    }

    /**
     * Token response
     * Using config() instead of factory() to avoid compatibility errors
     */
    protected function respondWithToken($token, $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'user' => auth('api')->user(),
                'access_token' => $token,
                'token_type'   => 'bearer',
                // This replaces factory() and works on all versions
                'expires_in'   => config('jwt.ttl') * 60 
            ],
        ], $status);
    }

    /**
     * Logout
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}