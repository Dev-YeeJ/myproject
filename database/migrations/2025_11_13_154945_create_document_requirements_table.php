<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained()->onDelete('cascade');
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Stored path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requirements');
    }
};
