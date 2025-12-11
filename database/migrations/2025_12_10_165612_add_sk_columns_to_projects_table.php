<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Only add 'category' if it doesn't exist
            if (!Schema::hasColumn('projects', 'category')) {
                $table->string('category')->default('Barangay Project')->after('description');
            }
            
            // Only add 'budget' if it doesn't exist
            if (!Schema::hasColumn('projects', 'budget')) {
                $table->decimal('budget', 15, 2)->default(0)->after('category');
            }

            // Only add 'amount_spent' if it doesn't exist
            if (!Schema::hasColumn('projects', 'amount_spent')) {
                $table->decimal('amount_spent', 15, 2)->default(0)->after('budget');
            }
            
            // Only add 'progress' if it doesn't exist
            if (!Schema::hasColumn('projects', 'progress')) {
                $table->integer('progress')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // We generally don't drop these in rollback to avoid losing Barangay data,
            // but if you must, you can list dropColumn here.
        });
    }
};