<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'meta',
    ];

    protected $casts = [
        'amount' => 'integer',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 作用域：获得积分
     */
    public function scopeEarned($query)
    {
        return $query->where('amount', '>', 0);
    }

    /**
     * 作用域：消耗积分
     */
    public function scopeSpent($query)
    {
        return $query->where('amount', '<', 0);
    }
}
