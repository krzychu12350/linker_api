<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Strategies\AuthenticationStrategy\AuthStrategy;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthStrategy $authStrategy;

    // You can inject different strategies depending on the request type
    public function __construct(AuthStrategy $authStrategy)
    {
        $this->authStrategy = $authStrategy;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated(); // Get the validated data

        try {
            $result = $this->authStrategy->register($data); // Use the authentication strategy to handle registration
            return response()->json($result, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400); // Handle any exceptions
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        try {
            $result = $this->authStrategy->login($credentials);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }
}

