<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'is_public',
        'is_vip_only',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_vip_only' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(KnowledgeDocument::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeVipOnly($query)
    {
        return $query->where('is_vip_only', true);
    }
}
