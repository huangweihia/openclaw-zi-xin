<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    // 指定表名为 positions（避免与 Laravel 队列 jobs 表冲突）
    protected $table = 'positions';

    protected $fillable = [
        'user_id',
        'title',
        'company_name',
        'location',
        'salary_range',
        'requirements',
        'description',
        'source_url',
        'contact_email',
        'contact_phone',
        'contact_wechat',
        'is_contact_vip',
        'is_vip_only',
        'is_published',
        'view_count',
        'apply_count',
        'published_at',
    ];

    protected $casts = [
        'is_contact_vip' => 'boolean',
        'is_vip_only' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * 获取发布者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取评论
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * 职位申请记录
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    /**
     * 检查用户是否可以查看联系方式
     */
    public function canViewContact($user): bool
    {
        if (!$user) {
            return false;
        }
        
        // 管理员或发布者本人可以查看
        if ($user->isAdmin() || $user->id === $this->user_id) {
            return true;
        }
        
        // 如果不需要 VIP，所有人都可以查看
        if (!$this->is_contact_vip) {
            return true;
        }
        
        // VIP 用户可以查看
        return $user->isVip();
    }

    /**
     * 是否可查看完整职位正文（含 VIP 专属）
     */
    public function userCanViewFullContent(?User $user): bool
    {
        if (! $this->is_vip_only) {
            return true;
        }
        if (! $user) {
            return false;
        }
        if ($user->isAdmin() || $user->id === $this->user_id) {
            return true;
        }

        return $user->isVip();
    }

    /**
     * 增加浏览次数
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
