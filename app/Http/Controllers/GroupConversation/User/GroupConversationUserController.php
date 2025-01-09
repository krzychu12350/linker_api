<?php

namespace App\Http\Controllers\GroupConversation\User;

use App\Enums\ConversationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupConversation\User\GroupConversationUserRequest;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;

class GroupConversationUserController extends Controller
{
    /**
     * Add multiple users to a specific group conversation.
     */
    public function store(GroupConversationUserRequest $request, Conversation $group): JsonResponse
    {
        // Get validated user IDs from the request
        $userIds = $request->validated()['user_ids'];

        // Ensure the conversation is a group conversation
        if ($group->type !== ConversationType::GROUP) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only group conversations can have users added.',
            ], 400);
        }

        // Filter out users who are already in the conversation
        $existingUserIds = $group->users()->pluck('user_id')->toArray();
        $newUserIds = array_diff($userIds, $existingUserIds);

        if (empty($newUserIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'All specified users are already part of this conversation.',
            ], 409);
        }

        // Add the new users to the conversation
        $group->users()->attach($newUserIds);

        return response()->json([
            'status' => 'success',
            'message' => 'Users added to the conversation successfully.',
        ], 200);
    }

    /**
     * Remove multiple users from a specific group conversation.
     */
    public function destroy(GroupConversationUserRequest $request, Conversation $group): JsonResponse
    {
        // Get validated user IDs from the request
        $userIds = $request->validated()['user_ids'];

        // Ensure the conversation is a group conversation
        if ($group->type !== ConversationType::GROUP) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only group conversations can have users removed.',
            ], 400);
        }

        // Filter out users who are not in the conversation
        $existingUserIds = $group->users()->pluck('user_id')->toArray();
        $removableUserIds = array_intersect($userIds, $existingUserIds);

        if (empty($removableUserIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'None of the specified users are part of this conversation.',
            ], 404);
        }

        // Remove the users from the conversation
        $group->users()->detach($removableUserIds);

        return response()->json([
            'status' => 'success',
            'message' => 'Users removed from the conversation successfully.',
        ], 200);
    }
}
