<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SwipeMatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'swipe_id_1',
        'swipe_id_2',
    ];

    /**
     * Get the user associated with swipe_id_1.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'swipe_id_1');
    }

}
