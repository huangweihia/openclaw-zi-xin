<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'reply_to_id',
        'content',
        'like_count',
        'is_hidden',
    ];

    protected $casts = [
        'like_count' => 'integer',
        'is_hidden' => 'boolean',
    ];

    /**
     * 评论者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 被评论对象
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * 父评论
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * 被引用评论
     */
    public function replyTo()
    {
        return $this->belongsTo(Comment::class, 'reply_to_id');
    }

    /**
     * 回复
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * 点赞用户
     */
    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'comment_likes');
    }

    /**
     * 检查用户是否已点赞
     */
    public function isLikedBy($user): bool
    {
        if (!$user) return false;
        return $this->likedBy()->where('user_id', $user->id)->exists();
    }
}
