<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'subscription_ends_at',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'subscription_ends_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Filament 后台访问权限
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    /**
     * 是否管理员
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * 是否 VIP
     */
    public function isVip(): bool
    {
        return $this->role === 'vip' || ($this->subscription_ends_at && $this->subscription_ends_at->isFuture());
    }

    /**
     * VIP 订阅仍有效，且到期日在未来 N 天以内（含当天），用于后台「到期提醒」按钮等。
     */
    public function isVipExpiryWithinDays(int $days = 3): bool
    {
        if (!$this->subscription_ends_at || !$this->subscription_ends_at->isFuture()) {
            return false;
        }

        return $this->subscription_ends_at->lte(now()->addDays($days)->endOfDay());
    }

    /**
     * 距离 subscription_ends_at 的剩余天数（按日期差，与 Carbon diffInDays 一致）；无有效到期日返回 null。
     */
    public function subscriptionDaysRemaining(): ?int
    {
        if (!$this->subscription_ends_at || !$this->subscription_ends_at->isFuture()) {
            return null;
        }

        return (int) now()->diffInDays($this->subscription_ends_at);
    }

    /**
     * 作者的文章
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * 主页收到的留言
     */
    public function receivedProfileMessages()
    {
        return $this->hasMany(ProfileMessage::class, 'recipient_id');
    }

    /**
     * 系统通知（站内信）
     */
    public function systemNotifications()
    {
        return $this->hasMany(SystemNotification::class);
    }

    /**
     * 用户积分
     */
    public function points()
    {
        return $this->hasOne(UserPoint::class);
    }

    /**
     * 获得积分
     */
    public function addPoints(int $amount, string $type, string $description, array $meta = []): bool
    {
        if ($amount <= 0) return false;

        $point = UserPoint::firstOrCreate(['user_id' => $this->id], ['balance' => 0, 'total_earned' => 0, 'total_spent' => 0]);
        $point->increment('balance', $amount);
        $point->increment('total_earned', $amount);

        PointTransaction::create([
            'user_id' => $this->id,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'meta' => $meta,
        ]);

        return true;
    }

    /**
     * 消耗积分
     */
    public function spendPoints(int $amount, string $type, string $description, array $meta = []): bool
    {
        if ($amount <= 0) return false;

        $point = UserPoint::where('user_id', $this->id)->first();
        
        if (!$point || $point->balance < $amount) {
            return false;
        }

        $point->decrement('balance', $amount);
        $point->increment('total_spent', $amount);

        PointTransaction::create([
            'user_id' => $this->id,
            'amount' => -$amount,
            'type' => $type,
            'description' => $description,
            'meta' => $meta,
        ]);

        return true;
    }

    /**
     * 当前积分余额
     */
    public function getPointsBalanceAttribute(): int
    {
        return $this->points?->balance ?? 0;
    }

    /**
     * 是否已点赞
     */
    public function hasLiked($model): bool
    {
        return UserAction::hasActioned($this->id, 'like', $model);
    }

    /**
     * 是否已收藏
     */
    public function hasFavorited($model): bool
    {
        return UserAction::hasActioned($this->id, 'favorite', $model);
    }

    /**
     * 更新最后登录
     */
    public function updateLastLogin($ip = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    /**
     * 评论/列表等处展示用头像地址（站内路径或外链，无图则用 ui-avatars）
     */
    public function avatarUrl(): string
    {
        $a = $this->avatar;
        if (filled($a)) {
            if (str_starts_with($a, 'http://') || str_starts_with($a, 'https://')) {
                return $a;
            }

            return str_starts_with($a, '/') ? $a : '/' . ltrim($a, '/');
        }

        return 'https://ui-avatars.com/api/?name=' . rawurlencode($this->name ?? 'U') . '&background=6366f1&color=fff&size=128';
    }
}