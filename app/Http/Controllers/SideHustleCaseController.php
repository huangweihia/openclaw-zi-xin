<?php

namespace App\Http\Controllers;

use App\Models\SideHustleCase;
use App\Models\Category;
use Illuminate\Http\Request;

class SideHustleCaseController extends Controller
{
    /**
     * 副业案例列表
     */
    public function index(Request $request)
    {
        $query = SideHustleCase::query()->where('status', 'approved');
        
        // 分类筛选
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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
        if ($request->get('sort') === 'income') {
            $query->orderByDesc('estimated_income');
        } elseif ($request->get('sort') === 'popular') {
            $query->orderByDesc('view_count');
        } else {
            $query->latest();
        }
        
        $cases = $query->paginate(12)->withQueryString();
        
        return view('max.cases.index', compact('cases'));
    }

    /**
     * 副业案例详情
     */
    public function show($slug)
    {
        $case = SideHustleCase::with(['user', 'comments.user'])->where('slug', $slug)->firstOrFail();
        
        // 增加浏览数
        $case->increment('view_count');
        
        // 记录浏览历史
        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $case);
        }
        
        // 相关案例
        $relatedCases = SideHustleCase::where('category', $case->category)
            ->where('id', '!=', $case->id)
            ->where('status', 'approved')
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();
        
        return view('max.cases.show', compact('case', 'relatedCases'));
    }
}
