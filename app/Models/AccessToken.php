<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccessToken extends Model
{
    protected $fillable = [
        'name',
        'email',
        'token',
        'expires_at',
        'last_used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Generate a new access token for the given name and email.
     */
    public static function generate(string $name, string $email, int $expiryDays = 365)
    {
        return self::create([
            'name' => $name,
            'email' => $email,
            'token' => Str::random(64),
            'expires_at' => now()->addDays($expiryDays),
        ]);
    }

    /**
     * Find a valid token by its string representation.
     */
    public static function findValidToken(string $token)
    {
        return self::where('token', $token)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
    }

    /**
     * Mark the token as used.
     */
    public function markAsUsed()
    {
        $this->update([
            'last_used_at' => now(),
        ]);

        return $this;
    }

    /**
     * Check if the token is valid.
     */
    public function isValid()
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
