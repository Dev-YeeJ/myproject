<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcements extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'audience',
        'is_published',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper to get image URL safely
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /**
     * Scope to filter announcements based on the authenticated user's role methods.
     * Usage: Announcement::forUser(Auth::user())->get();
     */
    public function scopeForUser($query, $user)
    {
        // If no user is logged in, only show 'All' public announcements
        if (!$user) {
            return $query->where('audience', 'All')->where('is_published', true);
        }

        // 1. Captains see everything (Drafts and Published, All Audiences)
        if ($user->isBarangayCaptain()) {
            return $query; 
        }

        // 2. Base query for everyone else: Must be published
        $query->where('is_published', true);

        return $query->where(function ($q) use ($user) {
            // Everyone sees 'All'
            $q->where('audience', 'All');

            // Logic for 'Residents' audience
            if ($user->isResident()) {
                $q->orWhere('audience', 'Residents');
            }

            // Logic for 'SK Officials' audience
            if ($user->isSkofficial()) {
                $q->orWhere('audience', 'SK Officials');
            }

            // Logic for 'Barangay Officials' audience
            if ($user->isSecretary() || $user->isTreasurer() || $user->isKagawad()) {
                $q->orWhere('audience', 'Barangay Officials');
            }
        });
    }
}