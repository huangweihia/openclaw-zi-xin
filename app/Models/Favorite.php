<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id',
    ];

    /**
     * 收藏者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 被收藏对象
     */
    public function favoritable()
    {
        return $this->morphTo();
    }
}
