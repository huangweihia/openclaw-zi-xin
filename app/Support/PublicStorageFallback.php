<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * 与头像上传一致：无 public/storage 软链时，仍可通过 public/ 下副本访问。
 * 广告位等后台上传使用 public_web 盘；历史数据可能仅在 storage/app/public。
 */
final class PublicStorageFallback
{
    /**
     * 若文件仅在 legacy public 盘（storage/app/public）而尚未复制到站点 public 目录，则复制一份。
     */
    public static function ensurePublicWebCopyFromStorageLegacy(string $relativePath): void
    {
        $relativePath = self::normalizeRelativePath($relativePath);
        if ($relativePath === '') {
            return;
        }

        if (Storage::disk('public_web')->exists($relativePath)) {
            return;
        }

        if (! Storage::disk('public')->exists($relativePath)) {
            return;
        }

        $src = storage_path('app/public/'.$relativePath);
        $dest = public_path($relativePath);
        if (! is_file($src)) {
            return;
        }

        $dir = dirname($dest);
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        @copy($src, $dest);
    }

    /**
     * 从 public_web 与 legacy public 盘删除同一相对路径（用于替换/清空上传）。
     */
    public static function deleteFromBothDisks(?string $relativePath): void
    {
        $relativePath = self::normalizeRelativePath((string) $relativePath);
        if ($relativePath === '') {
            return;
        }

        $basename = basename($relativePath);
        $toDelete = array_values(array_unique(array_filter([
            $relativePath,
            'avatars/ad-slots/'.$basename,
            'ad-slots/'.$basename,
        ])));

        foreach ($toDelete as $p) {
            try {
                Storage::disk('public_web')->delete($p);
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                Storage::disk('public')->delete($p);
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }

    private static function normalizeRelativePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return $path;
    }
}
