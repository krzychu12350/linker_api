<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreModeratorRequest;
use App\Http\Requests\Admin\User\UpdateModeratorRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the moderators.
     */
    public function index(Request $request)
    {
        $moderators = User::role('moderator')->paginate($request->per_page ?? 10);
        return response()->json($moderators);
    }

    /**
     * Store a newly created moderator.
     */
    public function store(StoreModeratorRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $moderator = User::create($validated);
        $moderator->assignRole('moderator');

        return response()->json($moderator, 201);
    }

    /**
     * Display the specified moderator.
     */
    public function show(User $user)
    {
        if (!$user->hasRole('moderator')) {
            return response()->json(['message' => 'User is not a moderator.'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified moderator.
     */
    public function update(UpdateModeratorRequest $request, User $user)
    {
        if (!$user->hasRole('moderator')) {
            return response()->json(['message' => 'User is not a moderator.'], 404);
        }

        $validated = $request->validated();

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Remove the specified moderator.
     */
    public function destroy(User $user)
    {
        if (!$user->hasRole('moderator')) {
            return response()->json(['message' => 'User is not a moderator.'], 404);
        }
        
        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * Ban a moderator.
     */
    public function ban(User $user, Request $request)
    {
        if (!$user->hasRole('moderator')) {
            return response()->json(['message' => 'User is not a moderator.'], 404);
        }

        $request->validate([
            'banned_until' => 'required|date|after:today',
        ]);

        $user->is_banned = true;
        $user->banned_until = $request->banned_until;
        $user->save();

        return response()->json(['message' => 'Moderator banned successfully.']);
    }

    /**
     * Unban a moderator.
     */
    public function unban(User $user)
    {
        if (!$user->hasRole('moderator')) {
            return response()->json(['message' => 'User is not a moderator.'], 404);
        }

        $user->is_banned = false;
        $user->banned_until = null;
        $user->save();

        return response()->json(['message' => 'Moderator unbanned successfully.']);
    }
}
