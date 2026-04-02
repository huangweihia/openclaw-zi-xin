{{-- AI 副业情报局 MAX - 项目列表页 --}}
@extends('layouts.max')

@section('title', 'AI 项目库 - AI 副业情报局 MAX')

@section('content')
{{-- Hero Section --}}
<section class="gradient-bg py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">🚀 AI 项目库</h1>
        <p class="text-xl text-purple-100">20+ AI 工具变现项目，每个都包含完整变现路径</p>
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
                    🎨 图像生成
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    ✍️ 文本写作
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    🎥 视频制作
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    🎵 音频处理
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    💻 代码编程
                </a>
            </div>
            
            {{-- 排序 --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">排序：</span>
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                    <option>最热门</option>
                    <option>最新</option>
                    <option>收入最高</option>
                    <option>难度最低</option>
                </select>
            </div>
        </div>
    </div>
</section>

{{-- 项目列表 --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {{-- 项目卡片 1 --}}
            @include('components.max.content-card', [
                'type' => 'project',
                'title' => 'Midjourney 商业变现',
                'summary' => '用 Midjourney 生成商业图片，为电商/自媒体提供图片服务，月入 10000+...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['图像生成', '月入 10000+'],
                'meta' => ['⭐ 95 分', '💰 高收益'],
                'isVip' => false,
                'route' => '#'
            ])
            
            {{-- 项目卡片 2 --}}
            @include('components.max.content-card', [
                'type' => 'project',
                'title' => 'ChatGPT 代写服务',
                'summary' => '在猪八戒/淘宝接单，用 ChatGPT 快速产出商业文案，赚取差价...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['文本写作', '月入 5000+'],
                'meta' => ['⭐ 90 分', '💰 中收益'],
                'isVip' => false,
                'route' => '#'
            ])
            
            {{-- 项目卡片 3 --}}
            @include('components.max.content-card', [
                'type' => 'project',
                'title' => '🔒 Runway 视频制作',
                'summary' => '用 Runway 制作商业短视频，为品牌/自媒体提供视频服务...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['视频制作', '月入 15000+'],
                'meta' => ['⭐ 92 分', '💰 高收益'],
                'isVip' => true,
                'route' => '#'
            ])
            
            {{-- 项目卡片 4 --}}
            @include('components.max.content-card', [
                'type' => 'project',
                'title' => 'ElevenLabs 语音合成',
                'summary' => '用 ElevenLabs 制作有声书/配音服务，为内容创作者提供语音服务...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['音频处理', '月入 8000+'],
                'meta' => ['⭐ 88 分', '💰 中收益'],
                'isVip' => false,
                'route' => '#'
            ])
            
            {{-- 项目卡片 5 --}}
            @include('components.max.content-card', [
                'type' => 'project',
                'title' => '🔒 Cursor 代码开发',
                'summary' => '用 Cursor 快速开发小型项目，接外包单，效率提升 5 倍...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['代码编程', '月入 20000+'],
                'meta' => ['⭐ 96 分', '💰 超高收益'],
                'isVip' => true,
                'route' => '#'
            ])
            
            {{-- 项目卡片 6 --}}
            @include('components.max.content-card', [
                'type' => 'project',
                'title' => 'Stable Diffusion 头像定制',
                'summary' => '用 SD 定制专属头像，在闲鱼/小红书售卖，低成本高利润...',
                'coverImage' => 'https://via.placeholder.com/400x200',
                'tags' => ['图像生成', '月入 3000+'],
                'meta' => ['⭐ 85 分', '💰 低门槛'],
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
        
        {{-- CTA --}}
        <div class="text-center mt-12">
            <a href="{{ route('max.vip') }}" class="inline-block gradient-bg text-white px-8 py-4 rounded-full text-lg font-semibold hover:opacity-90 transition">
                🚀 开通 VIP 解锁全部 20+ 项目
            </a>
        </div>
    </div>
</section>
@endsection
