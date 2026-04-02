<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan',
        'amount',
        'status',
        'started_at',
        'expires_at',
        'payment_id',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 有效订阅：状态 active，且非终身时未过期
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where(function (Builder $q): void {
                $q->where('plan', 'lifetime')
                    ->orWhereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        if ($this->plan === 'lifetime') {
            return true;
        }

        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    public function getPlanName(): string
    {
        return [
            'monthly' => '月度会员',
            'yearly' => '年度会员',
            'lifetime' => '终身会员',
        ][$this->plan] ?? '未知计划';
    }
}
