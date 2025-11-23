<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image_path',
        'is_published',
        'user_id',
    ];

    /**
     * Get the author of the announcement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to only show published announcements (for Residents).
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}