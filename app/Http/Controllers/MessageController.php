<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'to' => 'required|string',
            'content' => 'required|string',
        ]);

        // You can get the current logged-in user if needed
        $from = auth()->user()->id ?? 'user123'; // Assuming 'user123' is a placeholder if no user is logged in
        $to = $request->to;
        $content = "wwew";

        // Trigger the event to broadcast the message
        event(new MessageSent($from, $content, $to));

        return response()->json(['message' => 'Message sent']);
    }
}
