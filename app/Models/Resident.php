<?php
// app/Models/Resident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- ADDED
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 
use App\Models\Household; // <-- ADDED
use App\Models\User; // <-- ADDED

class Resident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 
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
        'precinct_number', 
        'is_indigenous',
        'is_pwd', 
        'pwd_id_number', 
        'disability_type', 
        'is_senior_citizen', 
        'is_4ps', 
        'is_active', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'monthly_income' => 'decimal:2',
        'is_registered_voter' => 'boolean',
        'is_indigenous' => 'boolean',
        'is_pwd' => 'boolean',
        'is_senior_citizen' => 'boolean',
        'is_4ps' => 'boolean',
        'is_active' => 'boolean',
        'age' => 'integer',
    ];

    /**
     * Get the household that this resident belongs to.
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the full name of the resident.
     * Accessor: $resident->full_name
     */
    public function getFullNameAttribute()
    {
        $nameParts = [$this->first_name];
        if (!empty($this->middle_name)) { // Use !empty to check for null or empty string
            $nameParts[] = $this->middle_name;
        }
        $nameParts[] = $this->last_name;
        if (!empty($this->suffix)) {
            $nameParts[] = $this->suffix;
        }
        return implode(' ', $nameParts);
    }

    /**
     * Get the user account associated with this resident.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active residents.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include senior citizens.
     */
    public function scopeSeniorCitizens($query)
    {
        return $query->where('is_senior_citizen', true);
    }

    public function scope4PsBeneficiaries($query)
    {
        return $query->where('is_4ps', true);
    }

    /**
     * Scope a query to only include Persons with Disability.
     */
    public function scopePwd($query)
    {
        return $query->where('is_pwd', true);
    }

    /**
     * Scope a query to only include indigenous persons.
     */
    public function scopeIndigenous($query)
    {
        return $query->where('is_indigenous', true);
    }

    /**
     * Scope a query to only include minors (under 18).
     */
    public function scopeMinors($query)
    {
        return $query->where('age', '<', 18);
    }

    /**
     * Scope a query to only include household heads.
     */
    public function scopeHouseholdHeads($query)
    {
        return $query->where('household_status', 'Household Head');
    }

    /**
     * Scope a query to filter by gender.
     */
    public function scopeByGender($query, $gender)
    {
        if ($gender && $gender !== 'All') {
            return $query->where('gender', $gender);
        }
        return $query; // No filter if 'All' or empty
    }

    /**
     * Scope a query to filter by household status (role in household).
     */
    public function scopeByHouseholdStatus($query, $status)
    {
        if ($status && $status !== 'All Status') {
            return $query->where('household_status', $status);
        }
        return $query; // No filter if 'All Status' or empty
    }

    /**
     * Scope a query to search residents by name, contact, or email.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $searchTerm = "%{$search}%";
                // Search concatenated full name
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $searchTerm)
                    ->orWhere('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('contact_number', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    // ADDED: Search by new ID numbers
                    ->orWhere('precinct_number', 'like', $searchTerm)
                    ->orWhere('pwd_id_number', 'like', $searchTerm);
            });
        }
        return $query; // No filter if search is empty
    }
}