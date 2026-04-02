<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'marquee_text',
        'body',
        'is_active',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Announcement $a): void {
            if ($a->slug === null || $a->slug === '') {
                $a->slug = 'a-' . Str::lower(Str::random(12));
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function getMarqueeLineAttribute(): string
    {
        $t = trim((string) ($this->marquee_text ?: $this->title));

        return $t !== '' ? $t : '公告';
    }
}
