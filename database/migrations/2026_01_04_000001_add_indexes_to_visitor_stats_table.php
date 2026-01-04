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
        Schema::table('visitor_stats', function (Blueprint $table) {
            // Index for email lookups (unique visitors, top visitors)
            $table->index('email');
            
            // Index for date-based queries (visitors per day, date ranges)
            $table->index('visited_at');
            
            // Index for access method filtering
            $table->index('access_method');
            
            // Index for page visited queries (popular pages)
            $table->index('page_visited');
            
            // Composite index for email + visited_at (for user activity tracking)
            $table->index(['email', 'visited_at']);
            
            // Composite index for access_method + visited_at (for method trends)
            $table->index(['access_method', 'visited_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_stats', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['visited_at']);
            $table->dropIndex(['access_method']);
            $table->dropIndex(['page_visited']);
            $table->dropIndex(['email', 'visited_at']);
            $table->dropIndex(['access_method', 'visited_at']);
        });
    }
};
