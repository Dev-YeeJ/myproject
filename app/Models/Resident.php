<?php
// app/Models/Resident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // <-- Added this line

class Resident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'date_of_birth',
        'age', // This should ideally be calculated, but storing it simplifies queries
        'gender',
        'civil_status',
        'household_id', // Foreign key linking to households table
        'household_status', // Role within the household (Head, Spouse, Child, Member)
        'address', // Resident's specific address (might be same as household)
        'contact_number',
        'email',
        'occupation',
        'monthly_income',
        'is_registered_voter',
        'is_indigenous',
        'is_pwd', // Person with Disability
        'is_senior_citizen', // Calculated based on age
        'is_4ps', // Pantawid Pamilyang Pilipino Program beneficiary
        'is_active', // For soft deletes
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
        if ($this->middle_name) {
            $nameParts[] = $this->middle_name;
        }
        $nameParts[] = $this->last_name;
        if ($this->suffix) {
            $nameParts[] = $this->suffix;
        }
        return implode(' ', $nameParts);
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

    /**
     * Scope a query to only include 4Ps beneficiaries.
     */
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
        // Using the stored age column for simplicity
        return $query->where('age', '<', 18);
        // Alternatively, calculate dynamically (potentially slower for large datasets):
        // return $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18');
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
                  ->orWhere('email', 'like', $searchTerm);
            });
        }
        return $query; // No filter if search is empty
    }
}

