<?php
// app/Models/Household.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'household_name', // A descriptive name for the household (e.g., "Dela Cruz Family")
        'household_number', // A unique identifier (e.g., "HH-001")
        'address', // Specific address like Block/Lot/Street
        'purok', // Zone or Purok designation
        'total_members', // Cached count of active members
        'status', // e.g., 'complete', 'incomplete' - indicates data completeness
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_members' => 'integer',
        // No date casts needed based on fillable fields
    ];

    /**
     * Get all residents belonging to this household (including inactive ones if needed).
     */
    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * Get only the active residents belonging to this household.
     */
    public function activeResidents()
    {
        return $this->hasMany(Resident::class)->where('is_active', true);
    }

    /**
     * Get the resident designated as the head of this household.
     */
    public function head()
    {
        // Assumes only one active head per household
        return $this->hasOne(Resident::class)
                    ->where('household_status', 'Household Head')
                    ->where('is_active', true); // Ensure the head is active
    }

    /**
     * Scope a query to only include households with 'complete' status.
     */
    public function scopeComplete($query)
    {
        return $query->where('status', 'complete');
    }

    /**
     * Scope a query to only include households with 'incomplete' status.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('status', 'incomplete');
    }

     /**
     * Recalculate and update the total_members count based on active residents.
     * Useful to call after adding/removing/deactivating residents.
     */
    public function updateTotalMembers()
    {
        $this->total_members = $this->activeResidents()->count();
        $this->save();
    }
}
