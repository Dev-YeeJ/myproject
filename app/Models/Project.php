<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // This property allows the data to be saved. Without it, the save button does nothing.
    protected $fillable = [
        'title', 
        'category', 
        'status', 
        'budget', 
        'amount_spent', 
        'progress', 
        'start_date', 
        'end_date', 
        'description'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}