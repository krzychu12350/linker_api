<?php

namespace App\Http\Controllers;

use App\Models\Ban;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BanController extends Controller
{
    // Banowanie użytkownika
    public function banUser(Request $request)
    {
        $userId = $request->input('user_id');
        $reason = $request->input('reason', 'No reason provided');
        $bannedUntil = $request->input('banned_until');

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $ban = new Ban([
            'reason' => $reason,
            'banned_until' => $bannedUntil,
        ]);

        $user->bans()->save($ban);

        return response()->json(['message' => 'User banned successfully']);
    }

    // Odbanowanie użytkownika
    public function unbanUser(Request $request)
    {
        $userId = $request->input('user_id');

        $ban = Ban::where('user_id', $userId)->where('banned_until', '>', now())->first();

        if (!$ban) {
            return response()->json(['error' => 'User is not banned'], 404);
        }

        $ban->delete();

        return response()->json(['message' => 'User unbanned successfully']);
    }
}
