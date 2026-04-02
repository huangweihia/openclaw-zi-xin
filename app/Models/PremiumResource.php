<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'source',
        'description',
        'content',
        'files',
        'tags',
        'original_price',
        'is_summarized',
        'curator_note',
        'quality_score',
        'is_vip_only',
        'download_count',
        'view_count',
    ];

    protected $casts = [
        'is_summarized' => 'boolean',
        'is_vip_only' => 'boolean',
        'files' => 'array',
        'tags' => 'array',
        'quality_score' => 'integer',
        'download_count' => 'integer',
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

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeVipOnly($query)
    {
        return $query->where('is_vip_only', true);
    }

    public function scopeHighQuality($query, int $minScore = 8)
    {
        return $query->where('quality_score', '>=', $minScore);
    }

    /**
     * 获取类型标签
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'course_notes' => '课程笔记',
            'report' => '行业报告',
            'template' => '模板工具',
            'toolkit' => '工具包',
            'ebook' => '电子书',
            default => '其他',
        };
    }

    /**
     * 获取质量星级
     */
    public function getQualityStarsAttribute(): string
    {
        $fullStars = floor($this->quality_score / 2);
        $halfStar = ($this->quality_score % 2 === 1) ? '⭐' : '';
        return str_repeat('⭐', $fullStars) . $halfStar . str_repeat('☆', 5 - $fullStars - ($halfStar ? 1 : 0));
    }
}
