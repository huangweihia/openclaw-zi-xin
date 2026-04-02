{{-- AI 副业情报局 MAX - 我的发布页 --}}
@extends('layouts.max')

@section('title', '我的发布 - AI 副业情报局 MAX')

@section('content')
{{-- 头部 --}}
<section class="gradient-bg py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">📝 我的发布</h1>
        <p class="text-xl text-purple-100">管理你发布的所有内容</p>
    </div>
</section>

{{-- 内容区 --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8">
            {{-- 左侧菜单 --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                    <nav class="space-y-2">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            📊 仪表盘
                        </a>
                        <a href="#" class="block px-4 py-3 gradient-bg text-white rounded-lg font-semibold">
                            📝 我的发布
                        </a>
                        <a href="{{ route('favorites.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            ⭐ 我的收藏
                        </a>
                        <a href="{{ route('history.index') }}" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            👁️ 浏览历史
                        </a>
                        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            💬 我的评论
                        </a>
                    </nav>
                </div>
            </div>
            
            {{-- 右侧内容 --}}
            <div class="md:col-span-3 space-y-8">
                {{-- 筛选区 --}}
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">筛选：</span>
                            <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                                <option value="">全部状态</option>
                                <option value="pending">⏳ 审核中</option>
                                <option value="approved">✅ 已通过</option>
                                <option value="rejected">❌ 已拒绝</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">类型：</span>
                            <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                                <option value="">全部类型</option>
                                <option value="article">文章</option>
                                <option value="case">案例</option>
                                <option value="tool">工具</option>
                                <option value="experience">心得</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                {{-- 发布列表（复用组件） --}}
                @include('components.max.publish-list', [
                    'items' => $posts ?? [],
                    'type' => 'my-posts',
                    'emptyMessage' => '还没有发布内容，开始分享你的经验吧',
                    'showActions' => true,
                    'showStats' => true
                ])
                
                {{-- 发布指南 --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="font-bold text-blue-800 mb-4">📖 发布指南</h3>
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-700">
                        <div>
                            <strong>✅ 推荐内容：</strong>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>真实的副业案例和经验</li>
                                <li>AI 工具使用心得和技巧</li>
                                <li>有数据支撑的变现方法</li>
                                <li>原创的技术教程</li>
                            </ul>
                        </div>
                        <div>
                            <strong>❌ 禁止内容：</strong>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>虚假或夸大的收入声明</li>
                                <li>抄袭或搬运的内容</li>
                                <li>广告或推广信息</li>
                                <li>违法或敏感内容</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                {{-- VIP 专属提示 --}}
                @if(!(auth()->user()->isVip ?? false))
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl shadow-lg p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-2">👑 VIP 专属权益</h3>
                            <p class="text-sm opacity-90">开通 VIP 后可发布更多类型内容，获得更高曝光</p>
                        </div>
                        <a href="{{ route('max.pricing') }}" class="bg-white text-purple-600 px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition whitespace-nowrap">
                            立即开通
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
