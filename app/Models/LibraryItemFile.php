<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LibraryItemFile extends Model
{
    protected $fillable = [
        'library_item_id',
        'type',
        'name',
        'file_path',
        'external_url',
        'original_filename',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the library item that owns this file.
     */
    public function libraryItem(): BelongsTo
    {
        return $this->belongsTo(LibraryItem::class);
    }

    /**
     * Get the file URL for display.
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->type === 'video' && $this->external_url) {
            return $this->external_url;
        }

        if ($this->type === 'document' && $this->file_path) {
            try {
                return Storage::disk('yandex')->url($this->file_path);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Get a human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope to order files by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    /**
     * Delete the file from storage when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if ($file->type === 'document' && $file->file_path) {
                try {
                    Storage::disk('yandex')->delete($file->file_path);
                } catch (\Exception $e) {
                    \Log::error('Failed to delete file from storage', [
                        'file_id' => $file->id,
                        'file_path' => $file->file_path,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }
}
