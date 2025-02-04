<?php

namespace App\Http\Controllers\User\Preference;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserPreferencesRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PreferenceController extends Controller
{
    /**
     * Get the user's stored preferences (data and detail preferences).
     *
     * @param User $user
     * @return JsonResponse
     */
    public function index(User $user): JsonResponse
    {
        // Fetch the user preference data (age range, height)
        $preferenceData = $user->preferenceData;

        // Fetch the user detail preferences (associated details)
        $detailPreferences = $user->detailPreferences;

        // Return the stored preferences as a JSON response
        return response()->json([
            'data' => [
                'preference_data' => $preferenceData, // Contains age range, height, etc.
                'detail_preferences' => $detailPreferences, // Contains associated details
            ]
        ], 200);
    }

    /**
     * Store user preferences (both data and detail preferences) via API.
     *
     * @param StoreUserPreferencesRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(StoreUserPreferencesRequest $request, User $user): JsonResponse
    {
        // Validate the incoming data using StoreUserPreferencesRequest
        $validated = $request->validated();

        // Handle user preference data (age, height, etc.)
        $userPreferenceData = $user->preferenceData()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'age_range_start' => $validated['age_range_start'],
                'age_range_end' => $validated['age_range_end'],
                'height' => $validated['height'],
            ]
        );

        // Handle user detail preferences (the associated details)
        // Attach the selected detail preferences
        $user->detailPreferences()->sync($validated['details']);

        // Return a success response with updated preferences
        return response()->json([
            'message' => 'Preferences updated successfully.',
            'data' => [
                'age_range_start' => $validated['age_range_start'],
                'age_range_end' => $validated['age_range_end'],
                'height' => $validated['height'],
                'details' => $validated['details'],
            ]
        ], 200);
    }
}
