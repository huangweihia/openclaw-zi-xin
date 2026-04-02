<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SideHustleCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'summary',
        'content',
        'category',
        'difficulty',
        'time_commitment',
        'startup_cost',
        'revenue_model',
        'estimated_monthly_income',
        'actual_income',
        'income_screenshots',
        'steps',
        'tools_needed',
        'common_pitfalls',
        'is_verified',
        'is_vip_only',
        'view_count',
        'like_count',
        'save_count',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_vip_only' => 'boolean',
        'income_screenshots' => 'array',
        'steps' => 'array',
        'tools_needed' => 'array',
        'common_pitfalls' => 'array',
        'estimated_monthly_income' => 'integer',
        'actual_income' => 'integer',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'save_count' => 'integer',
    ];

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes()
    {
        return $this->morphMany(UserLike::class, 'likeable');
    }

    public function saves()
    {
        return $this->morphMany(UserSave::class, 'savable');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeVipOnly($query)
    {
        return $query->where('is_vip_only', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * 获取难度标签
     */
    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty) {
            'easy' => '入门级',
            'medium' => '进阶级',
            'hard' => '专家级',
            default => '未知',
        };
    }

    /**
     * 获取分类标签
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'online' => '线上副业',
            'offline' => '线下副业',
            'hybrid' => '线上线下结合',
            default => '其他',
        };
    }
}
