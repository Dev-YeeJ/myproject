<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Import the SkOfficial model if it's in the same namespace context or reference it directly
use App\Models\SkOfficial; 
use App\Models\Resident;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role',
        'contact_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // ============================================
    // RELATIONSHIPS
    // ============================================

    /**
     * Get the resident profile associated with this user.
     */
    public function resident()
    {
        return $this->hasOne(Resident::class);
    }

    /**
     * Get the SK Official profile associated with this user.
     * This links User -> Resident -> SkOfficial
     */
    public function skOfficialProfile()
    {
        return $this->hasOneThrough(
            SkOfficial::class,
            Resident::class,
            'user_id',      // Foreign key on residents table
            'resident_id',  // Foreign key on sk_officials table
            'id',           // Local key on users table
            'id'            // Local key on residents table
        );
    }

    // ============================================
    // ROLE-CHECKING METHODS
    // ============================================

    public function isBarangayCaptain(): bool
    {
        return $this->role === 'barangay_captain';
    }

    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    public function isTreasurer(): bool
    {
        return $this->role === 'treasurer';
    }

    public function isKagawad(): bool
    {
        return $this->role === 'kagawad';
    }

    public function isHealthWorker(): bool
    {
        return $this->role === 'health_worker';
    }

    public function isTanod(): bool
    {
        return $this->role === 'tanod';
    }
    
    public function isSkofficial(): bool
    {
        // Checks if the role string is strictly 'sk_official'
        return $this->role === 'sk_official';
    }
    
    public function isResident(): bool
    {
        return $this->role === 'resident';
    }
}