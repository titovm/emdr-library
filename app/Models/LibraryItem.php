<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LibraryItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'categories',
        'tags',
        'is_published',
        'added_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Get the categories attribute with proper Unicode decoding.
     */
    protected function categories(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true) ?: [],
            set: fn ($value) => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        );
    }

    /**
     * Get the tags attribute with proper Unicode decoding.
     */
    protected function tags(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, true) ?: [],
            set: fn ($value) => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        );
    }

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // When a library item is deleted, all associated files will be deleted automatically
        // due to the cascade delete in the foreign key constraint and the LibraryItemFile model's boot method
    }

    /**
     * Get the files associated with this library item.
     */
    public function files()
    {
        return $this->hasMany(LibraryItemFile::class)->ordered();
    }

    /**
     * Get only document files.
     */
    public function documents()
    {
        return $this->files()->where('type', 'document');
    }

    /**
     * Get only video files.
     */
    public function videos()
    {
        return $this->files()->where('type', 'video');
    }

    /**
     * Get the user who added this library item.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Scope a query to only include published items.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Filter by category.
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('categories', 'like', "%$category%");
    }

    /**
     * Filter by tag.
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->where('tags', 'like', "%$tag%");
    }

    /**
     * Get the URL for viewing the library item.
     * Since items can now have multiple files, this returns the show page URL.
     */
    public function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                return route('library.show', $this->id);
            }
        );
    }

    /**
     * Check if this item has any files.
     */
    public function hasFiles(): bool
    {
        return $this->files()->count() > 0;
    }

    /**
     * Get the total file count.
     */
    public function fileCount(): int
    {
        return $this->files()->count();
    }

    /**
     * Get the document count.
     */
    public function documentCount(): int
    {
        return $this->documents()->count();
    }

    /**
     * Get the video count.
     */
    public function videoCount(): int
    {
        return $this->videos()->count();
    }
}
