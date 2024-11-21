<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function triggerNotification(Request $request)
    {
        // You could validate and sanitize your input here.
        $message = $request->input('message');

        $message = Message::create([
            'user_name' => 'Guest',  // Since we don't have an authenticated user
            'message' => $message,
        ]);
        // Broadcast the event with the message
        //broadcast(new NotificationEvent($message));
        //'Kuracjusz z lewego łóżka potrzebuje pomocy. Udaj się do pierwszego domku. ID domku: #1.'
        event(new NotificationEvent($message->message));

        return response()->json($message, 201);  // Return the stored message
    }
}
