{{-- AI 副业情报局 MAX - 文章列表页 --}}
@extends('layouts.max')

@section('title', 'AI 文章 - AI 副业情报局 MAX')

@section('content')
{{-- Hero Section --}}
<section class="gradient-bg py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">📰 AI 文章</h1>
        <p class="text-xl text-purple-100">每日更新最新 AI 资讯、技术教程、变现案例</p>
    </div>
</section>

{{-- 筛选区 --}}
<section class="py-8 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- 分类筛选 --}}
            <div class="flex flex-wrap gap-2">
                <a href="#" class="px-4 py-2 gradient-bg text-white rounded-full text-sm font-semibold">
                    全部
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    AI 资讯
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    技术教程
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    变现案例
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    🔒 VIP 专属
                </a>
            </div>
            
            {{-- 搜索框 --}}
            <div class="flex items-center gap-2">
                <input type="text" placeholder="搜索文章..." class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 w-64">
                <button class="gradient-bg text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition">
                    搜索
                </button>
            </div>
        </div>
    </div>
</section>

{{-- 文章列表 --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- 文章卡片 1 --}}
            @include('components.max.content-card', [
                'type' => 'article',
                'title' => 'GPT-5 即将发布：这些变化将影响你的副业',
                'summary' => 'OpenAI 宣布 GPT-5 将于下月发布，新增多模态理解、代码生成等能力，这将如何影响 AI 副业...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['AI 资讯', 'GPT-5'],
                'meta' => ['👁 1,234', '📅 2026-04-01'],
                'isVip' => false,
                'route' => '#'
            ])
            
            {{-- 文章卡片 2 --}}
            @include('components.max.content-card', [
                'type' => 'article',
                'title' => '用 ChatGPT 写小红书文案，效率提升 10 倍',
                'summary' => '分享我用 ChatGPT 批量生成小红书文案的完整流程，包括 Prompt 模板、优化技巧...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['技术教程', 'ChatGPT'],
                'meta' => ['👁 2,345', '📅 2026-03-31'],
                'isVip' => false,
                'route' => '#'
            ])
            
            {{-- 文章卡片 3 --}}
            @include('components.max.content-card', [
                'type' => 'article',
                'title' => '🔒 VIP：AI 代写服务完整 SOP，月入 5000+',
                'summary' => '从接单到交付的完整流程，包含定价策略、客户沟通、质量控制...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['变现案例', 'VIP'],
                'meta' => ['👁 890', '📅 2026-03-30'],
                'isVip' => true,
                'route' => '#'
            ])
            
            {{-- 文章卡片 4 --}}
            @include('components.max.content-card', [
                'type' => 'article',
                'title' => 'Midjourney V6 新功能：商业图片一键生成',
                'summary' => 'Midjourney 发布 V6 版本，新增商业图片生成、文字渲染等功能...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['AI 资讯', 'Midjourney'],
                'meta' => ['👁 3,456', '📅 2026-03-29'],
                'isVip' => false,
                'route' => '#'
            ])
            
            {{-- 文章卡片 5 --}}
            @include('components.max.content-card', [
                'type' => 'article',
                'title' => '🔒 VIP：用 AI 做抖音号，30 天涨粉 1 万实操',
                'summary' => '从账号定位到内容制作，完整分享我用 AI 工具运营抖音号的实操经验...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['变现案例', '抖音'],
                'meta' => ['👁 1,567', '📅 2026-03-28'],
                'isVip' => true,
                'route' => '#'
            ])
            
            {{-- 文章卡片 6 --}}
            @include('components.max.content-card', [
                'type' => 'article',
                'title' => '2026 年 AI 副业趋势分析：这 5 个方向最赚钱',
                'summary' => '基于 1000+ 案例数据分析，2026 年最值得投入的 AI 副业方向...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['行业分析', '趋势'],
                'meta' => ['👁 5,678', '📅 2026-03-27'],
                'isVip' => false,
                'route' => '#'
            ])
        </div>
        
        {{-- 分页 --}}
        <div class="mt-12 flex justify-center">
            <nav class="flex items-center gap-2">
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">上一页</a>
                <a href="#" class="px-4 py-2 gradient-bg text-white rounded-lg">1</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">2</a>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">3</a>
                <span class="px-2 text-gray-500">...</span>
                <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">下一页</a>
            </nav>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">🔒 更多 VIP 专属文章</h2>
        <p class="text-gray-600 mb-8">开通 VIP 解锁全部 500+ 篇深度文章</p>
        <a href="{{ route('max.vip') }}" class="inline-block gradient-bg text-white px-8 py-4 rounded-full text-lg font-semibold hover:opacity-90 transition">
            🚀 立即开通 VIP
        </a>
    </div>
</section>
@endsection
