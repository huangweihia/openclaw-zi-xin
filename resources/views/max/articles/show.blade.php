{{-- AI 副业情报局 MAX - 文章详情页 --}}
@extends('layouts.max')

@section('title', $article['title'] ?? '文章详情 - AI 副业情报局 MAX')

@section('content')
{{-- 文章头部 --}}
<section class="gradient-bg py-12">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="flex items-center justify-center gap-2 mb-4">
            <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">📰 AI 资讯</span>
            @if($article['isVip'] ?? false)
            <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">👑 VIP 专属</span>
            @endif
        </div>
        <h1 class="text-4xl font-bold text-white mb-6">
            {{ $article['title'] ?? 'GPT-5 即将发布：这些变化将影响你的副业' }}
        </h1>
        <div class="flex items-center justify-center gap-6 text-purple-100 text-sm">
            <span>👁 {{ $article['views'] ?? '1,234' }}</span>
            <span>👍 {{ $article['likes'] ?? '56' }}</span>
            <span>📅 {{ $article['date'] ?? '2026-04-01' }}</span>
            <span>⏱️ {{ $article['readTime'] ?? '5 分钟阅读' }}</span>
        </div>
    </div>
</section>

{{-- 文章内容 --}}
<section class="py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            {{-- VIP 锁定内容 --}}
            @if($article['isVip'] ?? false)
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🔒</div>
                <h2 class="text-2xl font-bold mb-4">VIP 专属内容</h2>
                <p class="text-gray-600 mb-8">开通 VIP 解锁完整文章内容</p>
                <a href="{{ route('max.vip') }}" class="inline-block gradient-bg text-white px-8 py-4 rounded-full text-lg font-semibold hover:opacity-90 transition">
                    🚀 立即开通 VIP
                </a>
            </div>
            @else
            {{-- 文章内容 --}}
            <div class="prose max-w-none">
                <p class="text-lg text-gray-700 mb-6">
                    OpenAI 宣布 GPT-5 将于下月发布，这次更新带来了多项重大改进，将直接影响 AI 副业的开展方式...
                </p>
                
                <h2 class="text-2xl font-bold mb-4">一、GPT-5 的主要改进</h2>
                <p class="text-gray-700 mb-4">
                    1. <strong>多模态理解能力</strong>：GPT-5 可以同时理解文字、图片、音频等多种形式的输入...
                </p>
                <p class="text-gray-700 mb-4">
                    2. <strong>代码生成能力</strong>：支持更多编程语言，代码质量大幅提升...
                </p>
                <p class="text-gray-700 mb-4">
                    3. <strong>上下文理解</strong>：支持更长的上下文窗口，理解能力更强...
                </p>
                
                <h2 class="text-2xl font-bold mb-4">二、对 AI 副业的影响</h2>
                <p class="text-gray-700 mb-4">
                    1. <strong>内容创作</strong>：文章、文案、脚本等创作效率将进一步提升...
                </p>
                <p class="text-gray-700 mb-4">
                    2. <strong>编程服务</strong>：代码代写、Bug 修复等服务将面临新的机遇...
                </p>
                <p class="text-gray-700 mb-4">
                    3. <strong>教育培训</strong>：AI 培训、Prompt 工程等服务需求将增加...
                </p>
                
                <h2 class="text-2xl font-bold mb-4">三、如何应对</h2>
                <p class="text-gray-700 mb-4">
                    1. <strong>提前学习</strong>：熟悉 GPT-5 的新功能，掌握使用方法...
                </p>
                <p class="text-gray-700 mb-4">
                    2. <strong>调整方向</strong>：根据新能力调整副业方向，抓住新机遇...
                </p>
                <p class="text-gray-700 mb-4">
                    3. <strong>提升竞争力</strong>：学习更高级的 Prompt 技巧，提升服务质量...
                </p>
            </div>
            @endif
        </div>
        
        {{-- 标签 --}}
        <div class="flex flex-wrap gap-2 mb-8">
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">AI 资讯</span>
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">GPT-5</span>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">副业机会</span>
        </div>
        
        {{-- 互动区 --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button class="flex items-center gap-2 text-gray-600 hover:text-primary-600 transition">
                        <span class="text-2xl">👍</span>
                        <span>{{ $article['likes'] ?? '56' }}</span>
                    </button>
                    <button class="flex items-center gap-2 text-gray-600 hover:text-primary-600 transition">
                        <span class="text-2xl">⭐</span>
                        <span>收藏</span>
                    </button>
                    <button class="flex items-center gap-2 text-gray-600 hover:text-primary-600 transition">
                        <span class="text-2xl">📤</span>
                        <span>分享</span>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- 相关推荐 --}}
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold mb-4">📖 相关文章</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @include('components.max.content-card', [
                    'type' => 'article',
                    'title' => '用 ChatGPT 写小红书文案，效率提升 10 倍',
                    'summary' => '分享我用 ChatGPT 批量生成小红书文案的完整流程...',
                    'coverImage' => 'https://via.placeholder.com/400x200',
                    'tags' => ['技术教程', 'ChatGPT'],
                    'meta' => ['👁 2,345'],
                    'isVip' => false,
                    'route' => '#'
                ])
                
                @include('components.max.content-card', [
                    'type' => 'article',
                    'title' => '🔒 VIP：AI 代写服务完整 SOP',
                    'summary' => '从接单到交付的完整流程...',
                    'coverImage' => 'https://via.placeholder.com/400x200',
                    'tags' => ['变现案例', 'VIP'],
                    'meta' => ['👁 890'],
                    'isVip' => true,
                    'route' => '#'
                ])
            </div>
        </div>
    </div>
</section>
@endsection
