{{-- AI 副业情报局 MAX - 副业案例列表页 --}}
@extends('layouts.max')

@section('title', '副业案例 - AI 副业情报局 MAX')

@section('content')
{{-- Hero Section --}}
<section class="gradient-bg py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-4">💰 副业实战案例</h1>
        <p class="text-xl text-purple-100">50+ 真实案例，每个都包含收入验证 + 完整操作步骤</p>
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
                    线上副业
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    线下副业
                </a>
                <a href="#" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                    线上线下结合
                </a>
            </div>
            
            {{-- 排序 --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">排序：</span>
                <select class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                    <option>最新发布</option>
                    <option>收入最高</option>
                    <option>难度最低</option>
                    <option>最热门</option>
                </select>
            </div>
        </div>
    </div>
</section>

{{-- 案例列表 --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-8">
            {{-- 案例卡片 1 --}}
            @include('components.max.vip-lock-card', [
                'category' => '线上副业 · 入门级',
                'title' => '小红书虚拟资料变现：从 0 到月入 8000',
                'summary' => '通过整理和售卖考研/考公资料，在小红书引流到微信成交，单人可操作...',
                'tags' => [
                    ['label' => '启动：0 元', 'color' => 'green'],
                    ['label' => '月收入：8000+', 'color' => 'blue'],
                ],
                'route' => '#'
            ])
            @slot('tags')
            <li>✅ 完整操作步骤（4 周计划）</li>
            <li>✅ 收入截图验证</li>
            <li>✅ 工具清单 + 避坑指南</li>
            @endslot
            @endinclude
            
            {{-- 案例卡片 2 --}}
            @include('components.max.vip-lock-card', [
                'category' => '线上副业 · 进阶级',
                'title' => 'AI 代写服务：利用 ChatGPT 接商业文案单',
                'summary' => '在猪八戒/淘宝接单，用 AI 工具快速产出商业文案，赚取差价...',
                'tags' => [
                    ['label' => '启动：200 元', 'color' => 'green'],
                    ['label' => '月收入：5000+', 'color' => 'blue'],
                ],
                'route' => '#'
            ])
            @slot('tags')
            <li>✅ 接单渠道汇总</li>
            <li>✅ Prompt 模板库</li>
            <li>✅ 定价策略参考</li>
            @endslot
            @endinclude
            
            {{-- 案例卡片 3 --}}
            @include('components.max.vip-lock-card', [
                'category' => '线上副业 · 入门级',
                'title' => '抖音中视频计划：搬运 + 二次创作赚播放收益',
                'summary' => '剪辑影视解说/知识科普视频，参与中视频计划获得播放分成...',
                'tags' => [
                    ['label' => '启动：0 元', 'color' => 'green'],
                    ['label' => '月收入：3000+', 'color' => 'blue'],
                ],
                'route' => '#'
            ])
            @slot('tags')
            <li>✅ 剪辑教程</li>
            <li>✅ 选题技巧</li>
            <li>✅ 避坑指南</li>
            @endslot
            @endinclude
            
            {{-- 案例卡片 4 --}}
            @include('components.max.vip-lock-card', [
                'category' => '线上副业 · 专家级',
                'title' => '知识星球运营：打造付费社群',
                'summary' => '围绕特定主题建立付费社群，提供持续价值...',
                'tags' => [
                    ['label' => '启动：0 元', 'color' => 'green'],
                    ['label' => '月收入：15000+', 'color' => 'blue'],
                ],
                'route' => '#'
            ])
            @slot('tags')
            <li>✅ 社群运营 SOP</li>
            <li>✅ 内容规划模板</li>
            <li>✅ 续费技巧</li>
            @endslot
            @endinclude
            
            {{-- 案例卡片 5 --}}
            @include('components.max.vip-lock-card', [
                'category' => '线上副业 · 入门级',
                'title' => '闲鱼无货源电商：信息差赚差价',
                'summary' => '从拼多多/1688 选品，加价挂闲鱼，出单后上家直发...',
                'tags' => [
                    ['label' => '启动：0 元', 'color' => 'green'],
                    ['label' => '月收入：2000+', 'color' => 'blue'],
                ],
                'route' => '#'
            ])
            @slot('tags')
            <li>✅ 选品技巧</li>
            <li>✅ 上架优化</li>
            <li>✅ 售后处理</li>
            @endslot
            @endinclude
            
            {{-- 案例卡片 6 --}}
            @include('components.max.vip-lock-card', [
                'category' => '线上线下结合 · 进阶级',
                'title' => 'AI 摄影工作室：用 Midjourney 接单',
                'summary' => '用 AI 生成商业图片，为电商/自媒体提供图片服务...',
                'tags' => [
                    ['label' => '启动：500 元', 'color' => 'green'],
                    ['label' => '月收入：10000+', 'color' => 'blue'],
                ],
                'route' => '#'
            ])
            @slot('tags')
            <li>✅ 客户开发技巧</li>
            <li>✅ 交付标准</li>
            <li>✅ 定价策略</li>
            @endslot
            @endinclude
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
                🚀 开通 VIP 解锁全部 50+ 案例
            </a>
        </div>
    </div>
</section>

{{-- 用户案例 --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">💬 用户怎么说</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <p class="text-gray-700 mb-4">"跟着案例做了小红书虚拟资料，第 2 周就回本了！现在每月稳定多赚 3000+"</p>
                <div class="font-semibold">- 小王 · VIP 会员 3 个月</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow">
                <p class="text-gray-700 mb-4">"AI 代写服务太实用了！第 1 个月就接了 5 单，赚了 3000 多"</p>
                <div class="font-semibold">- 小李 · VIP 会员 2 个月</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow">
                <p class="text-gray-700 mb-4">"案例很详细，步骤清晰，照着做就能赚钱，强烈推荐！"</p>
                <div class="font-semibold">- 小张 · VIP 会员 6 个月</div>
            </div>
        </div>
    </div>
</section>
@endsection
