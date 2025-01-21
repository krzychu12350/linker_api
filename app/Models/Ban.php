<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'reason', 'banned_until'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if a user is currently banned
    public function isCurrentlyBanned()
    {
        return $this->banned_until ? $this->banned_until > now() : false;
    }
}

