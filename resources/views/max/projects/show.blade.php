{{-- AI 副业情报局 MAX - 项目详情页 --}}
@extends('layouts.max')

@section('title', $project['name'] ?? '项目详情 - AI 副业情报局 MAX')

@section('content')
{{-- 项目头部 --}}
<section class="gradient-bg py-12">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="flex items-center justify-center gap-2 mb-4">
            <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">🚀 {{ $project['category'] ?? '图像生成' }}</span>
            @if($project['isVip'] ?? false)
            <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">👑 VIP 专属</span>
            @endif
        </div>
        <h1 class="text-4xl font-bold text-white mb-6">
            {{ $project['name'] ?? 'Midjourney 商业变现' }}
        </h1>
        <div class="flex items-center justify-center gap-6 text-purple-100 text-sm mb-6">
            <span>⭐ {{ $project['score'] ?? '95 分' }}</span>
            <span>💰 {{ $project['income'] ?? '月入 10000+' }}</span>
            <span>⏱️ {{ $project['difficulty'] ?? '进阶级' }}</span>
            <span>👥 {{ $project['users'] ?? '1,234 人在做' }}</span>
        </div>
        <div class="flex items-center justify-center gap-4">
            <a href="#details" class="bg-white text-purple-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">
                查看详情
            </a>
            <button class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-purple-600 transition">
                ⭐ 收藏项目
            </button>
        </div>
    </div>
</section>

{{-- 项目详情 --}}
<section id="details" class="py-12">
    <div class="max-w-4xl mx-auto px-4">
        {{-- VIP 锁定 --}}
        @if($project['isVip'] ?? false)
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center mb-8">
            <div class="text-6xl mb-4">🔒</div>
            <h2 class="text-2xl font-bold mb-4">VIP 专属项目</h2>
            <p class="text-gray-600 mb-8">开通 VIP 解锁完整项目详情、变现路径、操作 SOP</p>
            <a href="{{ route('max.vip') }}" class="inline-block gradient-bg text-white px-8 py-4 rounded-full text-lg font-semibold hover:opacity-90 transition">
                🚀 立即开通 VIP
            </a>
        </div>
        @else
        {{-- 项目信息 --}}
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            {{-- 左侧：项目详情 --}}
            <div class="md:col-span-2 space-y-8">
                {{-- 项目介绍 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4">📋 项目介绍</h2>
                    <p class="text-gray-700 mb-4">
                        用 Midjourney 生成商业图片，为电商/自媒体提供图片服务。Midjourney 是目前最强的 AI 图像生成工具，生成的图片质量高、商用价值大...
                    </p>
                    <p class="text-gray-700">
                        适合人群：设计师、电商从业者、自媒体人、想搞副业的普通人
                    </p>
                </div>
                
                {{-- 变现路径 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4">💰 变现路径</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-xl">1️⃣</div>
                            <div>
                                <h3 class="font-semibold mb-2">电商产品图</h3>
                                <p class="text-gray-600 text-sm">为淘宝/拼多多商家生成产品场景图，收费 200-800 元/套</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-xl">2️⃣</div>
                            <div>
                                <h3 class="font-semibold mb-2">自媒体配图</h3>
                                <p class="text-gray-600 text-sm">为公众号/小红书提供配图，收费 50-200 元/张</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-xl">3️⃣</div>
                            <div>
                                <h3 class="font-semibold mb-2">头像/壁纸定制</h3>
                                <p class="text-gray-600 text-sm">为个人定制专属头像/壁纸，收费 50-150 元/张</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- 操作步骤 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold mb-4">📝 操作步骤</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">1</div>
                            <div>
                                <h3 class="font-semibold mb-2">学习 Midjourney 基础</h3>
                                <p class="text-gray-600 text-sm">掌握基本 Prompt 写法，了解参数含义（约 1-2 天）</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">2</div>
                            <div>
                                <h3 class="font-semibold mb-2">练习生成商业图片</h3>
                                <p class="text-gray-600 text-sm">针对电商/自媒体场景，练习生成高质量图片（约 3-5 天）</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">3</div>
                            <div>
                                <h3 class="font-semibold mb-2">寻找客户</h3>
                                <p class="text-gray-600 text-sm">在猪八戒/淘宝/闲鱼开店，或主动联系电商商家</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">4</div>
                            <div>
                                <h3 class="font-semibold mb-2">接单交付</h3>
                                <p class="text-gray-600 text-sm">按客户需求生成图片，修改至满意，收取费用</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- 右侧：项目信息 --}}
            <div class="space-y-6">
                {{-- 项目统计 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-bold mb-4">📊 项目数据</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">热门程度</span>
                            <span class="font-semibold">⭐ 95 分</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">启动成本</span>
                            <span class="font-semibold">💰 500 元</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">时间投入</span>
                            <span class="font-semibold">⏱️ 每天 2-3 小时</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">预估月收入</span>
                            <span class="font-semibold text-green-600">💰 10000+</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">难度等级</span>
                            <span class="font-semibold">📈 进阶级</span>
                        </div>
                    </div>
                </div>
                
                {{-- 所需工具 --}}
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-bold mb-4">🛠️ 所需工具</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span>
                            <span>Midjourney 账号（30 美元/月）</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span>
                            <span> Discord 账号（免费）</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-green-500">✓</span>
                            <span>接单平台账号（免费）</span>
                        </li>
                    </ul>
                </div>
                
                {{-- CTA --}}
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-2xl shadow-lg p-6 text-white text-center">
                    <h3 class="font-bold mb-2">🚀 开始这个项目</h3>
                    <p class="text-sm mb-4">开通 VIP 获取完整 SOP 和 Prompt 模板</p>
                    <a href="{{ route('max.vip') }}" class="block bg-white text-purple-600 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        立即开通 VIP
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
