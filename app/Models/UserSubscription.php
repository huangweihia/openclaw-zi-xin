<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan',
        'starts_at',
        'ends_at',
        'payment_method',
        'transaction_id',
        'amount',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    public function isVip(): bool
    {
        return $this->plan === 'vip' && $this->isActive();
    }

    public function getSearchQuotaAttribute(): int
    {
        if ($this->isVip()) {
            return -1; // unlimited
        }
        return 10; // free users get 10 searches per month
    }
}
