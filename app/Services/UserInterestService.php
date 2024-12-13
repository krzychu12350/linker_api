<?php

namespace App\Services;

use App\Models\Detail;
use App\Models\User;

class UserInterestService
{
    private function fetchDetails(): \Illuminate\Support\Collection
    {
        // Fetch all the details (groups, subgroups, and options) with their children in a single query
        return Detail::with('children.children') // Eager load subgroups and options
        ->whereNull('parent_id') // Only get top-level groups (no parent)
        ->get();
    }

    private function mapDetails($details, array $selectedDetailsIds = [], bool $includeSelection = true): array
    {
        $selectedDetailsSet = collect($selectedDetailsIds); // Create a set for faster lookup

        return $details->map(function ($group) use ($selectedDetailsSet, $includeSelection) {
            // Fetch the subgroups (child groups) for this group
            $subGroups = $group->children->map(function ($subGroup) use ($group, $selectedDetailsSet, $includeSelection) {
                // Check if this subgroup has its own subgroups (options)
                $subGroupOptions = $subGroup->children->map(function ($option) use ($selectedDetailsSet, $includeSelection) {
                    $optionData = [
                        'id' => $option->id, // Option ID
                        'name' => $option->name, // Option name
                    ];
                    if ($includeSelection) {
                        $optionData['is_selected'] = $selectedDetailsSet->contains($option->id); // Use contains for faster lookup
                    }
                    return $optionData;
                });

                // If there are no subgroups (options) for this subgroup, return main group options
                if ($subGroupOptions->isEmpty()) {
                    $subGroupOptions = collect([[
                        'id' => $group->id, // Return the main group ID as an option
                        'name' => $group->name, // Main group name as option
                        'is_selected' => $includeSelection ? $selectedDetailsSet->contains($group->id) : null, // Add is_selected if required
                    ]]);
                }

                return [
                    'id' => $subGroup->id, // Add subgroup ID
                    'name' => $subGroup->name,
                    'options' => $subGroupOptions->toArray(),
                ];
            });

            // For groups like "Gender", only return options without subgroups
            if ($group->children->isNotEmpty() && $group->children->first()->children->isEmpty()) {
                // Return the group with its options (without subgroups)
                $options = $group->children->map(function ($option) use ($selectedDetailsSet, $includeSelection) {
                    $optionData = [
                        'id' => $option->id, // Option ID
                        'name' => $option->name, // Option name
                    ];
                    if ($includeSelection) {
                        $optionData['is_selected'] = $selectedDetailsSet->contains($option->id); // Check if selected
                    }
                    return $optionData;
                });

                return [
                    'id' => $group->id, // Add group ID
                    'group' => $group->name,
                    'options' => $options->toArray(), // Return as an array of options
                ];
            }

            // If there are subgroups, return the full structure with subgroups
            return [
                'id' => $group->id, // Add group ID
                'group' => $group->name,
                'subGroups' => $subGroups->isEmpty() ? null : $subGroups->toArray(), // Return subgroups if they exist
            ];
        })->toArray();
    }

    public function getAllUserDetails(): array
    {
        $details = $this->fetchDetails();
        return $this->mapDetails($details, [], false);
    }

    public function getAllUserDetailsWithSelection(User $user): array
    {
        $userDetails = $user
            ->details()
            ->get();

//        if($userDetails->isEmpty()) {
//            return [];
//        }

        $selectedDetailsIds = $userDetails
            ->pluck('id')
            ->toArray(); // Use pluck() to fetch only IDs

        return $this->mapDetails($this->fetchDetails(), $selectedDetailsIds);
    }

    /**
     * Update the user details with the provided group and sub-group IDs.
     *
     * @param array $validatedData
     * @param int $userId
     * @return bool
     */
    public function updateUserDetails(array $validatedData, int $userId): bool
    {
        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        $detailsToUpdate = [];

        // Collect all the sub-group details first for better performance
        $subGroups = Detail::whereIn('id', array_column($validatedData['details'], 'sub_group_id'))
            ->get()->keyBy('id');

        $groupDetails = Detail::whereIn('id', array_column($validatedData['details'], 'group_id'))
            ->get()->keyBy('id');

        // Loop through the provided details
        foreach ($validatedData['details'] as $detailData) {
            $subGroupDetail = $detailData['sub_group_id'] ?? null;

            // Check for valid sub-group detail if exists
            if ($subGroupDetail && isset($subGroups[$subGroupDetail])) {
                $groupDetail = $groupDetails[$detailData['group_id']] ?? null;

                if ($groupDetail && $subGroups[$subGroupDetail]->parent_id == $groupDetail->id) {
                    $detailsToUpdate[] = $detailData['options'];
                }
            } else {
                $detailsToUpdate[] = $detailData['options'];
            }
        }

        // Flatten and merge the options
        $mergedDetailIds = collect($detailsToUpdate)->flatten()->toArray();

        // Sync the user with the selected details
        $user->details()->sync($mergedDetailIds);

        return true;
    }
}
