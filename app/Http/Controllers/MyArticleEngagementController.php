<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ContentSubmission;
use App\Models\Favorite;
use App\Models\UserAction;
use Illuminate\View\View;

class MyArticleEngagementController extends Controller
{
    /**
     * 已通过投稿发布的文章：点赞/收藏明细与汇总
     */
    public function index(): View
    {
        $user = auth()->user();

        $articleIds = ContentSubmission::query()
            ->where('user_id', $user->id)
            ->where('published_model_type', Article::class)
            ->whereNotNull('published_model_id')
            ->pluck('published_model_id')
            ->unique()
            ->values();

        $articles = Article::query()
            ->whereIn('id', $articleIds)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        $details = [];
        foreach ($articles as $article) {
            $likes = UserAction::query()
                ->where('actionable_type', Article::class)
                ->where('actionable_id', $article->id)
                ->where('type', 'like')
                ->with('user')
                ->orderByDesc('id')
                ->limit(50)
                ->get();

            $favorites = Favorite::query()
                ->where('favoritable_type', Article::class)
                ->where('favoritable_id', $article->id)
                ->with('user')
                ->orderByDesc('id')
                ->limit(50)
                ->get();

            $details[$article->id] = [
                'likes' => $likes,
                'favorites' => $favorites,
            ];
        }

        return view('articles.my-engagement', [
            'articles' => $articles,
            'details' => $details,
        ]);
    }
}
