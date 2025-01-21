<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckIfBlocked
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $targetUserId = $request->route('user_id');

        if ($user->hasBlocked(User::find($targetUserId)) || User::find($targetUserId)->hasBlocked($user)) {
            return response()->json(['error' => 'You are blocked from interacting with this user'], 403);
        }

        return $next($request);
    }
}

