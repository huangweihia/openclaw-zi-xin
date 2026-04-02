<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'title', 'content',
        'category', 'tags', 'cover_image', 'attachments',
        'visibility', 'status',
        'audit_note', 'audited_by', 'audited_at',
        'view_count', 'like_count', 'comment_count', 'favorite_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'attachments' => 'array',
        'audited_at' => 'datetime',
    ];

    // 关联作者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 关联审核人
    public function auditor()
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    // 关联评论
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // 作用域：已审核
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // 作用域：可见
    public function scopeVisible($query, $user = null)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public')
              ->where('status', 'approved');
            
            if ($user) {
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('user_id', $user->id);
                });
            }
        });
    }

    // 类型名称
    public function getTypeNameAttribute()
    {
        $types = [
            'case' => '副业案例',
            'tool' => '工具推荐',
            'experience' => '经验心得',
            'resource' => '资源分享',
            'question' => '问答求助',
        ];
        return $types[$this->type] ?? $this->type;
    }
}
