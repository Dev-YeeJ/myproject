<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
}