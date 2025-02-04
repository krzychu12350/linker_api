<?php

namespace App\Http\Controllers\GroupConversation\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupConversation\Event\StoreEventRequest;
use App\Models\Conversation;
use App\Models\Event;

class EventController extends Controller
{
    /**
     * Get a list of events for a particular conversation.
     *
     * @param  \App\Models\Conversation  $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Conversation $group)
    {
        // Get all events related to the specific conversation
        $events = $group->events; // This loads all the events with their related polls and users

        // Add the grouped votes for each event
        $events = $events->map(function ($event) {
            // Fetch the grouped votes for the event using the votes() method in the Event model
            $event->votes = $event->votes();
            return $event;
        });

        // Return the events along with the grouped votes (if needed, you can customize the resource here)
        return response()->json($events);
    }

    // Get event with poll results
    public function show(Conversation $group, Event $event)
    {
        $event->load('polls.user');

        return response()->json($event);
    }

    // Create a new event
    public function store(StoreEventRequest $request, Conversation $group)
    {
        $event = $group->events()->create($request->validated());

        return response()->json(['message' => 'Event created successfully', 'event' => $event], 201);
    }

}
