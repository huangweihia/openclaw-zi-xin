<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PostAuditController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * 待审核列表
     */
    public function pending()
    {
        $posts = UserPost::where('status', 'pending')
            ->with('user')
            ->latest()
            ->paginate(20);
        
        return view('filament.pages.post-audit', compact('posts'));
    }

    /**
     * 审核通过
     */
    public function approve($id, Request $request)
    {
        $post = UserPost::findOrFail($id);
        
        $post->update([
            'status' => 'approved',
            'audited_by' => auth()->id(),
            'audited_at' => now(),
            'audit_note' => $request->audit_note,
        ]);

        // 发送审核通过通知
        $this->notificationService->sendAuditResult($post->user, $post->type_name, true);

        return back()->with('success', '审核通过');
    }

    /**
     * 审核拒绝
     */
    public function reject($id, Request $request)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $post = UserPost::findOrFail($id);
        
        $post->update([
            'status' => 'rejected',
            'audited_by' => auth()->id(),
            'audited_at' => now(),
            'audit_note' => $request->reject_reason,
        ]);

        // 发送审核拒绝通知
        $this->notificationService->sendAuditResult($post->user, $post->type_name, false, $request->reject_reason);

        return back()->with('success', '已拒绝');
    }

    /**
     * 批量审核
     */
    public function batchApprove(Request $request)
    {
        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'exists:user_posts,id',
        ]);

        $count = 0;
        foreach ($request->post_ids as $postId) {
            $post = UserPost::findOrFail($postId);
            $post->update([
                'status' => 'approved',
                'audited_by' => auth()->id(),
                'audited_at' => now(),
            ]);
            $this->notificationService->sendAuditResult($post->user, $post->type_name, true);
            $count++;
        }

        return back()->with('success', "已通过 {$count} 个发布");
    }
}
