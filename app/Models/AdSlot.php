<?php

namespace App\Models;

use App\Support\PublicStorageFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdSlot extends Model
{
    protected $fillable = [
        'name',
        'is_enabled',
        'title',
        'body',
        'cta_label',
        'display_mode',
        'image_url',
        'image_path',
        'html_content',
        'link_url',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public static function globalSlot(): ?self
    {
        return static::query()->orderBy('id')->first();
    }

    /**
     * 解析后的图片地址：优先本地上传，其次外链（二选一）。
     */
    public function resolvedImageUrl(): ?string
    {
        if ($this->image_path) {
            // 若外部是全 URL，直接返回
            if (is_string($this->image_path) && (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://'))) {
                return $this->image_path;
            }

            $path = str_replace('\\', '/', (string) $this->image_path);
            $path = ltrim($path, '/');

            // 兼容 Filament / 手动填入时可能出现的不同前缀：
            // - storage/... / public/... 剥掉前缀
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, strlen('storage/'));
            }
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, strlen('public/'));
            }

            $basename = basename($path);
            $besideAvatar = 'avatars/ad-slots/'.$basename;
            $legacyRoot = 'ad-slots/'.$basename;

            PublicStorageFallback::ensurePublicWebCopyFromStorageLegacy($path);
            PublicStorageFallback::ensurePublicWebCopyFromStorageLegacy($legacyRoot);
            PublicStorageFallback::ensurePublicWebCopyFromStorageLegacy($besideAvatar);

            // 旧版在 public/ad-slots：复制到 public/avatars/ad-slots（与头像相同 nginx 规则）
            $legacyPublicFile = public_path('ad-slots/'.$basename);
            $besideAvatarAbs = public_path($besideAvatar);
            if (is_file($legacyPublicFile) && ! is_file($besideAvatarAbs)) {
                $dir = dirname($besideAvatarAbs);
                if (! is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                @copy($legacyPublicFile, $besideAvatarAbs);
            }

            $tryPaths = array_values(array_unique(array_filter([
                $path,
                $besideAvatar,
                $legacyRoot,
            ])));

            foreach ($tryPaths as $try) {
                try {
                    if (Storage::disk('public_web')->exists($try)) {
                        return Storage::disk('public_web')->url($try);
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            try {
                if (Storage::disk('public')->exists($path)) {
                    return Storage::disk('public')->url($path);
                }
            } catch (\Throwable $e) {
                // ignore
            }

            // 若磁盘查询失败/不存在，则兜底按 /storage/... 拼接
            return '/storage/'.$path;
        }

        return $this->image_url ?: null;
    }

    /**
     * 侧边栏是否应渲染（启用且至少有一种可展示内容）。
     */
    public function shouldDisplaySidebar(): bool
    {
        if (! $this->is_enabled) {
            return false;
        }

        if ($this->display_mode === 'html' && filled($this->html_content)) {
            return true;
        }

        return filled($this->resolvedImageUrl())
            || filled($this->title)
            || filled($this->body);
    }
}
