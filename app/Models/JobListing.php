<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company',
        'salary',
        'city',
        'experience',
        'education',
        'description',
        'source_url',
        'tags',
        'is_full_time',
        'view_count',
        'published_at',
        'is_sent',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_full_time' => 'boolean',
        'published_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * 作用域：未发送的职位
     */
    public function scopeNotSent($query)
    {
        return $query->where('is_sent', false);
    }

    /**
     * 作用域：今日职位
     */
    public function scopeToday($query)
    {
        return $query->whereDate('published_at', today());
    }
}
