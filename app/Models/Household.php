<?php
// app/Models/Household.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Household extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'household_name', 
        // 'household_number', // Removed from fillable, auto-generated
        'address', 
        'purok', 
        'total_members', 
        'status', // e.g., 'complete', 'incomplete'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_members' => 'integer',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        /**
         * Listen for the "creating" event to automatically generate a household_number.
         */
        static::creating(function ($household) {
            // Check if household_number is already set (e.g., during seeding)
            if (empty($household->household_number)) {
                $household->household_number = static::generateHouseholdNumber();
            }
        });
    }

    /**
     * Helper function to generate the next available household number.
     *
     * @return string
     */
    public static function generateHouseholdNumber()
    {
        // Get the latest household by 'created_at' to find the last number
        $latestHousehold = static::orderBy('created_at', 'desc')->first();

        $nextNumber = 1; // Default for the very first household

        if ($latestHousehold && $latestHousehold->household_number) {
            // Extract the numeric part of the household_number
            // e.g., "HH-001" -> "001"
            $lastNumberStr = Str::after($latestHousehold->household_number, 'HH-');
            
            // Convert to integer, increment
            $lastNumber = intval($lastNumberStr);
            $nextNumber = $lastNumber + 1;
        }

        // Format the number back to "HH-001" format (pads with leading zeros)
        return 'HH-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }


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

    /**
     * ==========================================================
     * NEW LOGIC: Update household status based on head
     * ==========================================================
     *
     * Checks if an active 'Household Head' exists and updates
     * the household's status to 'complete' or 'incomplete'.
     */
    public function updateHouseholdStatus()
    {
        // Check if there is at least one active resident with the status 'Household Head'
        $hasActiveHead = $this->activeResidents()
                              ->where('household_status', 'Household Head')
                              ->exists();
        
        $this->status = $hasActiveHead ? 'complete' : 'incomplete';
        $this->save();
    }
}