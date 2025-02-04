<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthControllerOld extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
//            'first_name' => 'required|string|max:255',
//            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);
    
        $user = User::create([
//            'first_name' => $validated['first_name'],
//            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole(UserRole::USER->value);

        return response()->json(['user' => $user], 201);
    }
    

    public function login(LoginRequest $request)
    {
        // Validate the login request
//        $credentials = $request->validate([
//            'email' => 'required|email',
//            'password' => 'required|string',
//        ]);
        $credentials = $request->validated();

            // Manually check the user's credentials using the User model
        $user = User::where('email', $credentials['email'])->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Create a token for the authenticated user
        $token = $user->createToken('auth_token')->plainTextToken;

        $photoUrl = $user->photos->isEmpty() ? "" :
           // "https://res.cloudinary.com/dm4zof0l0/image/upload/v1734207746/" .
            $user->photos->first()->url;

        // Return the user data along with the token
        return response()->json([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'photo' =>  $photoUrl,
                'role' => $user->getRoleNames()?->first()
        ],
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
