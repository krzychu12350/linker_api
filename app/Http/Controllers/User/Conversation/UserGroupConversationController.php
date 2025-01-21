<?php

namespace App\Http\Controllers\User\Conversation;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupConversationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserGroupConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user): JsonResponse
    {
        $user = auth()->user();

        // Fetch group conversations with the last message eagerly loaded
        $userConversations = $user->conversations()
            ->with('users')
            ->where('type', 'group')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => GroupConversationResource::collection($userConversations),
        ]);
    }
}
