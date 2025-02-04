<?php

namespace App\Services;

use App\Models\SwipeMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SwipeFilterService
{
    protected Builder $query;

    public function __construct()
    {
        // Initialize the query builder
        $this->query = User::query();
    }

    /**
     * Initialize the filter service with the base query.
     *
     * @param User $user
     * @return $this
     */
    public function initialize(User $user): self
    {
        $this->query->with(['photos', 'detailPreferences'])
            ->where('id', '!=', $user->id); // Exclude current user

        return $this;
    }

    /**
     * Filter users based on the user's age and height preference.
     *
     * @param User $user
     * @return $this
     */
    public function filterByPreferenceData(User $user): self
    {
        $userPreferenceData = $user->preferenceData;

        if ($userPreferenceData != null) {
            if ($userPreferenceData->age_range_start && $userPreferenceData->age_range_end) {
                $this->query->whereBetween('age', [
                    $userPreferenceData->age_range_start,
                    $userPreferenceData->age_range_end
                ]);
            }

            if ($userPreferenceData->height) {
                $this->query->where('height', '>=', $userPreferenceData->height);
            }
        }

        return $this;
    }

    /**
     * Filter users based on the current user's detail preferences.
     *
     * @param User $user
     * @return $this
     */
    public function filterByDetailPreferences(User $user): self
    {
        if($user->detailPreferences->isNotEmpty()) {
            $this->query->whereHas('detailPreferences', function ($query) use ($user) {
                $query->whereIn('detail_id', $user->detailPreferences->pluck('id'));
            });
        }

        return $this;
    }

    /**
     * Exclude users who have already been swiped or matched with the current user.
     *
     * @param User $user
     * @return $this
     */
    public function excludeMatchedUsers(User $user): self
    {
        $this->query->whereNotIn('id', function ($query) use ($user) {
            $query->select('swipe_id_2')
                ->from('swipe_matches')
                ->where('swipe_id_1', $user->id)
                ->union(
                    SwipeMatch::select('swipe_id_1')
                        ->where('swipe_id_2', $user->id)
                );
        });

        return $this;
    }

    /**
     * Get the final filtered users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query->get();
    }
}
