<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'full_name',
        'description',
        'url',
        'language',
        'stars',
        'forks',
        'score',
        'tags',
        'monetization',
        'difficulty',
        'revenue',
        'is_featured',
        'is_vip',
        'collected_at',
        'income_range',
        'time_commitment',
        'monetization_paths',
        'tech_stack',
        'resources',
    ];

    protected $casts = [
        'stars' => 'integer',
        'forks' => 'integer',
        'score' => 'decimal:2',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_vip' => 'boolean',
        'collected_at' => 'datetime',
        'monetization_paths' => 'array',
        'tech_stack' => 'array',
        'resources' => 'array',
    ];

    /**
     * 分类关联
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
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
     * 是否可查看 VIP 项目完整详情（变现分析、技术栈、外链等）
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

        return $user->isVip();
    }

    /**
     * 获取难度标签
     */
    public function getDifficultyLabel(): string
    {
        $difficulty = $this->difficulty ?? 3;
        return [
            1 => '⭐ 简单',
            2 => '⭐⭐ 较易',
            3 => '⭐⭐⭐ 中等',
            4 => '⭐⭐⭐⭐ 较难',
            5 => '⭐⭐⭐⭐⭐ 困难',
        ][$difficulty] ?? '⭐⭐⭐ 中等';
    }

    /**
     * 供 Blade / API 使用：`$project->difficulty_label`
     */
    public function getDifficultyLabelAttribute(): string
    {
        return $this->getDifficultyLabel();
    }

    /**
     * 获取收入范围标签
     */
    public function getIncomeLabel(): string
    {
        if (!$this->income_range) return '面议';
        return '¥' . $this->income_range . '/月';
    }

    /**
     * 获取时间投入标签
     */
    public function getTimeLabel(): string
    {
        return $this->time_commitment ?? '灵活';
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query, $limit = 20)
    {
        return $query->orderBy('stars', 'desc')->limit($limit);
    }

    public function scopeLatest($query, $limit = 20)
    {
        return $query->orderBy('collected_at', 'desc')->limit($limit);
    }

    /**
     * 计算项目评分
     */
    public static function calculateScore($stars, $growth = 0, $monetizationPotential = 'medium')
    {
        $starScore = min($stars / 10000, 10);
        $growthScore = min($growth, 10);
        $monetizationScores = ['low' => 3, 'medium' => 6, 'high' => 10];
        $monetizationScore = $monetizationScores[$monetizationPotential] ?? 6;

        return round($starScore * 0.3 + $growthScore * 0.3 + $monetizationScore * 0.4, 2);
    }

}
