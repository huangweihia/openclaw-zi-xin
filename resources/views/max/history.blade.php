{{-- AI 副业情报局 MAX - 浏览历史页 --}}
@extends('layouts.max')

@section('title', '浏览历史 - AI 副业情报局 MAX')

@section('content')
{{-- 头部 --}}
<section class="gradient-bg py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="text-7xl mb-4">👁️</div>
        <h1 class="text-4xl font-bold text-white mb-4">浏览历史</h1>
        <p class="text-xl text-white/90">记录你浏览过的所有优质内容</p>
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
                        <a href="{{ route('favorites.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">⭐</span>
                            <span>我的收藏</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 gradient-bg text-white rounded-xl font-medium shadow-md">
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
                {{-- 筛选和操作区 --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-gray-600 font-medium">筛选：</span>
                            <select class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="">全部类型</option>
                                <option value="article">📰 文章</option>
                                <option value="project">🚀 项目</option>
                                <option value="case">💰 案例</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-gray-600 font-medium">时间：</span>
                            <select class="border-2 border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
                                <option value="">全部时间</option>
                                <option value="today">今天</option>
                                <option value="week">本周</option>
                                <option value="month">本月</option>
                            </select>
                            <button class="text-red-600 hover:text-red-700 font-medium px-4 py-2 rounded-xl hover:bg-red-50 transition flex items-center gap-2">
                                <span>🗑️</span>
                                清空历史
                            </button>
                        </div>
                    </div>
                </div>
                
                {{-- 历史记录 --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">浏览记录（{{ count($histories ?? []) }}）</h2>
                        <div class="text-sm text-gray-500 flex items-center gap-2">
                            <span class="flex items-center gap-1">💡</span>
                            自动保留最近 90 天的记录
                        </div>
                    </div>
                    
                    @if(isset($histories) && count($histories) > 0)
                    <div class="space-y-4">
                        @foreach($histories as $history)
                        <div class="group flex items-center gap-4 p-5 bg-gradient-to-r from-white to-gray-50 border-2 border-gray-100 rounded-xl hover:border-primary-300 hover:shadow-lg transition-all duration-300">
                            {{-- 时间 --}}
                            <div class="text-center min-w-[90px] p-3 bg-gradient-to-br from-primary-500 to-purple-600 text-white rounded-xl shadow-md">
                                <div class="text-2xl font-bold">{{ $history['day'] ?? '01' }}</div>
                                <div class="text-xs opacity-90">{{ $history['month'] ?? '4 月' }}</div>
                            </div>
                            
                            {{-- 内容 --}}
                            <div class="flex-1">
                                {{-- 类型标签 --}}
                                <div class="flex items-center gap-2 mb-2">
                                    @if($history['type'] === 'article')
                                    <span class="text-xs bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-1 rounded-full font-medium">📰 文章</span>
                                    @elseif($history['type'] === 'project')
                                    <span class="text-xs bg-gradient-to-r from-purple-500 to-purple-600 text-white px-3 py-1 rounded-full font-medium">🚀 项目</span>
                                    @elseif($history['type'] === 'case')
                                    <span class="text-xs bg-gradient-to-r from-green-500 to-green-600 text-white px-3 py-1 rounded-full font-medium">💰 案例</span>
                                    @endif
                                    
                                    @if($history['is_vip'] ?? false)
                                    <span class="text-xs bg-gradient-to-r from-orange-400 to-orange-500 text-white px-3 py-1 rounded-full font-medium">👑 VIP</span>
                                    @endif
                                </div>
                                
                                {{-- 标题 --}}
                                <h3 class="font-bold text-gray-800 mb-2 text-lg group-hover:text-primary-600 transition">
                                    <a href="{{ $history['url'] ?? '#' }}" class="hover:underline">
                                        {{ $history['title'] ?? '浏览的内容' }}
                                    </a>
                                </h3>
                                
                                {{-- 摘要 --}}
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    {{ Str::limit($history['summary'] ?? '', 100) }}
                                </p>
                                
                                {{-- 统计信息 --}}
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">🕐 {{ $history['viewed_at'] ?? '刚刚' }}</span>
                                    <span class="flex items-center gap-1">👁️ {{ $history['views'] ?? 1 }}</span>
                                </div>
                            </div>
                            
                            {{-- 操作 --}}
                            <button class="text-gray-400 hover:text-red-600 hover:bg-red-50 p-3 rounded-xl transition opacity-0 group-hover:opacity-100" title="删除记录">
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
                        <div class="text-9xl mb-6 animate-pulse">👁️</div>
                        <h3 class="text-3xl font-bold text-gray-800 mb-4">还没有浏览记录</h3>
                        <p class="text-gray-600 mb-8 text-lg">开始浏览内容，你的浏览记录会显示在这里</p>
                        <a href="{{ route('max.home') }}" class="gradient-bg text-white px-8 py-4 rounded-full font-bold hover:opacity-90 transition shadow-xl transform hover:scale-105">
                            🏠 去首页逛逛
                        </a>
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
