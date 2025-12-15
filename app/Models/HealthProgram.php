<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthProgram extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'schedule_date',
        'organizer',
        'status',
    ];

    /**
     * The attributes that should be cast.
     * This converts schedule_date to a Carbon instance automatically.
     */
    protected $casts = [
        'schedule_date' => 'datetime',
    ];
}