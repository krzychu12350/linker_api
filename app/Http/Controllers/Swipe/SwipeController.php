<?php

namespace App\Http\Controllers\Swipe;

use App\Enums\ConversationType;
use App\Enums\SwipeType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Swipe\StoreSwipeRequest;
use App\Http\Resources\SwipeResource;
use App\Http\Resources\MatchedSwipeResource;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\Swipe;
use App\Models\SwipeMatch;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        $users = User::with(['photos'])
            ->where('id', '!=', $userId) // Exclude current user
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('swipe_id_2')
                    ->from('swipe_matches')
                    ->where('swipe_id_1', $userId)
                    ->union(
                        SwipeMatch::select('swipe_id_1')
                            ->where('swipe_id_2', $userId)
                    );
            })
            ->get();

        return SwipeResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSwipeRequest $request)
    {
        // The validated data from the request
        $validated = $request->validated();

        // Get the authenticated user
        $user = auth()->user();

        // Ensure the swiped user exists
        $swipedUser = User::find($validated['swiped_user_id']);
        if (!$swipedUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'The user you swiped on does not exist.'
            ], 404);
        }

        // Check if the swipe already exists
        $swipe = Swipe::firstOrCreate(
            [
                'user_id' => $validated['user_id'],
                'swiped_user_id' => $validated['swiped_user_id'],
            ],
            [
                'type' => $validated['type'],
            ]
        );

        // Check if the other user has swiped back
        $hasMatch = Swipe::where('user_id', $validated['swiped_user_id'])
            ->where('swiped_user_id', $validated['user_id'])
            ->whereIn('type', [SwipeType::RIGHT, SwipeType::UP]) // Check for mutual right or up swipes
            ->exists();

        if ($hasMatch) {
            // Ensure that we have a unique match
            $swipeMatch = SwipeMatch::firstOrCreate([
                'swipe_id_1' => $validated['user_id'],
                'swipe_id_2' => $validated['swiped_user_id'],
            ]);

            // Create a conversation between the two matched users
            $conversation = Conversation::create([
                'match_id' => $swipeMatch->id,
                'type' => ConversationType::USER, // Assuming a PRIVATE type exists in ConversationType
            ]);

            // Add the current user to the conversation
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $validated['user_id'],
                'is_admin' => false, // By default, the user is not an admin
            ]);

            // Add the swiped user to the conversation
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $validated['swiped_user_id'],
                'is_admin' => false, // By default, the user is not an admin
            ]);

            // Return success response with matched users
            return response()->json([
                'status' => 'success',
                'message' => 'Match and conversation created',
                'data' => [
                    'current_user' => new SwipeResource(User::find($validated['user_id'])),
                    'matched_user' => new SwipeResource(User::find($validated['swiped_user_id'])),
                ]
            ], 201);
        }

        // If no match, just return a success message
        return response()->json([
            'status' => 'success',
            'message' => 'Swipe added successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getMatchedSwipes(): AnonymousResourceCollection
    {
        // Get the authenticated user
        $user = auth()->user();

        // Retrieve all swipeMatches where the current user's ID is swipe_id_1 or swipe_id_2
        //$swipes = $user->swipeMatches()->get();
        $swipes = SwipeMatch::all();
        //dd(  SwipeMatch::all()->toArray());
        // Initialize an array to store matched users where swipe_id_2 matches
        $matchedUsers = [];
        // Loop through each swipeMatch
        foreach ($swipes as $swipe) {
            // Check if swipe_id_2 matches the authenticated user's ID
            if ($swipe->swipe_id_1 == $user->id) {

                // Find the user who swiped on the authenticated user (where swipe_id_1 matches)
                $matchedUser = User::find($swipe->swipe_id_2);

                // Add the matched user to the array
                $matchedUsers[] = $matchedUser;
            } elseif ($swipe->swipe_id_2 == $user->id) {

                // Find the user who swiped on the authenticated user (where swipe_id_1 matches)
                $matchedUser = User::find($swipe->swipe_id_1);

                // Add the matched user to the array
                $matchedUsers[] = $matchedUser;
            }
        }

        // Return matched users with the specified relationships
        return MatchedSwipeResource::collection($matchedUsers);
    }
}
