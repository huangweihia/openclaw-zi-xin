<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProblemFeedback extends Model
{
    protected $table = 'problem_feedback';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image_path',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
        'rewarded_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        $path = str_replace('\\', '/', (string) $this->image_path);
        $path = ltrim($path, '/');
        // 仅使用本地 public disk 展示：/storage/.../
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return '/storage/' . $path;
    }
}

