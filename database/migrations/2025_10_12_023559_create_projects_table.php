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
            $table->text('description')->nullable();
            $table->string('category'); // Infrastructure, Community, Environmental
            $table->string('status')->default('Planning'); // Planning, In Progress, Completed, On Hold
            $table->integer('progress')->default(0); // 0 to 100
            $table->decimal('budget', 15, 2)->default(0);
            $table->decimal('amount_spent', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};