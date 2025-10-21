<?php
// app/Models/Household.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_number',
        'address',
        'purok',
        'total_members',
        'status',
    ];

    /**
     * Get all residents in this household
     */
    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * Get the household head
     */
    public function head()
    {
        return $this->hasOne(Resident::class)->where('household_status', 'Household Head');
    }
}