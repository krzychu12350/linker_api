<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\PollResponse;

class EventPoll extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'response'
    ];

    protected $casts = [
        'response' => PollResponse::class,
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}