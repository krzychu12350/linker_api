<?php

namespace App\Models;

use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Define the fillable fields for mass assignment protection
    protected $fillable = [
        'description',
        'type',
//        'user_id'
    ];

    // Casting 'type' to the ReportType enum
    protected $casts = [
        'type' => ReportType::class,
    ];

    /**
     * Define the relationship between Report and User
     * A report belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
