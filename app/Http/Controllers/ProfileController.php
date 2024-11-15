<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'bio' => $user->bio,
            'interests' => $user->interests,
            'preferences' => $user->preferences,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 200);
    }
    
    public function update(Request $request)
    {
        $user = $request->user();
    
        $validated = $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'bio' => 'string|nullable',
            'interests' => 'array|nullable',
            'preferences' => 'array|nullable',
        ]);
    
        $user->update($validated);
    
        return response()->json(['user' => $user], 200);
    }
}
