<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SkOfficial extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'position',
        'committee',
        'term_start',
        'term_end',
        'is_active',
    ];

    /**
     * 1. Casts
     * Automatically converts database columns to PHP types.
     * 'date' converts MySQL dates to Carbon instances (so ->format() works).
     * 'boolean' ensures is_active is always true/false.
     */
    protected $casts = [
        'term_start' => 'date',
        'term_end'   => 'date',
        'is_active'  => 'boolean',
    ];

    /**
     * 2. Relationships
     * Links the official to their Resident profile.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * 3. Local Scope: Active
     * Allows you to write SkOfficial::active()->get() in your controller
     * instead of SkOfficial::where('is_active', true)->get().
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 4. Helper: Check if Term is Valid
     * Returns true if today is between term start and end dates.
     * Usage: $official->in_term
     */
    public function getInTermAttribute()
    {
        return Carbon::now()->between($this->term_start, $this->term_end);
    }
}