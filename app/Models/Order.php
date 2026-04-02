<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'user_id',
        'product_type',
        'product_id',
        'amount',
        'status',
        'payment_method',
        'payment_time',
        'paid_amount',
        'wechat_transaction_id',
        'remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_no)) {
                $order->order_no = 'ORD-' . strtoupper(Str::random(12));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 当 product_type 为 subscription 时，product_id 指向 subscriptions.id
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'product_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getStatusLabel(): string
    {
        return [
            'pending' => '待支付',
            'paid' => '已支付',
            'cancelled' => '已取消',
            'refunded' => '已退款',
        ][$this->status] ?? $this->status;
    }
}
