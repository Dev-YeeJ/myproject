<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blotter_records', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique(); // e.g., BLT-2025-001
            $table->dateTime('date_reported');
            $table->string('incident_type'); // e.g., Noise Complaint, Theft
            $table->string('complainant');
            $table->string('respondent')->nullable(); // The person being complained about
            $table->string('location');
            $table->enum('priority', ['Low', 'Medium', 'High']);
            $table->enum('status', ['Open', 'Under Investigation', 'For Mediation', 'Resolved', 'Closed']);
            $table->text('narrative')->nullable(); // Details of the incident
            $table->text('actions_taken')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blotter_records');
    }
};