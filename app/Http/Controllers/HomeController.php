<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\Favorite;
use App\Models\Comment;
use App\Models\ProfileMessage;
use App\Models\ViewHistory;
use App\Models\VipUrgentNotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * 首页 - MAX 版本（转化型 Landing Page）
     */
    public function index()
    {
        // MAX 版本直接返回新视图，不需要数据
        return view('max.home');
    }

    /**
     * 个人中心（需要登录）
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // 我的收藏
        $favorites = Favorite::where('user_id', $user->id)
            ->with('favoritable')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // 我的评论
        $comments = Comment::where('user_id', $user->id)
            ->with('commentable')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // 浏览历史（ViewHistory 无 timestamps，使用 viewed_at 排序）
        $histories = ViewHistory::where('user_id', $user->id)
            ->with('viewable')
            ->orderBy('viewed_at', 'desc')
            ->limit(10)
            ->get();

        // 他人发给我的主页留言（个人中心展示）
        $profileMessagesReceived = ProfileMessage::where('recipient_id', $user->id)
            ->with('sender')
            ->latest()
            ->limit(8)
            ->get();
        
        // 统计数据
        $stats = [
            'favorites' => Favorite::where('user_id', $user->id)->count(),
            'comments' => Comment::where('user_id', $user->id)->count(),
            'histories' => ViewHistory::where('user_id', $user->id)->count(),
            'profile_messages' => ProfileMessage::where('recipient_id', $user->id)->count(),
        ];
        
        return view('dashboard', compact('user', 'favorites', 'comments', 'histories', 'stats', 'profileMessagesReceived'));
    }
    
    /**
     * 用户主页（公开）
     */
    public function userProfile($id)
    {
        $user = User::findOrFail($id);

        $stats = [
            'comments' => Comment::where('user_id', $user->id)->count(),
            'favorites' => Favorite::where('user_id', $user->id)->count(),
            'histories' => ViewHistory::where('user_id', $user->id)->count(),
            'profile_messages' => ProfileMessage::where('recipient_id', $user->id)->count(),
        ];

        $profileMessages = null;
        $urgentSentToday = false;
        $profileMessagesSent = null;

        if (auth()->check() && (int) auth()->id() === (int) $user->id) {
            $profileMessages = ProfileMessage::where('recipient_id', $user->id)
                ->with('sender')
                ->latest()
                ->paginate(15);
            $urgentSentToday = VipUrgentNotificationLog::query()
                ->where('sender_user_id', $user->id)
                ->whereDate('sent_at', now()->toDateString())
                ->exists();
        } elseif (auth()->check()) {
            // 访客：展示自己发给主页主人的留言记录（分页参数 sent_page）
            $profileMessagesSent = ProfileMessage::query()
                ->where('recipient_id', $user->id)
                ->where('sender_id', auth()->id())
                ->latest()
                ->paginate(10, ['*'], 'sent_page');
        }

        return view('users.show', compact('user', 'stats', 'profileMessages', 'urgentSentToday', 'profileMessagesSent'));
    }

    /**
     * 上传头像
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $user = auth()->user();

            // 若请求体过大，PHP 会直接丢弃文件字段
            if (!$request->hasFile('avatar')) {
                $message = sprintf(
                    '上传失败：未收到文件。请检查 PHP 配置 upload_max_filesize=%s, post_max_size=%s',
                    ini_get('upload_max_filesize'),
                    ini_get('post_max_size')
                );

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            $file = $request->file('avatar');

            if (!$file->isValid()) {
                $message = '上传失败：文件上传错误码 ' . $file->getError() . '（请检查 php.ini 的 upload_tmp_dir / upload_max_filesize / post_max_size）';

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            $validator = Validator::make($request->all(), [
                'avatar' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,webp,bmp,avif,tif,tiff,heic,heif', 'max:20480'],
            ], [
                'avatar.mimes' => '仅支持 jpeg/jpg/png/gif/webp/bmp/avif/tif/tiff/heic/heif 图片格式',
                'avatar.max' => '图片大小不能超过 20MB',
                'avatar.uploaded' => '文件上传失败，请检查服务器上传限制',
            ]);

            if ($validator->fails()) {
                $message = '上传失败：' . $validator->errors()->first('avatar');

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }

            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $ext;

            // 上传头像到 Laravel public disk：storage/app/public/avatars
            // 对外通过 /storage/... 访问（需要已执行 storage:link）
            $path = $file->storeAs('avatars', $filename, 'public');
            if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
                throw new \RuntimeException('头像保存失败：文件未写入存储目录，请检查 storage/app/public 权限');
            }

            // 兜底：确保文件在磁盘上确实存在（避免 storeAs 返回异常值）
            $storageFileAbs = storage_path('app/public/' . $path);
            if (! is_file($storageFileAbs)) {
                throw new \RuntimeException('头像保存失败：存储文件不存在，请检查 storage/app/public 写入情况');
            }

            // 避免“返回失败”：如果服务器没做 storage:link（没有 public/storage），
            // 就把文件再复制到 public/avatars，确保上传仍能成功且可访问。
            $publicStorage = public_path('storage');
            if (file_exists($publicStorage)) {
                $avatarUrl = asset('storage/' . $path);
            } else {
                $publicAvatarDir = public_path('avatars');
                if (! is_dir($publicAvatarDir)) {
                    @mkdir($publicAvatarDir, 0755, true);
                }

                $publicAvatarAbs = $publicAvatarDir . '/' . $filename;
                $copied = @copy($storageFileAbs, $publicAvatarAbs);
                if (! $copied || ! is_file($publicAvatarAbs)) {
                    throw new \RuntimeException('头像上传失败：服务器未启用 /storage 映射（缺少 public/storage），且无法写入 public/avatars，请检查目录权限并执行 php artisan storage:link');
                }

                $avatarUrl = asset('avatars/' . $filename);
            }

            // 删除旧头像：兼容历史 /avatars/*、/storage/avatars/* 以及完整 URL
            if (! empty($user->avatar) && is_string($user->avatar)) {
                $oldAvatarPath = $user->avatar;
                if (preg_match('#^https?://#i', $oldAvatarPath)) {
                    $parsed = parse_url($oldAvatarPath, PHP_URL_PATH);
                    $oldAvatarPath = is_string($parsed) ? $parsed : $oldAvatarPath;
                }

                if (str_starts_with($oldAvatarPath, '/avatars/')) {
                    $oldAvatarAbsolutePath = public_path(ltrim($oldAvatarPath, '/'));
                    if (is_file($oldAvatarAbsolutePath)) {
                        @unlink($oldAvatarAbsolutePath);
                    }
                } elseif (str_starts_with($oldAvatarPath, '/storage/')) {
                    $rel = ltrim($oldAvatarPath, '/'); // storage/avatars/xxx
                    if (str_starts_with($rel, 'storage/')) {
                        $oldKey = substr($rel, strlen('storage/')); // avatars/xxx
                        $oldAbs = storage_path('app/public/' . $oldKey);
                        if (is_file($oldAbs)) {
                            @unlink($oldAbs);
                        }
                    }
                }
            }

            $user->update([
                'avatar' => $avatarUrl,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '头像上传成功！',
                    'avatar_url' => $avatarUrl,
                ]);
            }

            return redirect()->route('dashboard')->with('success', '头像上传成功！');
        } catch (\Throwable $e) {
            $message = '上传失败：' . $e->getMessage();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }

            return back()->with('error', $message);
        }
    }
}
