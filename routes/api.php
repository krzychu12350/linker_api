<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Detail\DetailController;
use App\Http\Controllers\GroupConversation\GroupConversationController;
use App\Http\Controllers\GroupConversation\Message\GroupConversationMessageController;
use App\Http\Controllers\GroupConversation\User\GroupConversationUserController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\Swipe\SwipeController;
use App\Http\Controllers\User\Conversation\ConversationController;
use App\Http\Controllers\User\Conversation\UserGroupConversationController;
use App\Http\Controllers\User\Detail\UserDetailController;
use App\Http\Controllers\User\Photo\UserProfilePhotoController;
use App\Http\Controllers\User\Profile\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
//    Route::get('/profile', [UserProfileController::class, 'getProfile']); // Pobieranie danych profilu
//    Route::put('/profile', [UserProfileController::class, 'update']);    // Aktualizacja profilu


    Route::prefix('/users/{user}')->group(function () {
        Route::get('/profile', [UserProfileController::class, 'show']);
        Route::put('/profile', [UserProfileController::class, 'update']);

        Route::get('/photos', [UserProfilePhotoController::class, 'index']);
        Route::post('/photos', [UserProfilePhotoController::class, 'update']);
        Route::delete('/photos/{id}', [UserProfilePhotoController::class, 'destroy']);

        Route::apiResource('conversations.messages',MessageController::class )->shallow(false);


        Route::apiResource('conversations', ConversationController::class)->only([
            'index',
            'show',
        ]);

        Route::get('/groups', [UserGroupConversationController::class,'index'])->name('user.groups');

//        Route::prefix('/conversations/{conversation}')->group(function () {
//            Route::apiResource('messages', MessageController::class)->only([
//                'index',
//                'store',
//            ]);
//        });

        Route::get('/swipe-data', [SwipeController::class, 'show']);
    });


    Route::get('/matches', [MatchController::class, 'index']);
    Route::get('/matches/{id}', [MatchController::class, 'show']);
    Route::post('/users/{id}/report', [MatchController::class, 'report']);


    Route::apiResource('details', DetailController::class)->only([
        'index',
    ]);

    Route::apiResource('users.details', UserDetailController::class)->only([
        'index',
        'store'
    ]);

    Route::apiResource('swipes', SwipeController::class)->only([
        'index',
        'store',
    ]);

    Route::get('/swipes/matches', [SwipeController::class, 'getMatchedSwipes']);

    // Group conversations
    Route::apiResource('groups', GroupConversationController::class);

    // Group conversation users
    Route::prefix('/groups/{group}')->group(function () {
        Route::post('/users', [GroupConversationUserController::class, 'store']);
        Route::delete('/users', [GroupConversationUserController::class, 'destroy']);

        Route::apiResource('messages', GroupConversationMessageController::class);
    });
});

//Route::apiResource('profiles', ProfileController::class)->only([
//    'index'
//]);


Route::get('/health', [HealthCheckController::class, 'healthCheck']);