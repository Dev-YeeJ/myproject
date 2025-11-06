<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'resident_id',
        'tracking_number',
        'document_type',
        'purpose',
        'price',
        'priority',
        'payment_status',
        'status',
    ];

    /**
     * Get the resident who requested the document.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}