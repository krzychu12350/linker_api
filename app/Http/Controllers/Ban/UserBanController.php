<?php

namespace App\Http\Controllers\Ban;

use App\Enums\UserBanStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserBanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $user->update([
            'is_banned' => UserBanStatus::BANNED,
        ]);

        return response()->json([
            'message' => 'Status updated successfully'
        ]);
//        // Pobieramy status z żądania (oczekujemy np. 'ban' lub 'unban')
//        $action = $request->input('action');
//
//        if ($action === 'ban') {
//            if ($user->is_banned == UserBanStatus::BANNED) {
//                return response()->json([
//                    'message' => 'User is already banned'
//                ]);
//            }
//
//            $user->update([
//                'is_banned' => UserBanStatus::BANNED,
//            ]);
//
//            return response()->json([
//                'message' => 'User has been banned successfully'
//            ]);
//        } elseif ($action === 'unban') {
//            if ($user->is_banned == UserBanStatus::UNBANNNED) {
//                return response()->json([
//                    'message' => 'User is already unbanned'
//                ]);
//            }
//
//            $user->update([
//                'is_banned' => UserBanStatus::UNBANNNED,
//            ]);
//
//            return response()->json([
//                'message' => 'User has been unbanned successfully'
//            ]);
//        }
//
//        return response()->json([
//            'message' => 'Invalid action. Use "ban" or "unban".'
//        ], 400);
//    }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
