<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient',
        'subject',
        'content',
        'type',
        'template_id',
        'status',
        'error_message',
        'sent_at',
    ];

    /**
     * 模板关联
     */
    public function template()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * 作用域：已发送
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * 作用域：发送失败
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
