<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorStat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'ip_address',
        'user_agent',
        'visited_at',
        'page_visited',
        'access_method',
        'has_consent'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visited_at' => 'datetime',
        'has_consent' => 'boolean',
    ];

    /**
     * Record a new visitor statistic asynchronously.
     *
     * @param array $data
     * @return void
     */
    public static function recordVisit(array $data): void
    {
        // Dispatch async job to avoid blocking the request
        \App\Jobs\RecordVisitorStatJob::dispatch($data);
    }

    /**
     * Record a new visitor statistic synchronously (for testing or specific cases).
     *
     * @param array $data
     * @return VisitorStat
     */
    public static function recordVisitSync(array $data): VisitorStat
    {
        return self::create(array_merge($data, [
            'visited_at' => now(),
        ]));
    }
}
