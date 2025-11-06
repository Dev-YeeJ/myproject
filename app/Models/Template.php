<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Template extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'file_path', 'document_type_id'];

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}