<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewHistory extends Model
{
    use HasFactory;

    // `view_histories` 迁移未创建 created_at/updated_at，关闭 Eloquent 时间戳以避免 SQL 500。
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'viewable_type',
        'viewable_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * 用户关联
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 被浏览对象
     */
    public function viewable()
    {
        return $this->morphTo();
    }

    /**
     * 记录浏览历史
     */
    public static function record($user, $viewable)
    {
        if (!$user) return;
        
        // 检查是否已存在（避免重复记录）
        $exists = static::where('user_id', $user->id)
            ->where('viewable_type', get_class($viewable))
            ->where('viewable_id', $viewable->id)
            ->first();
        
        if ($exists) {
            // 更新浏览时间
            $exists->update(['viewed_at' => now()]);
        } else {
            // 创建新记录
            static::create([
                'user_id' => $user->id,
                'viewable_type' => get_class($viewable),
                'viewable_id' => $viewable->id,
                'viewed_at' => now(),
            ]);
        }
    }

    /**
     * 获取用户的浏览历史
     */
    public static function getUserHistory($user, $limit = 20)
    {
        return static::where('user_id', $user->id)
            ->with('viewable')
            ->orderBy('viewed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 清空用户浏览历史
     */
    public static function clearUserHistory($user)
    {
        return static::where('user_id', $user->id)->delete();
    }
}
