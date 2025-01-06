<?php

namespace App\Http\Controllers\Swipe;

use App\Enums\SwipeType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Swipe\StoreSwipeRequest;
use App\Http\Resources\MatchUserResource;
use App\Http\Resources\SwipeResource;
use App\Models\Swipe;
use App\Models\SwipeMatch;
use App\Models\User;
use App\Services\UserInterestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwipeController extends Controller
{
    public function __construct(private readonly UserInterestService $userInterestService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
//        dd(Auth::id());
        $users = User::with(['photos'])
            ->where('id', '!=', Auth::id() ?? $user->id) // Exclude the current authenticated user
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
//        dd($validated);

        // Get the authenticated user
        $user = auth()->user();
//        dd([
//            'user_id' => $user->id,
//            'swiped_user_id' => $validated['swiped_user_id'],
//            'type' => $validated['type'],
//        ]);
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
            ->where('type', SwipeType::RIGHT) // Assuming SwipeType has a method to get the opposite type
            ->orWhere('type', SwipeType::UP) // Assuming SwipeType has a method to get the opposite type
            ->exists();

        // If the other user has swiped back, create a match
        if ($hasMatch) {
            // Ensure that we have a unique match
            $swipeMatch = SwipeMatch::firstOrCreate([
                'swipe_id_1' => $validated['user_id'],
                'swipe_id_2' => $validated['swiped_user_id'],
            ]);

//            dd('has match');

            return response()->json([
                'status' => 'success',
                'message' => 'Match',
                'data' => [
                    'current_user' => new MatchUserResource(User::find($validated['user_id'])),
                    'matched_user' => new MatchUserResource(User::find($validated['swiped_user_id'])),
                ]
            ], 201);
            // create new conversation between these two users

            // broadcast event to fronend via pusher about new match for current logged in user

        }

        return response()->json([
            'status' => 'success',
            'message' => 'Swipe added successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // dd($user->toArray());

           // dd( $this->userInterestService->getUserSelectedOptionForEachGroup($user));
        return response()->json([
            'status' => 'success',
            'data' => [
                'primary' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'city' => $user->city,
                    'profession' => $user->profession,
                    'bio' => $user->bio,
                    'weight' => $user->weight,
                    'height' => $user->height,
                    'age' => $user->age,
                ],
                'details' => $this->userInterestService->getUserSelectedOptionForEachGroup($user),
            ]
        ]);
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
}
