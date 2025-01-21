<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ModeratorController extends Controller
{
    // Pobierz wszystkich moderatorów (z filtrem roli, jeśli podany w query param)
    public function index(Request $request)
    {
        $role = $request->query('role');

        if ($role) {
            $users = User::role($role)->get(); // Używa spatie/laravel-permission
        } else {
            $users = User::all();
        }

        return response()->json($users);
    }

    // Pobierz informacje o konkretnym moderatorze
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // Utwórz nowego moderatora
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $moderatorRole = Role::where('name', 'moderator')->first();

        if (!$moderatorRole) {
            return response()->json(['message' => 'Role moderator not found'], 404);
        }

        $user->assignRole($moderatorRole);

        return response()->json($user, 201);
    }

    // Usuń moderatora lub użytkownika
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->hasRole(['user', 'moderator'])) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        }

        return response()->json(['message' => 'User does not have the required role'], 400);
    }
}

