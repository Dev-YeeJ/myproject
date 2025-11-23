<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_request_id',
        'file_name',
        'file_path',
    ];

    public function documentRequest()
    {
        return $this->belongsTo(DocumentRequest::class);
    }
}
