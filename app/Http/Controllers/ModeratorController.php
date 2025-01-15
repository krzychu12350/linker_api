<?php
namespace App\Http\Controllers;

use App\Models\Moderator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ModeratorController extends Controller
{
public function index()
{
return Moderator::all();
}

public function store(Request $request)
{
$request->validate([
'name' => 'required|string|max:255',
'email' => 'required|email|unique:moderators',
'password' => 'required|string|min:8',
]);

$moderator = new Moderator([
'name' => $request->get('name'),
'email' => $request->get('email'),
'password' => Hash::make($request->get('password')),
]);

$moderator->save();
return response()->json($moderator, 201);
}

public function show($id)
{
return Moderator::findOrFail($id);
}

public function update(Request $request, $id)
{
$moderator = Moderator::findOrFail($id);
$moderator->update($request->all());
return response()->json($moderator, 200);
}

public function destroy($id)
{
$moderator = Moderator::findOrFail($id);
$moderator->delete();
return response()->json(null, 204);
}
}
