<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiToolMonetization extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_name', 'slug', 'tool_url',
        'category', 'available_in_china', 'pricing_model',
        'content',
        'monetization_scenes', 'prompt_templates', 'pricing_reference',
        'channels', 'delivery_standards',
        'visibility',
        'view_count', 'like_count', 'favorite_count',
    ];

    protected $casts = [
        'available_in_china' => 'boolean',
        'monetization_scenes' => 'array',
        'prompt_templates' => 'array',
        'pricing_reference' => 'array',
        'channels' => 'array',
        'delivery_standards' => 'array',
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
