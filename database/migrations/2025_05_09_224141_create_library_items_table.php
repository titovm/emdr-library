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
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['document', 'video']);
            $table->string('file_path')->nullable(); // For documents stored on S3
            $table->string('external_url')->nullable(); // For videos from YouTube/Vimeo
            $table->json('categories')->nullable(); // Store categories as JSON
            $table->json('tags')->nullable(); // Store tags as JSON
            $table->boolean('is_published')->default(true);
            $table->foreignId('added_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_items');
    }
};
