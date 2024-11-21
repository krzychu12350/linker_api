<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SignalingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\HealthCheckController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']); // Pobieranie danych profilu
    Route::put('/profile', [ProfileController::class, 'update']);    // Aktualizacja profilu

    Route::get('/matches', [MatchController::class, 'index']);
    Route::get('/matches/{id}', [MatchController::class, 'show']);
    Route::post('/users/{id}/report', [MatchController::class, 'report']);



});
Route::post('/send-signal', [ChatController::class, 'initiateCall'])->name(
    'send-signal'
);
Route::get('/create-room', [ChatController::class, 'createRoom']);

Route::get('/health', [HealthCheckController::class, 'healthCheck']);

Route::post('/signal', [SignalingController::class, 'signal']);

Route::post('/send-message', [MessageController::class, 'sendMessage']);

Route::post('chat', [ChatController::class, 'sendMessage']);
Route::get('chat', [ChatController::class, 'getMessages']);

Route::post('/notifications', [NotificationController::class, 'triggerNotification']);

