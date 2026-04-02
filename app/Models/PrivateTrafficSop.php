<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateTrafficSop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'platform',
        'type',
        'summary',
        'content',
        'checklist',
        'templates',
        'tools_recommended',
        'metrics',
        'case_studies',
        'difficulty_level',
        'estimated_time',
        'is_vip_only',
        'view_count',
        'save_count',
    ];

    protected $casts = [
        'is_vip_only' => 'boolean',
        'checklist' => 'array',
        'templates' => 'array',
        'tools_recommended' => 'array',
        'metrics' => 'array',
        'case_studies' => 'array',
        'difficulty_level' => 'integer',
        'estimated_time' => 'integer',
        'view_count' => 'integer',
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

    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeVipOnly($query)
    {
        return $query->where('is_vip_only', true);
    }

    /**
     * 获取平台标签
     */
    public function getPlatformLabelAttribute(): string
    {
        return match($this->platform) {
            'wechat' => '微信生态',
            'xiaohongshu' => '小红书',
            'douyin' => '抖音',
            'bilibili' => 'B 站',
            'zhihu' => '知乎',
            default => '其他',
        };
    }

    /**
     * 获取类型标签
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'growth' => '引流获客',
            'operation' => '日常运营',
            'conversion' => '转化成交',
            'retention' => '用户留存',
            default => '其他',
        };
    }

    /**
     * 获取难度星级
     */
    public function getDifficultyStarsAttribute(): string
    {
        return str_repeat('⭐', $this->difficulty_level) . str_repeat('☆', 5 - $this->difficulty_level);
    }
}
