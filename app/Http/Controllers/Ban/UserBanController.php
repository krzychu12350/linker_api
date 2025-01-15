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
        // Pobieramy akcję z żądania ('ban' lub 'unban')
        $action = $request->input('action');

        // Sprawdzamy, czy akcja jest poprawna
        if (!in_array($action, ['ban', 'unban'])) {
            return response()->json([
                'message' => 'Invalid action. Use "ban" or "unban".'
            ], 400);
        }

        // Ustawiamy odpowiedni status na podstawie akcji
        $newStatus = $action === 'ban' ? UserBanStatus::BANNED : UserBanStatus::UNBANNNED;

        // Sprawdzamy, czy użytkownik ma już ten status
        if ($user->is_banned == $newStatus) {
            $statusMessage = $action === 'ban' ? 'User is already banned' : 'User is already unbanned';

            return response()->json([
                'message' => $statusMessage
            ]);
        }

        // Aktualizujemy status użytkownika
        $user->update([
            'is_banned' => $newStatus,
        ]);

        $successMessage = $action === 'ban' ? 'User has been banned successfully' : 'User has been unbanned successfully';

        return response()->json([
            'message' => $successMessage
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
