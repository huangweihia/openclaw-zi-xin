<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserThemePreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'theme', 'dark_mode', 'font_size', 'follow_system',
    ];

    protected $casts = [
        'dark_mode' => 'boolean',
        'follow_system' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取或创建用户主题偏好
     */
    public static function getOrCreate($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            ['theme' => 'default', 'dark_mode' => false, 'font_size' => 'medium', 'follow_system' => false]
        );
    }
}
