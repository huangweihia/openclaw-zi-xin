<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\User;

class NotificationService
{
    /**
     * 发送系统通知
     */
    public function send(User $user, string $title, string $content, ?string $actionUrl = null): SystemNotification
    {
        return SystemNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'content' => $content,
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);
    }

    /**
     * 发送评论回复通知
     */
    public function sendCommentReply(User $recipient, User $replier, string $commentContent): SystemNotification
    {
        return $this->send(
            $recipient,
            '💬 有人回复了你的评论',
            "{$replier->name} 回复了你：{$commentContent}",
            route('notifications.index')
        );
    }

    /**
     * 发送审核结果通知
     */
    public function sendAuditResult(User $user, string $contentType, bool $approved, ?string $reason = null): SystemNotification
    {
        $title = $approved ? '✅ 审核通过' : '❌ 审核未通过';
        $content = $approved
            ? "你的{$contentType}已通过审核，现在可以公开显示了。"
            : "你的{$contentType}未通过审核。原因：{$reason}";
        
        return $this->send($user, $title, $content);
    }

    /**
     * 发送会员到期提醒
     */
    public function sendVipExpiringReminder(User $user, int $daysLeft): SystemNotification
    {
        return $this->send(
            $user,
            '⏰ VIP 会员即将到期',
            "您的 VIP 会员还有 {$daysLeft} 天到期，及时续费享受更多优惠！",
            route('max.pricing')
        );
    }

    /**
     * 批量发送通知
     */
    public function sendBatch(array $users, string $title, string $content, ?string $actionUrl = null): int
    {
        $count = 0;
        foreach ($users as $user) {
            $this->send($user, $title, $content, $actionUrl);
            $count++;
        }
        return $count;
    }

    /**
     * 获取未读通知数
     */
    public function getUnreadCount(User $user): int
    {
        return SystemNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * 标记为已读
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = SystemNotification::findOrFail($notificationId);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        return true;
    }

    /**
     * 全部标记为已读
     */
    public function markAllAsRead(User $user): int
    {
        return SystemNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
