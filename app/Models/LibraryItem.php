<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class LibraryItem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'file_path',
        'external_url',
        'categories',
        'tags',
        'is_published',
        'added_by',
    ];

    protected $casts = [
        'categories' => 'array',
        'tags' => 'array',
        'is_published' => 'boolean',
    ];

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
     */
    public function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->type === 'document') {
                    return route('library.download', $this->id);
                }
                
                return $this->external_url;
            }
        );
    }
}
