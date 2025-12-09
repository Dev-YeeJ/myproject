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
    Schema::table('document_requests', function (Blueprint $table) {
        // This adds the missing column needed for the financial breakdown
        $table->foreignId('document_type_id')->nullable()->constrained('document_types');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            //
        });
    }
};
