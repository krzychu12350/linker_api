<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'role',
        'is_banned',
        'city',
        'profession',
        'bio',
        'weight',
        'height',
        'age'
    ];

    protected $casts = [
//        'interests' => 'array',
//        'preferences' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Define the relationship to the images table (Many-to-Many)
    public function photos()
    {
        return $this->belongsToMany(File::class, 'file_user', 'user_id', 'file_id');
    }

    // Define the relationship with Detail model via the detail_user pivot table
    public function details()
    {
        return $this->belongsToMany(Detail::class, 'detail_user', 'user_id', 'detail_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user', 'user_id', 'conversation_id');
    }

//    public function swipeMatches(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
//    {
//        return $this->hasManyThrough(SwipeMatch::class, Swipe::class, 'user_id', 'swipe_id_1', 'id', 'id');
//    }

    public function swipeMatches()
    {
        $userId = auth()->id(); // Get the current authenticated user's ID

        // First attempt to get swipe matches where swipe_id_1 is the current user
        $swipes = $this->hasMany(SwipeMatch::class, 'swipe_id_1', 'id')
            ->where('swipe_id_1', $userId)
            ->with(['conversation.users' => function ($query) use ($userId) {
                // Fetch users in the conversation, but exclude the authenticated user
                $query->where('users.id', '!=', $userId);
            }]);

        // If no results found, switch to swipe_id_2 as foreign key
        if ($swipes->get()->isEmpty()) {
            $swipes = $this->hasMany(SwipeMatch::class, 'swipe_id_2', 'id')
                ->where('swipe_id_2', $userId)
                ->with(['conversation.users' => function ($query) use ($userId) {
                    // Fetch users in the conversation, but exclude the authenticated user
                    $query->where('users.id', '!=', $userId);
                }]);
        }

        return $swipes;
    }



    /**
     * Get all conversations that the user is part of.
     */
//    public function conversations()
//    {
//        return $this->belongsToMany(Conversation::class, 'conversation_user')
//            ->withTimestamps();
//    }

    /**
     * Get all messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get all messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * The files that belong to the user.
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'file_user', 'user_id', 'file_id');
    }

    /**
     * Fetch all the details (groups, subgroups, and options) with eager loading.
     *
     * @return \Illuminate\Support\Collection
     */
    private function fetchDetails()
    {
        // Eager load subgroups and options in a single query to prevent N+1
        return Detail::with('children.children')  // Eager load children (subgroups) and children of children (options)
        ->whereNull('parent_id')  // Only get top-level groups (no parent)
        ->get();
    }

    /**
     * Map the details into a specific structure while optimizing selection checks.
     *
     * @param \Illuminate\Support\Collection $details
     * @param array $selectedDetailsIds
     * @param bool $includeSelection
     * @return array
     */
    private function mapDetails($details, array $selectedDetailsIds = [], bool $includeSelection = true): array
    {
        $selectedDetailsSet = collect($selectedDetailsIds); // Use a collection for faster contains lookup

        return $details->map(function ($group) use ($selectedDetailsSet, $includeSelection) {
            $subGroups = $group->children->map(function ($subGroup) use ($group, $selectedDetailsSet, $includeSelection) {
                // Check if this subgroup has its own subgroups (options)
                $subGroupOptions = $subGroup->children->map(function ($option) use ($selectedDetailsSet, $includeSelection) {
                    return [
                        'id' => $option->id,
                        'name' => $option->name,
                        'is_selected' => $includeSelection ? $selectedDetailsSet->contains($option->id) : null,
                    ];
                });

                // If there are no subgroups (options) for this subgroup, return main group options
                if ($subGroupOptions->isEmpty()) {
                    $subGroupOptions = collect([[
                        'id' => $group->id,
                        'name' => $group->name,
                        'is_selected' => $includeSelection ? $selectedDetailsSet->contains($group->id) : null,
                    ]]);
                }

                return [
                    'id' => $subGroup->id,
                    'name' => $subGroup->name,
                    'options' => $subGroupOptions->toArray(),
                ];
            });

            // For groups like "Gender", return options without subgroups
            if ($group->children->isNotEmpty() && $group->children->first()->children->isEmpty()) {
                return [
                    'id' => $group->id,
                    'group' => $group->name,
                    'options' => $group->children->map(function ($option) use ($selectedDetailsSet, $includeSelection) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'is_selected' => $includeSelection ? $selectedDetailsSet->contains($option->id) : null,
                        ];
                    })->toArray(),
                ];
            }

            return [
                'id' => $group->id,
                'group' => $group->name,
                'subGroups' => $subGroups->isEmpty() ? null : $subGroups->toArray(),
            ];
        })->toArray();
    }

    /**
     * Fetch all selected details and map them.
     *
     * @return array
     */
    public function allSelectedDetails(): array
    {
        // Fetch selected details for the user efficiently
        $selectedDetailsIds = $this->details()->pluck('id')->toArray(); // Use pluck for faster retrieval of IDs

        // Fetch the details and map them
        $details = $this->fetchDetails();
        return $this->mapDetails($details, $selectedDetailsIds);
    }
}
