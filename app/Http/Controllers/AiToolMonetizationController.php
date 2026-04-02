<?php

namespace App\Http\Controllers;

use App\Models\AiToolMonetization;
use Illuminate\Http\Request;

class AiToolMonetizationController extends Controller
{
    /**
     * AI 工具变现列表
     */
    public function index(Request $request)
    {
        $query = AiToolMonetization::query();
        
        // 分类筛选
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // 国内可用筛选
        if ($request->has('china')) {
            $query->where('available_in_china', true);
        }
        
        // 搜索
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('tool_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('content', fn($q) => $q->where('content', 'like', '%' . $request->search . '%'));
            });
        }
        
        // 排序
        if ($request->get('sort') === 'popular') {
            $query->orderByDesc('view_count');
        } else {
            $query->latest();
        }
        
        $tools = $query->paginate(12)->withQueryString();
        
        return view('max.tools.index', compact('tools'));
    }

    /**
     * AI 工具变现详情
     */
    public function show($slug)
    {
        $tool = AiToolMonetization::with(['comments.user'])->where('slug', $slug)->firstOrFail();
        
        // 增加浏览数
        $tool->increment('view_count');
        
        // 记录浏览历史
        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $tool);
        }
        
        // 相关工具
        $relatedTools = AiToolMonetization::where('category', $tool->category)
            ->where('id', '!=', $tool->id)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();
        
        return view('max.tools.show', compact('tool', 'relatedTools'));
    }
}
