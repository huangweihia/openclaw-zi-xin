<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Project;
use App\Models\UserAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * 项目列表 - MAX 版本
     */
    public function index(Request $request)
    {
        $query = $this->projectListQuery($request, false);
        $projects = $query->paginate(12)->withQueryString();

        return view('max.projects.index', [
            'projects' => $projects,
        ]);
    }

    /**
     * 项目详情 - MAX 版本
     */
    public function show($id)
    {
        $project = Project::with(['category', 'comments.user'])->findOrFail($id);
        
        // 记录浏览历史
        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $project);
        }
        
        $comments = $project->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->with(['user'])
            ->latest()
            ->limit(10)
            ->get();
        
        // 相关项目推荐
        $relatedProjects = Project::where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->orderBy('stars', 'desc')
            ->limit(5)
            ->get();
        
        return view('max.projects.show', compact(
            'project',
            'comments',
            'relatedProjects'
        ));
    }
        
        $commentsTotal = $project->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->count();

        $featuredComment = $project->comments()
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

        $commentsQuery = $project->comments()
            ->whereNull('parent_id')
            ->where('is_hidden', false)
            ->with(['user', 'replies.user', 'replies.replyTo.user'])
            ->latest();

        if ($featuredComment) {
            $commentsQuery->where('id', '!=', $featuredComment->id);
        }

        $comments = collect();
        if ($featuredComment) {
            $comments->push($featuredComment);
        }
        $comments = $comments->concat($commentsQuery->get());
        
        // 检查用户是否已收藏
        $isFavorited = auth()->check() && $project->isFavoritedBy(auth()->user());
        
        // 相关项目推荐（同分类，排除自己）
        $relatedProjects = Project::where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->orderBy('stars', 'desc')
            ->limit(5)
            ->get();
        
        $likedCommentIds = auth()->check()
            ? UserAction::query()
                ->where('user_id', auth()->id())
                ->where('type', 'comment_like')
                ->where('actionable_type', Comment::class)
                ->pluck('actionable_id')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        $canViewFullProject = $project->userCanViewFullContent(auth()->user());

        return view('projects.show', compact(
            'project',
            'comments',
            'commentsTotal',
            'featuredComment',
            'isFavorited',
            'likedCommentIds',
            'relatedProjects',
            'canViewFullProject'
        ));
    }

    /**
     * VIP 专属项目列表（仅 is_vip = true）
     */
    public function vipProjects(Request $request)
    {
        $query = $this->projectListQuery($request, true);
        $projects = $query->paginate(12)->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'vipOnly' => true,
        ]);
    }

    /**
     * 列表与 VIP 列表共用的查询构造
     */
    protected function projectListQuery(Request $request, bool $vipOnly): Builder
    {
        $query = Project::query();

        if ($vipOnly) {
            $query->where('is_vip', true);
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('monetization')) {
            $query->where('monetization', $request->monetization);
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'popular', 'stars' => $query->orderByDesc('stars'),
                default => $query->latest('collected_at'),
            };
        } else {
            $query->latest('collected_at');
        }

        return $query;
    }

    /**
     * 收藏/取消收藏项目
     */
    public function toggleFavorite($id)
    {
        $project = Project::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }

        $favorited = UserAction::toggleAction($user->id, 'favorite', $project);

        if ($favorited) {
            $project->increment('favorite_count');
            Favorite::firstOrCreate([
                'user_id' => $user->id,
                'favoritable_type' => Project::class,
                'favoritable_id' => $project->id,
            ]);
        } else {
            $project->decrement('favorite_count');
            Favorite::where('user_id', $user->id)
                ->where('favoritable_type', Project::class)
                ->where('favoritable_id', $project->id)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'isFavorited' => $favorited,
            'favorites_count' => $project->favorite_count,
        ]);
    }

    /**
     * 发表评论（项目）
     */
    public function storeComment($id, Request $request)
    {
        $project = Project::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
            'reply_to_id' => 'nullable|integer|exists:comments,id',
        ]);

        $parentId = $request->input('parent_id');
        $replyToId = $request->input('reply_to_id');
        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if (!$parentComment || $parentComment->commentable_type !== Project::class || (int) $parentComment->commentable_id !== (int) $project->id) {
                return response()->json([
                    'success' => false,
                    'message' => '回复目标无效',
                ], 422);
            }

            if ($replyToId) {
                $replyTarget = Comment::find($replyToId);
                if (!$replyTarget || (int) ($replyTarget->parent_id ?: $replyTarget->id) !== (int) $parentComment->id) {
                    return response()->json([
                        'success' => false,
                        'message' => '引用目标无效',
                    ], 422);
                }
            }
        }

        $comment = $project->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $parentId,
            'reply_to_id' => $replyToId,
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'total' => $project->comments()->whereNull('parent_id')->where('is_hidden', false)->count(),
        ]);
    }
}
