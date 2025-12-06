<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('Announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('image_path')->nullable();
            
            // Audience column specifically for your target groups
            $table->enum('audience', [
                'All', 
                'Residents', 
                'Barangay Officials', 
                'SK Officials'
            ])->default('All');

            $table->boolean('is_published')->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Author
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('Announcements');
    }
};