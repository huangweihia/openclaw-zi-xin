<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\KnowledgeDocument;
use App\Models\Comment;
use App\Models\UserAction;
use App\Services\KnowledgeSearchService;
use Illuminate\Http\Request;

class KnowledgeController extends Controller
{
    protected $searchService;

    public function __construct(KnowledgeSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * 知识库首页
     */
    public function index(Request $request)
    {
        $bases = KnowledgeBase::query()
            ->where('is_public', true)
            ->with('user:id,name')
            ->withCount('documents')
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('knowledge.index', compact('bases'));
    }

    /**
     * 搜索
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1|max:200',
        ]);
        
        $query = $request->input('q');
        $user = auth()->user();
        
        // 检查搜索配额
        if ($user) {
            $quota = $this->searchService->checkSearchQuota($user->id);
            
            if (!$quota['can_search']) {
                return redirect()->route('knowledge.index')
                    ->with('error', "搜索次数已达上限，升级为 VIP 可无限搜索");
            }
        }
        
        // 执行搜索
        $results = $this->searchService->search($query, $user?->id);
        
        return view('knowledge.search', compact('results', 'query'));
    }

    /**
     * 知识库详情
     */
    public function show(KnowledgeBase $knowledgeBase)
    {
        // 权限检查
        if (!$knowledgeBase->is_public) {
            abort(403, '知识库未公开');
        }
        
        if ($knowledgeBase->is_vip_only && (!auth()->check() || (!auth()->user()->isVip() && !auth()->user()->isAdmin()))) {
            return redirect()->route('vip', ['redirect' => request()->fullUrl()])
                ->with('error', '此知识库仅 VIP 用户可访问');
        }
        
        $documents = $knowledgeBase->documents()
            ->latest()
            ->paginate(20);
        
        return view('knowledge.show', compact('knowledgeBase', 'documents'));
    }

    /**
     * 文档详情
     */
    public function showDocument(KnowledgeDocument $document)
    {
        $knowledgeBase = $document->knowledgeBase;
        
        // 权限检查
        if (!$knowledgeBase->is_public) {
            abort(403, '文档未公开');
        }
        
        if ($knowledgeBase->is_vip_only && (!auth()->check() || (!auth()->user()->isVip() && !auth()->user()->isAdmin()))) {
            return redirect()->route('vip', ['redirect' => request()->fullUrl()])
                ->with('error', '此文档仅 VIP 用户可访问');
        }
        
        // 增加浏览次数
        $document->incrementViewCount();
        
        // 记录浏览历史
        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $document);
        }
        
        $comments = collect();
        $commentsTotal = $document->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->count();

        $featuredComment = $document->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->withCount('replies')
            ->with(['user', 'replies.user', 'replies.replyTo.user'])
            ->orderByDesc('replies_count')
            ->orderByDesc('id')
            ->first();

        if ($featuredComment && (int) $featuredComment->replies_count < 1) {
            $featuredComment = null;
        }

        $commentsQuery = $document->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->with(['user', 'replies.user', 'replies.replyTo.user'])
            ->latest();

        if ($featuredComment) {
            $commentsQuery->where('id', '!=', $featuredComment->id);
        }

        if ($featuredComment) {
            $comments->push($featuredComment);
        }
        $comments = $comments->concat($commentsQuery->get());

        $likedCommentIds = auth()->check()
            ? UserAction::query()
                ->where('user_id', auth()->id())
                ->where('type', 'comment_like')
                ->where('actionable_type', Comment::class)
                ->pluck('actionable_id')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        return view('knowledge.document', compact(
            'document',
            'knowledgeBase',
            'comments',
            'commentsTotal',
            'featuredComment',
            'likedCommentIds'
        ));
    }

    /**
     * 发表评论（与文章评论区逻辑一致：支持楼中楼）
     */
    public function storeComment(Request $request, KnowledgeDocument $document)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录后评论',
            ], 401);
        }

        $knowledgeBase = $document->knowledgeBase;
        if (! $knowledgeBase->is_public) {
            return response()->json(['success' => false, 'message' => '无权评论'], 403);
        }
        if ($knowledgeBase->is_vip_only && (! $user->isVip() && ! $user->isAdmin())) {
            return response()->json(['success' => false, 'message' => '仅 VIP 可参与讨论'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'reply_to_id' => 'nullable|integer|exists:comments,id',
        ]);

        $parentId = $request->input('parent_id');
        $replyToId = $request->input('reply_to_id');
        $parentComment = null;

        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if (
                ! $parentComment
                || $parentComment->commentable_type !== KnowledgeDocument::class
                || (int) $parentComment->commentable_id !== (int) $document->id
            ) {
                return response()->json([
                    'success' => false,
                    'message' => '回复目标无效',
                ], 422);
            }

            if ($replyToId) {
                $replyTarget = Comment::find($replyToId);
                if (! $replyTarget || (int) ($replyTarget->parent_id ?: $replyTarget->id) !== (int) $parentComment->id) {
                    return response()->json([
                        'success' => false,
                        'message' => '引用目标无效',
                    ], 422);
                }
            }
        }

        $comment = $document->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $parentId,
            'reply_to_id' => $replyToId,
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load(['user', 'replyTo.user']),
            'total' => $document->comments()->whereNull('parent_id')->where('is_hidden', false)->count(),
        ]);
    }
}
