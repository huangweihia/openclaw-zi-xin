<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'position', 'size', 'is_active', 'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 关联广告
    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }

    // 获取当前有效广告
    public function getActiveAdvertisement()
    {
        return $this->advertisements()
            ->active()
            ->orderByDesc('id')
            ->first();
    }

    // 作用域：启用的广告位
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort');
    }
}
