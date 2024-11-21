<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Define many-to-many relationship with User
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user');
    }
}
