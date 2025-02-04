<?php

use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Detail\DetailController;
use App\Http\Controllers\GroupConversation\GroupConversationController;
use App\Http\Controllers\GroupConversation\Message\GroupConversationMessageController;
use App\Http\Controllers\GroupConversation\User\GroupConversationUserController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\Message\MessageController;
use App\Http\Controllers\Swipe\SwipeController;
use App\Http\Controllers\TwilioVideoController;
use App\Http\Controllers\User\Ban\BanController;
use App\Http\Controllers\User\Block\BlockController;
use App\Http\Controllers\User\Conversation\ConversationController;
use App\Http\Controllers\User\Conversation\UserGroupConversationController;
use App\Http\Controllers\User\Detail\UserDetailController;
use App\Http\Controllers\User\Photo\UserProfilePhotoController;
use App\Http\Controllers\User\Preference\PreferenceController;
use App\Http\Controllers\User\Profile\UserProfileController;
use App\Http\Controllers\User\Report\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::prefix('/password/reset')->group(function () {
        Route::post('/email', [PasswordResetController::class, 'sendPasswordResetEmail']);
        Route::post('', [PasswordResetController::class, 'resetPassword']);
    });
});

// ROLE COMMON USER
Route::middleware('auth:sanctum')->group(function () {
//    Route::get('/profile', [UserProfileController::class, 'getProfile']); // Pobieranie danych profilu
//    Route::put('/profile', [UserProfileController::class, 'update']);    // Aktualizacja profilu


    Route::prefix('/users/{user}')->group(function () {
        Route::get('/profile', [UserProfileController::class, 'show']);
        Route::put('/profile', [UserProfileController::class, 'update']);

        Route::get('/photos', [UserProfilePhotoController::class, 'index']);
        Route::post('/photos', [UserProfilePhotoController::class, 'update']);
        Route::delete('/photos/{id}', [UserProfilePhotoController::class, 'destroy']);

        Route::apiResource('conversations.messages', MessageController::class)
            ->only([
                'index',
                'store'
            ])
            ->shallow(false);


        Route::apiResource('conversations', ConversationController::class)->only([
            'index',
            'show',
        ]);

        Route::get('/groups', [UserGroupConversationController::class, 'index'])->name('user.groups');

//        Route::prefix('/conversations/{conversation}')->group(function () {
//            Route::apiResource('messages', MessageController::class)->only([
//                'index',
//                'store',
//            ]);
//        });


        Route::get('/swipe-data', [SwipeController::class, 'show']);

        Route::apiResource('blocks', BlockController::class)->except([
            'update'
        ]);

        Route::post('/ban', [BanController::class, 'banUser']);
        Route::post('/unban', [BanController::class, 'unbanUser']);


        Route::apiResource('preferences', PreferenceController::class)->only([
            'index',
            'store',
        ]);

//        Route::post('/blocks', [BlockController::class, 'blockUser']);        // Create a block (block user)
//        Route::delete('/blocks/{blocked_id}', [BlockController::class, 'unblockUser']); // Delete a block (unblock user)

        // Endpoint for users to submit reports
        Route::apiResource('reports', ReportController::class)->only([
            'index',
            'store'
        ]);

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
    Route::apiResource('groups', GroupConversationController::class)
        ->except('update');

    // Group conversation users
    Route::prefix('/groups/{group}')->group(function () {
        Route::post('/users', [GroupConversationUserController::class, 'store']);
        Route::delete('/users', [GroupConversationUserController::class, 'destroy']);

        Route::apiResource('messages', GroupConversationMessageController::class)->only([
            'index',
            'store'
        ]);

        Route::patch('/name', [GroupConversationController::class, 'updateName']);
    });


    // ROLE ADMIN/MODERATOR
    Route::middleware(['auth:sanctum', 'admin'])->prefix('/admin')->group(function () {
        //Reports management

        // Endpoint for fetching reports with pagination
        Route::apiResource('/reports', AdminReportController::class)
            ->except(['update'])
            ->names([
                'index'   => 'admin.reports.index',    // GET /reports
                'store'   => 'admin.reports.store',    // POST /reports
                'show'    => 'admin.reports.show',     // GET /reports/{id}
                'destroy' => 'admin.reports.destroy',  // DELETE /reports/{id}
                'create'  => 'admin.reports.create',   // (Optional - if using forms)
                'edit'    => 'admin.reports.edit',     // (Optional - if using forms)
            ]);


        // Endpoint for admin to update the report status
        Route::patch('/reports/{id}/status', [AdminReportController::class, 'updateStatus'])
            ->name('admin.reports.update.status');

        //User management
    });


});

//Route::apiResource('profiles', ProfileController::class)->only([
//    'index'
//]);


Route::get('/health', [HealthCheckController::class, 'healthCheck']);

Route::post('/twilio/token', [TwilioVideoController::class, 'generateToken']);