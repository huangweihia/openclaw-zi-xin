{{-- AI 副业情报局 MAX - 收藏列表页 --}}
@extends('layouts.max')

@section('title', '我的收藏 - AI 副业情报局 MAX')

@section('content')
{{-- 头部 --}}
<section class="gradient-bg py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="text-7xl mb-4">⭐</div>
        <h1 class="text-4xl font-bold text-white mb-4">我的收藏</h1>
        <p class="text-xl text-white/90">管理你收藏的所有优质内容</p>
    </div>
</section>

{{-- 内容区 --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8">
            {{-- 左侧菜单 --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-8 border border-gray-100">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">📋 功能菜单</h3>
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">📊</span>
                            <span>仪表盘</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">📝</span>
                            <span>我的发布</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 gradient-bg text-white rounded-xl font-medium shadow-md">
                            <span class="text-xl">⭐</span>
                            <span>我的收藏</span>
                        </a>
                        <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">👁️</span>
                            <span>浏览历史</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">💬</span>
                            <span>我的评论</span>
                        </a>
                    </nav>
                </div>
            </div>
            
            {{-- 右侧内容 --}}
            <div class="md:col-span-3 space-y-8">
                {{-- 筛选区 --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-gray-600 font-medium">筛选：</span>
                            <select class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="">全部类型</option>
                                <option value="article">📰 文章</option>
                                <option value="project">🚀 项目</option>
                                <option value="case">💰 案例</option>
                                <option value="tool">🛠️ 工具</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-gray-600 font-medium">排序：</span>
                            <select class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="latest">最新收藏</option>
                                <option value="oldest">最早收藏</option>
                                <option value="title">标题 A-Z</option>
                            </select>
                            <button class="text-red-600 hover:text-red-700 font-medium px-4 py-2 rounded-xl hover:bg-red-50 transition flex items-center gap-2">
                                <span>🗑️</span>
                                批量删除
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- 收藏列表 --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">收藏内容（{{ count($favorites ?? []) }}）</h2>
                        <div class="text-sm text-gray-500">
                            💡 提示：点击卡片查看详情
                        </div>
                    </div>
                    
                    @if(isset($favorites) && count($favorites) > 0)
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($favorites as $favorite)
                        <div class="group relative bg-gradient-to-br from-white to-gray-50 rounded-2xl border-2 border-gray-100 hover:border-primary-300 hover:shadow-xl transition-all duration-300 overflow-hidden">
                            {{-- 内容卡片 --}}
                            @include('components.max.content-card', [
                                'type' => $favorite['type'] ?? 'article',
                                'title' => $favorite['title'] ?? '收藏内容',
                                'summary' => $favorite['summary'] ?? '暂无摘要',
                                'coverImage' => $favorite['cover_image'] ?? null,
                                'tags' => $favorite['tags'] ?? [],
                                'meta' => ['📅 ' . ($favorite['created_at'] ?? '最近')],
                                'isVip' => $favorite['is_vip'] ?? false,
                                'route' => '#'
                            ])
                            
                            {{-- 删除按钮 --}}
                            <button class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-red-600 hover:text-red-700 hover:bg-red-50 p-2 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 transform group-hover:scale-110" title="取消收藏">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- 分页 --}}
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center gap-2">
                            <a href="#" class="px-4 py-2 border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 transition font-medium">上一页</a>
                            <a href="#" class="px-4 py-2 gradient-bg text-white rounded-xl font-medium shadow-md">1</a>
                            <a href="#" class="px-4 py-2 border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 transition font-medium">2</a>
                            <a href="#" class="px-4 py-2 border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 transition font-medium">3</a>
                            <span class="px-2 text-gray-400">...</span>
                            <a href="#" class="px-4 py-2 border-2 border-gray-200 rounded-xl hover:border-primary-500 hover:bg-primary-50 transition font-medium">下一页</a>
                        </nav>
                    </div>
                    @else
                    {{-- 空状态 --}}
                    <div class="text-center py-20">
                        <div class="text-9xl mb-6 animate-bounce">⭐</div>
                        <h3 class="text-3xl font-bold text-gray-800 mb-4">还没有收藏内容</h3>
                        <p class="text-gray-600 mb-8 text-lg">看到喜欢的内容就收藏起来吧，方便以后查看</p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a href="{{ route('max.home') }}" class="gradient-bg text-white px-8 py-4 rounded-full font-bold hover:opacity-90 transition shadow-xl transform hover:scale-105">
                                🏠 去首页逛逛
                            </a>
                            <a href="{{ route('max.articles.index') }}" class="border-2 border-primary-600 text-primary-600 px-8 py-4 rounded-full font-bold hover:bg-primary-50 transition transform hover:scale-105">
                                📰 浏览文章
                            </a>
                            <a href="{{ route('max.cases.index') }}" class="border-2 border-green-600 text-green-600 px-8 py-4 rounded-full font-bold hover:bg-green-50 transition transform hover:scale-105">
                                💰 查看案例
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 主题切换器 --}}
@include('components.max.theme-switcher')
@endsection
