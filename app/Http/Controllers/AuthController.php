<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth; // Import the Facade
use Exception;

class AuthController extends Controller
{
    /**
     * Register
     */
    public function registerUser(Request $request)
    {
        try {
            // Log for debugging
            Log::info('--- New Registration Attempt ---');
            Log::info('Content-Type: ' . $request->header('Content-Type'));
            Log::info('Raw Body: ' . $request->getContent());
            Log::info('Data:', $request->all());

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
                'role'           => 'user', // Set default role
            ]);

            Log::info("User Registered: {$user->email}");

            $token = auth('api')->login($user);

            return $this->respondWithToken($token, 'User registered successfully', 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Registration failed', 'error' => $e->getMessage()], 500);
        }
    }
    //Register Admin
    public function registerAdmin(Request $request)
    {
        try {
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
                'phone'          => $validated['phone'],
                'date_of_birth'  => $validated['date_of_birth'],
                'email'          => $validated['email'],
                'password'       => Hash::make($validated['password']),
                'role'           => $validated['role'],
            ]);

            Log::info("Admin Registered: {$user->email}");

            $token = auth('api')->login($user);

            return $this->respondWithToken($token, 'Admin registered successfully', 201);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Admin registration failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        try {
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

            Log::info("User Logged In: {$credentials['email']}");

            return $this->respondWithToken($token, 'Login successful');
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Login failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile()
    {
        try {
            return response()->json([
                'success' => true,
                'data' => auth('api')->user()
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve profile', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Refresh a token
     */
    public function refresh()
    {
        try {
            return $this->respondWithToken(auth('api')->refresh(), 'Token refreshed successfully');
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Token refresh failed', 'error' => $e->getMessage()], 500);
        }
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
        try {
            auth('api')->logout();
            return response()->json(['success' => true, 'message' => 'Successfully logged out']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Logout failed', 'error' => $e->getMessage()], 500);
        }
    }
}