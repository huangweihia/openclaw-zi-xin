<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'subscribed_to_daily',
        'subscribed_to_weekly',
        'subscribed_to_notifications',
        'unsubscribe_token',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_to_daily' => 'boolean',
        'subscribed_to_weekly' => 'boolean',
        'subscribed_to_notifications' => 'boolean',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($subscription) {
            if (empty($subscription->unsubscribe_token)) {
                $subscription->unsubscribe_token = Str::random(32);
            }
        });
    }

    /**
     * 用户关联
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 根据邮箱获取或创建订阅
     */
    public static function getByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    /**
     * 根据退订令牌获取订阅
     */
    public static function getByToken(string $token): ?self
    {
        return self::where('unsubscribe_token', $token)->first();
    }

    /**
     * 检查是否已订阅日报
     */
    public function isSubscribedToDaily(): bool
    {
        return $this->subscribed_to_daily && !$this->unsubscribed_at;
    }

    /**
     * 检查是否已订阅周报
     */
    public function isSubscribedToWeekly(): bool
    {
        return $this->subscribed_to_weekly && !$this->unsubscribed_at;
    }

    /**
     * 检查是否已订阅通知
     */
    public function isSubscribedToNotifications(): bool
    {
        return $this->subscribed_to_notifications && ! $this->unsubscribed_at;
    }

    /**
     * 是否允许接收「系统通知」类站内信与模板邮件（与前台「系统通知」开关一致）。
     * 无邮件订阅记录则视为不允许，避免未显式订阅的用户收到通知。
     */
    public static function wantsSystemNotifications(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $sub = static::query()
            ->where(function ($q) use ($user): void {
                $q->where('user_id', $user->id)->orWhere('email', $user->email);
            })
            ->first();

        return $sub !== null && $sub->isSubscribedToNotifications();
    }

    /**
     * 退订所有
     */
    public function unsubscribeAll(): void
    {
        $this->update([
            'subscribed_to_daily' => false,
            'subscribed_to_weekly' => false,
            'subscribed_to_notifications' => false,
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * 重新订阅
     */
    public function resubscribe(): void
    {
        $this->update([
            'subscribed_to_daily' => true,
            'subscribed_to_weekly' => true,
            'subscribed_to_notifications' => true,
            'unsubscribed_at' => null,
            'unsubscribe_token' => Str::random(32),
        ]);
    }

    /**
     * 获取退订链接
     */
    public function getUnsubscribeUrl(): string
    {
        return url('/unsubscribe/' . $this->unsubscribe_token);
    }
}
