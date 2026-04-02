<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actionable_type',
        'actionable_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function actionable()
    {
        return $this->morphTo();
    }

    /**
     * 检查用户是否已执行该行为
     */
    public static function hasActioned(int $userId, string $type, $actionable): bool
    {
        return static::where('user_id', $userId)
            ->where('type', $type)
            ->where('actionable_type', get_class($actionable))
            ->where('actionable_id', $actionable->id)
            ->exists();
    }

    /**
     * 添加行为
     */
    public static function addAction(int $userId, string $type, $actionable): bool
    {
        if (self::hasActioned($userId, $type, $actionable)) {
            return false;
        }

        static::create([
            'user_id' => $userId,
            'actionable_type' => get_class($actionable),
            'actionable_id' => $actionable->id,
            'type' => $type,
        ]);

        return true;
    }

    /**
     * 移除行为
     */
    public static function removeAction(int $userId, string $type, $actionable): bool
    {
        return static::where('user_id', $userId)
            ->where('type', $type)
            ->where('actionable_type', get_class($actionable))
            ->where('actionable_id', $actionable->id)
            ->delete() > 0;
    }

    /**
     * 切换行为（有则删除，无则添加）
     */
    public static function toggleAction(int $userId, string $type, $actionable): bool
    {
        if (self::hasActioned($userId, $type, $actionable)) {
            self::removeAction($userId, $type, $actionable);
            return false;
        } else {
            self::addAction($userId, $type, $actionable);
            return true;
        }
    }
}
