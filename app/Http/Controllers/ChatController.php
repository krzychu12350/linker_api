<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validate the incoming message
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        // Store the message in the database (without user authentication)
        $message = Message::create([
            'user_name' => 'Guest',  // Since we don't have an authenticated user
            'message' => $validated['message'],
        ]);

        // Broadcast the message to other users
        event(new MessageSent($message));

        // Return the stored message as response
        return response()->json($message, 201);  // Return the stored message
    }
}
