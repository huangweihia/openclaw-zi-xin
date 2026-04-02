<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'cover_image',
        'author_id',
        'view_count',
        'like_count',
        'favorite_count',
        'is_premium',
        'is_vip',
        'is_published',
        'published_at',
        'source_url',
        'meta_keywords',
        'meta_description',
    ];

    protected $casts = [
        'view_count' => 'integer',
        'like_count' => 'integer',
        'favorite_count' => 'integer',
        'is_premium' => 'boolean',
        'is_vip' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * 评论关联
     */
    public function comments()
    {
        return $this->morphMany(\App\Models\Comment::class, 'commentable');
    }

    /**
     * 收藏关联
     */
    public function favorites()
    {
        return $this->morphMany(\App\Models\Favorite::class, 'favoritable');
    }

    /**
     * 检查用户是否已收藏
     */
    public function isFavoritedBy($user): bool
    {
        if (!$user) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    /**
     * 检查用户是否已点赞
     */
    public function isLikedBy($user): bool
    {
        if (!$user) return false;
        // 简单实现，实际应该有点赞表
        return false;
    }

    /**
     * 是否可查看 VIP 全文（后端鉴权：非 VIP 或未解锁则不得输出正文）
     */
    public function userCanViewFullContent(?User $user): bool
    {
        if (!$this->is_vip) {
            return true;
        }
        if (!$user) {
            return false;
        }
        if ($user->isAdmin()) {
            return true;
        }
        if ($this->author_id && (int) $user->id === (int) $this->author_id) {
            return true;
        }
        if ($user->isVip()) {
            return true;
        }

        return UserAction::hasActioned($user->id, 'unlock', $this);
    }

    /**
     * 获取相关文章（同分类，排除自己）
     */
    public function getRelatedArticles($limit = 5)
    {
        return static::where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->where('is_published', true)
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 获取阅读进度（基于内容长度）
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // 假设每分钟读 200 字
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('published_at', '<=', now());
    }

    public function scopeFree($query)
    {
        return $query->where('is_premium', false);
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
