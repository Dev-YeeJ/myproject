<?php
// app/Models/Resident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'age',
        'gender',
        'civil_status',
        'household_id',
        'household_status',
        'address',
        'contact_number',
        'email',
        'occupation',
        'monthly_income',
        'is_registered_voter',
        'is_indigenous',
        'is_pwd',
        'is_senior_citizen',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_registered_voter' => 'boolean',
        'is_indigenous' => 'boolean',
        'is_pwd' => 'boolean',
        'is_senior_citizen' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the household that the resident belongs to
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute()
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }
        return $name;
    }
}