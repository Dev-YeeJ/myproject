<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Fix Projects Table (Add missing columns if they don't exist)
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'status')) {
                $table->string('status')->default('Planning'); 
            }
            if (!Schema::hasColumn('projects', 'amount_spent')) {
                $table->decimal('amount_spent', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('projects', 'progress')) {
                $table->integer('progress')->default(0);
            }
        });

        // 2. Add Link to Financial Transactions
        Schema::table('financial_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_transactions', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'amount_spent', 'progress']);
        });
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};