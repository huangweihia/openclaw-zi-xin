<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\UserAction;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 文章列表 - MAX 版本
     */
    public function index(Request $request)
    {
        $query = Article::query();

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('summary', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $query->where('is_published', true);

        if ($request->get('sort') === 'popular') {
            $query->orderByDesc('view_count')->orderByDesc('like_count')->orderByDesc('published_at');
        } else {
            $query->latest('published_at');
        }

        $articles = $query->paginate(10)->withQueryString();

        return view('max.articles.index', compact('articles'));
    }

    /**
     * 文章详情 - MAX 版本
     */
    public function show($id)
    {
        $article = Article::with(['author', 'category'])->findOrFail($id);

        $article->increment('view_count');

        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $article);
        }

        $isFavorited = auth()->check() && $article->isFavoritedBy(auth()->user());
        $isLiked = auth()->check() && auth()->user()->hasLiked($article);

        $relatedArticles = $article->getRelatedArticles(5);
        $canViewFullArticle = $article->userCanViewFullContent(auth()->user());
        $showComments = ! $article->is_vip || $canViewFullArticle;

        $comments = collect();
        if ($showComments) {
            $comments = $article->comments()
                ->whereNull('parent_id')
                ->where('is_hidden', false)
                ->with(['user'])
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('max.articles.show', [
            'article' => $article,
            'isFavorited' => $isFavorited,
            'isLiked' => $isLiked,
            'relatedArticles' => $relatedArticles,
            'canViewFullArticle' => $canViewFullArticle,
            'showComments' => $showComments,
            'comments' => $comments,
        ]);
    }

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
        }

        return view('articles.show', [
            'article' => $article,
            'isFavorited' => $isFavorited,
            'isLiked' => $isLiked,
            'relatedArticles' => $relatedArticles,
            'canViewFullArticle' => $canViewFullArticle,
            'showComments' => $showComments,
            'comments' => $comments,
            'commentsTotal' => $commentsTotal,
            'featuredComment' => $featuredComment,
            'likedCommentIds' => $likedCommentIds,
            'hideGlobalAdSlot' => true,
        ]);
    }

    /**
     * 发表评论（文章）
     */
    public function storeComment($id, Request $request, SystemNotificationService $systemNotificationService)
    {
        $article = Article::findOrFail($id);
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }

        if (! $article->userCanViewFullContent($user)) {
            return response()->json([
                'success' => false,
                'message' => '开通 VIP 或解锁后可参与评论',
            ], 403);
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
            if (! $parentComment || $parentComment->commentable_type !== Article::class || (int) $parentComment->commentable_id !== (int) $article->id) {
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

        $comment = $article->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $parentId,
            'reply_to_id' => $replyToId,
        ]);

        $systemNotificationService->notifyArticleCommented($article, $user, $comment);
        if ($parentComment) {
            $systemNotificationService->notifyCommentReplied($article, $user, $comment, $parentComment);
        }

        return response()->json([
            'success' => true,
            'comment' => $comment->load(['user', 'replyTo.user']),
            'total' => $article->comments()->whereNull('parent_id')->where('is_hidden', false)->count(),
        ]);
    }

    /**
     * 收藏/取消收藏文章
     */
    public function toggleFavorite($id, SystemNotificationService $systemNotificationService)
    {
        $article = Article::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }

        $favorited = UserAction::toggleAction($user->id, 'favorite', $article);

        if ($favorited) {
            $article->increment('favorite_count');
            \App\Models\Favorite::firstOrCreate([
                'user_id' => $user->id,
                'favoritable_type' => Article::class,
                'favoritable_id' => $article->id,
            ]);
            $systemNotificationService->notifyArticleFavorited($article->fresh(), $user);
        } else {
            $article->decrement('favorite_count');
            \App\Models\Favorite::where('user_id', $user->id)
                ->where('favoritable_type', Article::class)
                ->where('favoritable_id', $article->id)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'isFavorited' => $favorited,
            'favorites_count' => $article->favorite_count,
        ]);
    }

    /**
     * 点赞文章
     */
    public function toggleLike($id, SystemNotificationService $systemNotificationService)
    {
        $article = Article::findOrFail($id);
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }

        $liked = UserAction::toggleAction($user->id, 'like', $article);

        if ($liked) {
            $article->increment('like_count');
            $systemNotificationService->notifyArticleLiked($article->fresh(), $user);
        } else {
            $article->decrement('like_count');
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'like_count' => $article->like_count,
        ]);
    }

    /**
     * VIP 专属文章列表
     */
    public function vipArticles()
    {
        $articles = Article::where('is_vip', true)
            ->where('is_published', true)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('articles.vip-index', compact('articles'));
    }
}
