<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    // Blokowanie użytkownika
    public function blockUser(Request $request)
    {
        $blockedUserId = $request->input('blocked_id');
        $blockerId = Auth::id();

        if ($blockerId == $blockedUserId) {
            return response()->json(['error' => 'Cannot block yourself'], 400);
        }

        if (Block::where('blocker_id', $blockerId)->where('blocked_id', $blockedUserId)->exists()) {
            return response()->json(['error' => 'User is already blocked'], 400);
        }

        Block::create(['blocker_id' => $blockerId, 'blocked_id' => $blockedUserId]);

        return response()->json(['message' => 'User blocked successfully']);
    }

    // Odblokowanie użytkownika
    public function unblockUser(Request $request)
    {
        $blockedUserId = $request->input('blocked_id');
        $blockerId = Auth::id();

        $block = Block::where('blocker_id', $blockerId)->where('blocked_id', $blockedUserId)->first();

        if (!$block) {
            return response()->json(['error' => 'No block found'], 404);
        }

        $block->delete();

        return response()->json(['message' => 'User unblocked successfully']);
    }

    // Lista zablokowanych użytkowników
    public function getBlockedUsers()
    {
        $blockedUsers = Auth::user()->blockedUsers()->with('blocked')->get();
        return response()->json($blockedUsers);
    }
}

