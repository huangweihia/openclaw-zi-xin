<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_base_id',
        'title',
        'file_path',
        'content',
        'file_type',
        'file_size',
        'chunks',
        'embedding',
        'view_count',
    ];

    protected $casts = [
        'chunks' => 'array',
        'embedding' => 'array',
        'view_count' => 'integer',
    ];

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}
