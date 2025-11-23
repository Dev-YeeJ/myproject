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
        'document_type',
        'tracking_number',
        // 'document_type', // <-- This was a duplicate, I removed it
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