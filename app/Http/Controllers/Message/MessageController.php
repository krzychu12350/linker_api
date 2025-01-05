<?php

namespace App\Http\Controllers\Message;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(int $userId, int $conversationId)
    {
       // dd($userId, $conversationId);
        $conversation = Conversation::findOrFail($conversationId);
        $messages = $conversation->messages()->get();

        return  MessageResource::collection($messages);
    }

    // Store a new message for a conversation
    public function store(StoreMessageRequest $request, int $userId, int $conversationId)
    {
       // dd($userId, $conversationId);
        // Validate the incoming request
//        $request->validate([
//            'body' => 'required|string', // Make sure body is a string and required
//        ]);
        $validatedData  = $request->validated();

        // Find the conversation or fail if it doesn't exist
        $conversation = Conversation::findOrFail($conversationId);

        // Create a new message and associate it with the conversation and authenticated user
        $message = Message::create([
            'body' => $request->body,
            'conversation_id' => $conversation->id,
            'sender_id' => $validatedData['sender_id'], // Use the authenticated user's ID as the sender
            'receiver_id' =>  $validatedData['receiver_id'], // Get the other participant
        ]);

        // Trigger the event to broadcast the message
        event(new MessageSent($message));

     //   dd( $message);
        // Return the newly created message as a resource
        return new MessageResource($message);
    }

}
