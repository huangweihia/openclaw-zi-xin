<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Article;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * 收藏列表页面
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('message', '请先登录后查看收藏');
        }
        
        $type = $request->get('type', 'all'); // all, projects, articles
        $perPage = 12;
        
        // 获取收藏数据
        if ($type === 'projects') {
            $favorites = Favorite::where('user_id', $user->id)
                ->where('favoritable_type', Project::class)
                ->with('favoritable')
                ->latest()
                ->paginate($perPage);
        } elseif ($type === 'articles') {
            $favorites = Favorite::where('user_id', $user->id)
                ->where('favoritable_type', Article::class)
                ->with('favoritable')
                ->latest()
                ->paginate($perPage);
        } else {
            // 获取所有收藏
            $favorites = Favorite::where('user_id', $user->id)
                ->with('favoritable')
                ->latest()
                ->paginate($perPage);
        }
        
        // 统计数量
        $stats = [
            'all' => Favorite::where('user_id', $user->id)->count(),
            'projects' => Favorite::where('user_id', $user->id)
                ->where('favoritable_type', Project::class)->count(),
            'articles' => Favorite::where('user_id', $user->id)
                ->where('favoritable_type', Article::class)->count(),
        ];
        
        return view('favorites.index', compact('favorites', 'type', 'stats'));
    }

    /**
     * 取消收藏
     */
    public function destroy($type, $id)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }
        
        $model = $type === 'project' ? Project::class : Article::class;
        
        $favorite = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', $model)
            ->where('favoritable_id', $id)
            ->first();
        
        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => '取消收藏成功',
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => '收藏不存在',
        ], 404);
    }

    /**
     * 批量取消收藏
     */
    public function bulkDestroy(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:favorites,id',
        ]);
        
        Favorite::where('user_id', $user->id)
            ->whereIn('id', $request->ids)
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => '批量取消收藏成功',
        ]);
    }
}
