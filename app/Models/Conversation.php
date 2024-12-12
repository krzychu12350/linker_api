<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // 'user' or 'group'
        'name',
        'description',
        'match_id',
    ];

    /**
     * Get all users that belong to the conversation.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withTimestamps();
    }

    /**
     * Get all messages in the conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the swipe match if it's a one-on-one conversation.
     */
    public function swipeMatch()
    {
        return $this->belongsTo(SwipeMatch::class, 'match_id');
    }
}
