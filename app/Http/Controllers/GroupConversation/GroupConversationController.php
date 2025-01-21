<?php

namespace App\Http\Controllers\GroupConversation;

use App\Enums\ConversationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupConversation\GroupConversationStoreRequest;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GroupConversationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => Conversation::where('type', ConversationType::GROUP)->get([
                'id',
                'name'
            ]),
        ]);
    }

    public function show(Conversation $group): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $group->load('users')->only([
                'id',
                'name',
                'users'
            ]),
        ]);
    }

    /**
     * Store a newly created conversation and assign users to it.
     */
    public function store(GroupConversationStoreRequest $request): JsonResponse
    {
        // Get the validated data
        $data = $request->validated();

        // Create the conversation
        $conversation = Conversation::create([
            'type' => ConversationType::GROUP,
            'name' => $data['name'] ?: null,
        ]);

        // Attach users to the conversation
        $conversation->users()->attach($data['user_ids']);

        // Return the created conversation as a response
        return response()->json([
            'status' => 'success',
            'message' => 'Conversation created successfully.',
            'data' => $conversation->load('users'), // Load the users relationship
        ], 201);
    }

    /**
     * Remove a group conversation and detach all connected users and messages.
     */
    public function destroy(Conversation $group): JsonResponse
    {
        //  dd($group->toArray());
        // Ensure the conversation is a group conversation
        if ($group->type !== ConversationType::GROUP) {
            return response()->json([
                'status' => 'error',
                'message' => 'Only group conversations can be deleted.',
            ], 400);
        }

        // Delete all related messages and their files
        $group->messages->each(function ($message) {

            // Delete associated files if any
//            if (!$message->files->isEmpty()) {
//                //Storage::delete($message->file_path);
//                //Delete each file of conversation in local/cloudinary enviroment
//            }
            $message->delete();
        });

        // Detach all users from the conversation
        $group->users()->detach();

        // Delete the conversation
        $group->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Group conversation and related messages deleted successfully.',
        ], 200);
    }
}
