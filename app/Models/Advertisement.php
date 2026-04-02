<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_slot_id', 'title', 'content', 'image_url', 'link_url',
        'start_at', 'end_at', 'is_active',
        'impression_count', 'click_count',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // 关联广告位
    public function adSlot()
    {
        return $this->belongsTo(AdSlot::class);
    }

    // 作用域：有效广告
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_at')
                  ->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', now());
            });
    }

    // 记录曝光
    public function recordImpression()
    {
        $this->increment('impression_count');
    }

    // 记录点击
    public function recordClick()
    {
        $this->increment('click_count');
    }
}
