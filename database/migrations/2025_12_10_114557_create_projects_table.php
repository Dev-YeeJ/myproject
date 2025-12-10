<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category')->default('SK Project'); // Distinguish from Barangay Projects
            $table->decimal('budget', 15, 2)->default(0);      // Allocated Budget
            $table->decimal('amount_spent', 15, 2)->default(0); // Actual Expenses
            $table->date('start_date');
            $table->date('end_date')->nullable();
            // Status: Planning, In Progress, Completed, Cancelled
            $table->string('status')->default('Planning'); 
            $table->integer('progress')->default(0); // 0-100%
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
