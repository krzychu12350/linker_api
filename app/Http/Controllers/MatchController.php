<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $matches = User::query()
            ->where('id', '!=', $user->id)
            ->whereJsonContains('interests', $user->interests)
            ->paginate(10);

        return response()->json($matches);
    }

    public function show($id)
    {
        $match = User::findOrFail($id);
        return response()->json($match);
    }

    public function report(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        // Tu implementacja logiki raportowania.

        return response()->json(['message' => 'User reported successfully'], 200);
    }
}
