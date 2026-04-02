<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsyncTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'total',
        'processed',
        'success',
        'failed',
        'error_message',
        'meta',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * 创建任务
     */
    public static function createTask(string $name, string $type, int $total = 0, array $meta = []): self
    {
        return static::create([
            'name' => $name,
            'type' => $type,
            'status' => 'pending',
            'total' => $total,
            'meta' => $meta,
        ]);
    }

    /**
     * 开始任务
     */
    public function start(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * 更新进度
     */
    public function updateProgress(int $processed, int $success = 0, int $failed = 0): void
    {
        $this->update([
            'processed' => $processed,
            'success' => $success,
            'failed' => $failed,
        ]);
    }

    /**
     * 完成任务
     */
    public function complete(int $success = 0, int $failed = 0): void
    {
        $this->update([
            'status' => 'completed',
            'processed' => $this->total,
            'success' => $success,
            'failed' => $failed,
            'completed_at' => now(),
        ]);
    }

    /**
     * 失败
     */
    public function fail(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * 获取进度百分比
     */
    public function getProgressAttribute(): float
    {
        if ($this->total === 0) return 0;
        return round(($this->processed / $this->total) * 100, 2);
    }

    /**
     * 作用域：最近的任务
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
