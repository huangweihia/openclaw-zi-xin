<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SystemNotificationController extends Controller
{
    protected function notificationsTableReady(): bool
    {
        return Schema::hasTable('system_notifications');
    }

    public function index(Request $request)
    {
        if (! $this->notificationsTableReady()) {
            return response()
                ->view('notifications.setup-required', [], 503);
        }

        $userId = auth()->id();

        $notifications = SystemNotification::query()
            ->where('user_id', $userId)
            ->orderByDesc('is_from_admin')
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = SystemNotification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(SystemNotification $notification)
    {
        if (! $this->notificationsTableReady()) {
            return back()->with('error', '系统通知尚未完成数据库升级，请执行 php artisan migrate。');
        }

        abort_unless((int) $notification->user_id === (int) auth()->id(), 403);
        $notification->markAsRead();

        return back()->with('success', '已标记已读');
    }

    public function markAllRead()
    {
        if (! $this->notificationsTableReady()) {
            return back()->with('error', '系统通知尚未完成数据库升级，请执行 php artisan migrate。');
        }

        SystemNotification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', '已全部标记为已读');
    }
}
