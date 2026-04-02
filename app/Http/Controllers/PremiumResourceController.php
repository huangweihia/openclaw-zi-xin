<?php

namespace App\Http\Controllers;

use App\Models\PremiumResource;
use Illuminate\Http\Request;

class PremiumResourceController extends Controller
{
    /**
     * 付费资源列表
     */
    public function index(Request $request)
    {
        $query = PremiumResource::query();
        
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
        if ($request->get('sort') === 'downloads') {
            $query->orderByDesc('download_count');
        } elseif ($request->get('sort') === 'popular') {
            $query->orderByDesc('view_count');
        } else {
            $query->latest();
        }
        
        $resources = $query->paginate(12)->withQueryString();
        
        return view('max.resources.index', compact('resources'));
    }

    /**
     * 付费资源详情
     */
    public function show($slug)
    {
        $resource = PremiumResource::with(['comments.user'])->where('slug', $slug)->firstOrFail();
        
        // 增加浏览数
        $resource->increment('view_count');
        
        // 记录浏览历史
        if (auth()->check()) {
            \App\Models\ViewHistory::record(auth()->user(), $resource);
        }
        
        // 相关资源
        $relatedResources = PremiumResource::where('type', $resource->type)
            ->where('id', '!=', $resource->id)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();
        
        return view('max.resources.show', compact('resource', 'relatedResources'));
    }
}
