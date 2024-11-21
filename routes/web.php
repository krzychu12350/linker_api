<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});



Route::post('/broadcasting/auth', function (Request $request) {
    // Check if the user is authenticated
    if (Auth::check()) {
        return Broadcast::auth($request);
    }

    // If not authenticated, return a 401 Unauthorized response
    return response()->json(['message' => 'Unauthorized'], 401);
});

