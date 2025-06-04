<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('library_items', function (Blueprint $table) {
            // Remove the type-specific columns since we'll store files separately
            $table->dropColumn(['type', 'file_path', 'external_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('library_items', function (Blueprint $table) {
            // Add back the columns if we need to rollback
            $table->enum('type', ['document', 'video'])->after('description');
            $table->string('file_path')->nullable()->after('type');
            $table->string('external_url')->nullable()->after('file_path');
        });
    }
};
