<?php

namespace App\Http\Controllers;

use App\Models\ViewHistory;
use App\Models\Project;
use App\Models\Article;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * 浏览历史列表
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('message', '请先登录后查看历史记录');
        }
        
        $type = $request->get('type', 'all'); // all, projects, articles
        $perPage = 20;
        
        // 获取浏览历史
        if ($type === 'projects') {
            $histories = ViewHistory::where('user_id', $user->id)
                ->where('viewable_type', Project::class)
                ->with('viewable')
                ->latest('viewed_at')
                ->paginate($perPage);
        } elseif ($type === 'articles') {
            $histories = ViewHistory::where('user_id', $user->id)
                ->where('viewable_type', Article::class)
                ->with('viewable')
                ->latest('viewed_at')
                ->paginate($perPage);
        } else {
            $histories = ViewHistory::where('user_id', $user->id)
                ->with('viewable')
                ->latest('viewed_at')
                ->paginate($perPage);
        }
        
        // 统计数量
        $stats = [
            'all' => ViewHistory::where('user_id', $user->id)->count(),
            'projects' => ViewHistory::where('user_id', $user->id)
                ->where('viewable_type', Project::class)->count(),
            'articles' => ViewHistory::where('user_id', $user->id)
                ->where('viewable_type', Article::class)->count(),
        ];
        
        return view('history.index', compact('histories', 'type', 'stats'));
    }

    /**
     * 清空浏览历史
     */
    public function clear()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }
        
        ViewHistory::where('user_id', $user->id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => '已清空浏览历史',
        ]);
    }

    /**
     * 删除单条历史记录
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => '请先登录',
            ], 401);
        }
        
        $history = ViewHistory::where('user_id', $user->id)
            ->where('id', $id)
            ->first();
        
        if ($history) {
            $history->delete();
            return response()->json([
                'success' => true,
                'message' => '已删除历史记录',
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => '历史记录不存在',
        ], 404);
    }

    /**
     * 记录浏览（供其他控制器调用）
     */
    public static function record($viewable)
    {
        $user = auth()->user();
        if ($user) {
            ViewHistory::record($user, $viewable);
        }
    }
}
