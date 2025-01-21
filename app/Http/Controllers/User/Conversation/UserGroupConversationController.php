<?php

namespace App\Http\Controllers\User\Conversation;

use App\Enums\ConversationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupConversation\GroupConversationStoreRequest;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Js;

class UserGroupConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user): JsonResponse
    {
        $user = auth()->user();

        $userConversations = $user->conversations()->where('type', 'group')->get();

        return response()->json([
            'status' => 'success',
            'data' => $userConversations,
        ]);
    }

}
