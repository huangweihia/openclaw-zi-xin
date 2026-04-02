<?php

namespace App\Http\Controllers;

use App\Models\PrivateTrafficSop;
use Illuminate\Http\Request;

class PrivateTrafficSopController extends Controller
{
    /**
     * 运营 SOP 列表
     */
    public function index(Request $request)
    {
        $query = PrivateTrafficSop::query();
        
        // 平台筛选
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        
        // 类型筛选
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // 搜索
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('summary', 'like', '%' . $request->search . '%');
            });
        }
        
        // 排序
        if ($request->get('sort') === 'popular') {
            $query->orderByDesc('view_count');
        } else {
            $query->latest();
        }
        
        $sops = $query->paginate(12)->withQueryString();
        
        return view('max.sops.index', compact('sops'));
    }

    /**
     * 运营 SOP 详情
     */
    public function show($slug)
    {
        $sop = PrivateTrafficSop::with(['comments.user'])->where('slug', $slug)->firstOrFail();
        
        // 增加浏览数
        $sop->increment('view_count');
        
        // 记录浏览历史
        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $sop);
        }
        
        // 相关 SOP
        $relatedSops = PrivateTrafficSop::where('platform', $sop->platform)
            ->where('id', '!=', $sop->id)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();
        
        return view('max.sops.show', compact('sop', 'relatedSops'));
    }
}
