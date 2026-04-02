<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiToolMonetization extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_name',
        'tool_url',
        'tool_logo',
        'category',
        'description',
        'monetization_scenarios',
        'prompt_templates',
        'delivery_standards',
        'pricing_guide',
        'client_channels',
        'is_domestic',
        'pricing_model',
        'popularity_score',
        'is_vip_only',
        'view_count',
    ];

    protected $casts = [
        'is_domestic' => 'boolean',
        'is_vip_only' => 'boolean',
        'monetization_scenarios' => 'array',
        'prompt_templates' => 'array',
        'delivery_standards' => 'array',
        'pricing_guide' => 'array',
        'client_channels' => 'array',
        'popularity_score' => 'integer',
        'view_count' => 'integer',
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

    public function scopeDomestic($query)
    {
        return $query->where('is_domestic', true);
    }

    public function scopeInternational($query)
    {
        return $query->where('is_domestic', false);
    }

    public function scopeVipOnly($query)
    {
        return $query->where('is_vip_only', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopePopular($query, int $minScore = 70)
    {
        return $query->where('popularity_score', '>=', $minScore);
    }

    /**
     * 获取分类标签
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'image' => '图像生成',
            'text' => '文本写作',
            'video' => '视频制作',
            'audio' => '音频处理',
            'code' => '代码编程',
            default => '其他',
        };
    }

    /**
     * 获取定价模式标签
     */
    public function getPricingModelLabelAttribute(): string
    {
        return match($this->pricing_model) {
            'free' => '免费',
            'subscription' => '订阅制',
            'pay_per_use' => '按量付费',
            'freemium' => '免费 + 付费',
            default => '未知',
        };
    }
}
