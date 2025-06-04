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
        Schema::create('library_item_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_item_id')->constrained('library_items')->onDelete('cascade');
            $table->enum('type', ['document', 'video']);
            $table->string('name'); // Display name for the file/video
            $table->string('file_path')->nullable(); // For documents stored on S3
            $table->string('external_url')->nullable(); // For videos from YouTube/Vimeo
            $table->string('original_filename')->nullable(); // Original filename for documents
            $table->string('mime_type')->nullable(); // MIME type for documents
            $table->integer('file_size')->nullable(); // File size in bytes
            $table->integer('sort_order')->default(0); // For ordering files within an item
            $table->timestamps();
            
            // Index for better performance
            $table->index(['library_item_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_item_files');
    }
};
