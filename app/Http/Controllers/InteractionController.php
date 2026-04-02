<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Project;
use App\Models\UserAction;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    /**
     * 点赞
     */
    public function like(Request $request, string $type, int $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['success' => false, 'message' => '内容不存在'], 404);
        }

        $liked = UserAction::toggleAction($user->id, 'like', $model);
        
        // 更新计数
        if ($liked) {
            $model->increment('like_count');
            // 点赞获得 2 积分
            $user->addPoints(2, 'like', '点赞内容', ['model' => $type, 'id' => $id]);
        } else {
            $model->decrement('like_count');
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'count' => $model->like_count,
        ]);
    }

    /**
     * 收藏
     */
    public function favorite(Request $request, string $type, int $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $model = $this->getModel($type, $id);
        
        if (!$model) {
            return response()->json(['success' => false, 'message' => '内容不存在'], 404);
        }

        $favorited = UserAction::toggleAction($user->id, 'favorite', $model);
        
        // 更新计数
        if ($favorited) {
            $model->increment('favorite_count');
            // 收藏获得 5 积分
            $user->addPoints(5, 'favorite', '收藏内容', ['model' => $type, 'id' => $id]);
        } else {
            $model->decrement('favorite_count');
        }

        return response()->json([
            'success' => true,
            'favorited' => $favorited,
            'count' => $model->favorite_count,
        ]);
    }

    /**
     * 积分解锁文章
     */
    public function unlockArticle(Request $request, int $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $article = Article::findOrFail($id);
        
        if (!$article->is_vip) {
            return response()->json(['success' => false, 'message' => '该文章无需解锁'], 400);
        }

        if ($article->userCanViewFullContent($user)) {
            return response()->json([
                'success' => true,
                'message' => '您已可阅读全文',
                'content' => $article->content,
            ]);
        }

        // 检查是否已解锁
        $unlocked = UserAction::hasActioned($user->id, 'unlock', $article);
        
        if ($unlocked) {
            return response()->json(['success' => true, 'message' => '已解锁', 'content' => $article->content]);
        }

        // 积分价格（根据文章设定，默认 100 积分）
        $pointCost = 100;
        
        // 检查积分
        if (!$user->points() || $user->points->balance < $pointCost) {
            return response()->json([
                'success' => false,
                'message' => "积分不足，需要 {$pointCost} 积分"
            ], 402);
        }

        // 消耗积分
        $user->spendPoints($pointCost, 'unlock', '解锁文章', ['article_id' => $id]);
        
        // 记录解锁
        UserAction::create([
            'user_id' => $user->id,
            'actionable_type' => Article::class,
            'actionable_id' => $article->id,
            'type' => 'unlock',
        ]);

        return response()->json([
            'success' => true,
            'message' => '解锁成功',
            'content' => $article->content,
        ]);
    }

    /**
     * 评论点赞
     */
    public function likeComment(int $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => '请先登录'], 401);
        }

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['success' => false, 'message' => '评论不存在'], 404);
        }

        $liked = UserAction::toggleAction($user->id, 'comment_like', $comment);

        if ($liked) {
            $comment->increment('like_count');
        } else {
            $comment->decrement('like_count');
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'count' => $comment->like_count,
        ]);
    }

    /**
     * 获取模型
     */
    private function getModel(string $type, int $id)
    {
        return match ($type) {
            'article' => Article::find($id),
            'project' => Project::find($id),
            'comment' => Comment::find($id),
            default => null,
        };
    }
}
