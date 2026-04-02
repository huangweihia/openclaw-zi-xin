<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_spent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * 增加积分
     */
    public static function addPoints(int $userId, int $amount, string $type, string $description, array $meta = []): bool
    {
        if ($amount <= 0) return false;

        $point = static::firstOrCreate(['user_id' => $userId]);
        $point->increment('balance', $amount);
        $point->increment('total_earned', $amount);

        PointTransaction::create([
            'user_id' => $userId,
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
    public static function spendPoints(int $userId, int $amount, string $type, string $description, array $meta = []): bool
    {
        if ($amount <= 0) return false;

        $point = static::where('user_id', $userId)->first();
        
        if (!$point || $point->balance < $amount) {
            return false;
        }

        $point->decrement('balance', $amount);
        $point->increment('total_spent', $amount);

        PointTransaction::create([
            'user_id' => $userId,
            'amount' => -$amount,
            'type' => $type,
            'description' => $description,
            'meta' => $meta,
        ]);

        return true;
    }

    /**
     * 检查积分是否足够
     */
    public static function hasEnoughPoints(int $userId, int $amount): bool
    {
        $point = static::where('user_id', $userId)->first();
        return $point && $point->balance >= $amount;
    }
}
