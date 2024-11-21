<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Broadcast::channel('voice-chat.{userId}', function ($user, $userId) {
//    // Check if the authenticated user is allowed to interact with userId
//    // For example, you can validate if they are in the same group, room, or friends list
//    return $user->canListenToUser($userId); // Custom logic
//});

//Broadcast::channel('voice-chat.{userId}', function ($user, $userId) {
//    return (int) $user->id === (int) $userId;  // Only allow the user to join their private channel
//});
//
//Broadcast::channel('private-voice-chat.{userId}', function ($user, $userId) {
//    return (int) $user->id === (int) $userId; // Only allow the intended user to listen
//});

//Broadcast::channel('voice-chat.{userId}', function ($user, $userId) {
//    // This is a public channel, but you can still check some basic authorization if needed
//    return (int) $user->id === (int) $userId;
//});

//Broadcast::channel('voice-call.{callee}', function ($user, $callee) {
//    // Only allow the callee to listen to their channel
//    return (int) $user->id === (int) $callee; // or use a different condition
//});
//
//Broadcast::channel('chat.{userId}', function ($user, $userId) {
//    return (int) $user->id === (int) $userId;
//});

//Broadcast::channel('chat', function ($user) {
//    return true;  // Allow all users to listen on the "chat" channel
//});

Broadcast::channel('notifications', function () {
    return true;
});
