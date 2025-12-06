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
    // Stores the form setup (e.g., "Ask for Business Name")
    Schema::table('document_types', function (Blueprint $table) {
        $table->json('custom_fields')->nullable()->after('description'); 
    });

    // Stores the user's answers (e.g., "My Store Name")
    Schema::table('document_requests', function (Blueprint $table) {
        $table->json('custom_data')->nullable()->after('purpose');
    });
}

public function down()
{
    Schema::table('document_types', function (Blueprint $table) { $table->dropColumn('custom_fields'); });
    Schema::table('document_requests', function (Blueprint $table) { $table->dropColumn('custom_data'); });
}
};
