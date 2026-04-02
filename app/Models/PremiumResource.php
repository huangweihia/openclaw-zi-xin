<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'summary',
        'type', 'content',
        'download_link', 'extract_code', 'original_price',
        'tags', 'visibility',
        'download_count', 'view_count', 'like_count', 'favorite_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'download_count' => 'integer',
    ];

    // 关联评论
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // 作用域：可见
    public function scopeVisible($query, $user = null)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public');
            
            if ($user && $user->isVip()) {
                $q->orWhere('visibility', 'vip');
            }
        });
    }
}
