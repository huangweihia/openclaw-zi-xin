<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleInteractionController extends Controller
{
    /**
     * 点赞/取消点赞
     */
    public function toggleLike(Article $article, Request $request)
    {
        $user = $request->user();
        
        if ($user->hasLiked($article)) {
            // 取消点赞
            \App\Models\UserAction::where('user_id', $user->id)
                ->where('actionable_type', Article::class)
                ->where('actionable_id', $article->id)
                ->where('type', 'like')
                ->delete();
            
            $article->decrement('like_count');
            
            return response()->json([
                'success' => true,
                'liked' => false,
                'count' => $article->like_count,
            ]);
        } else {
            // 点赞
            \App\Models\UserAction::create([
                'user_id' => $user->id,
                'actionable_type' => Article::class,
                'actionable_id' => $article->id,
                'type' => 'like',
            ]);
            
            $article->increment('like_count');
            
            return response()->json([
                'success' => true,
                'liked' => true,
                'count' => $article->like_count,
            ]);
        }
    }

    /**
     * 收藏/取消收藏
     */
    public function toggleFavorite(Article $article, Request $request)
    {
        $user = $request->user();
        
        if ($user->hasFavorited($article)) {
            // 取消收藏
            \App\Models\UserAction::where('user_id', $user->id)
                ->where('actionable_type', Article::class)
                ->where('actionable_id', $article->id)
                ->where('type', 'favorite')
                ->delete();
            
            $article->decrement('favorite_count');
            
            return response()->json([
                'success' => true,
                'favorited' => false,
                'count' => $article->favorite_count,
            ]);
        } else {
            // 收藏
            \App\Models\UserAction::create([
                'user_id' => $user->id,
                'actionable_type' => Article::class,
                'actionable_id' => $article->id,
                'type' => 'favorite',
            ]);
            
            $article->increment('favorite_count');
            
            return response()->json([
                'success' => true,
                'favorited' => true,
                'count' => $article->favorite_count,
            ]);
        }
    }

    /**
     * 积分解锁文章
     */
    public function unlockArticle(Article $article, Request $request)
    {
        $user = $request->user();
        
        if (!$article->is_vip) {
            return response()->json([
                'success' => false,
                'message' => '这篇文章不需要解锁',
            ]);
        }
        
        if ($user->isVip()) {
            return response()->json([
                'success' => false,
                'message' => 'VIP 用户无需解锁',
            ]);
        }
        
        if ($user->points_balance < 100) {
            return response()->json([
                'success' => false,
                'message' => '积分不足',
            ]);
        }
        
        // 消耗积分
        $user->spendPoints(100, 'article_unlock', "解锁文章：{$article->title}");
        
        return response()->json([
            'success' => true,
            'message' => '解锁成功',
        ]);
    }
}
