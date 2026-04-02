<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'summary',
        'content',
        'is_paid',
        'price',
        'currency',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
        'published_at',
        'payload',
        'published_model_type',
        'published_model_id',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'price' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function publishedModel()
    {
        return $this->morphTo(__FUNCTION__, 'published_model_type', 'published_model_id');
    }
}
