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
        'document_type', // Foreign key column
        'tracking_number',
        'purpose',
        'price',
        'priority',
        'payment_status',
        'status',
        'payment_method',
        'payment_reference_number',
        'payment_proof',
        'custom_data', // Added: Stores the user's answers to dynamic fields
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'custom_data' => 'array', // Automatically cast JSON to array
    ];

    /**
     * Get the resident who requested the document.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function requirements()
    {
        return $this->hasMany(DocumentRequirement::class);
    }

    /**
     * Get the document type that this request is for.
     */
    public function documentType(): BelongsTo
    {
        // This links the 'document_type' (foreign key) on this model
        // to the 'id' (primary key) on the DocumentType model.
        return $this->belongsTo(DocumentType::class, 'document_type', 'id');
    }
}