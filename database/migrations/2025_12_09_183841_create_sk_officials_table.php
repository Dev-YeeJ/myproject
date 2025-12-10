<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sk_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade'); // Link to resident profile
            $table->string('position'); // SK Chairperson, Kagawad, Secretary, Treasurer
            $table->string('committee')->nullable(); // Committee on Sports, Education, etc.
            $table->date('term_start');
            $table->date('term_end');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sk_officials');
    }
};