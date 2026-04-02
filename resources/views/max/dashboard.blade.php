{{-- AI 副业情报局 MAX - 个人中心/仪表盘 --}}
@extends('layouts.max')

@section('title', '个人中心 - AI 副业情报局 MAX')

@section('content')
{{-- 用户信息头部 --}}
<section class="gradient-bg py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center gap-6">
            {{-- 头像 --}}
            <div class="relative">
                <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center text-6xl shadow-2xl border-4 border-white/30">
                    👤
                </div>
                @if(auth()->user()->isVip ?? false)
                <div class="absolute -bottom-2 -right-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                    👑 VIP
                </div>
                @endif
            </div>
            
            {{-- 用户信息 --}}
            <div class="text-white text-center md:text-left flex-1">
                <h1 class="text-3xl font-bold mb-2">{{ auth()->user()->name ?? '用户' }}</h1>
                <div class="flex flex-wrap justify-center md:justify-start gap-3 text-sm">
                    <span class="bg-white/20 px-4 py-2 rounded-full backdrop-blur-sm">
                        @if(auth()->user()->isVip ?? false)
                            👑 VIP 会员
                        @else
                            🆓 免费用户
                        @endif
                    </span>
                    <span class="bg-white/20 px-4 py-2 rounded-full backdrop-blur-sm">
                        📧 {{ auth()->user()->email ?? '未设置' }}
                    </span>
                    <span class="bg-white/20 px-4 py-2 rounded-full backdrop-blur-sm">
                        📅 注册 {{ (auth()->user()->created_at ?? now())->diffForHumans() }}
                    </span>
                </div>
            </div>
            
            {{-- 操作按钮 --}}
            <div class="flex gap-3">
                @if(!(auth()->user()->isVip ?? false))
                <a href="{{ route('max.vip') }}" class="bg-white text-purple-600 px-6 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-lg transform hover:scale-105">
                    💎 开通 VIP
                </a>
                @endif
                <button class="bg-white/20 text-white px-6 py-3 rounded-full font-bold hover:bg-white/30 transition backdrop-blur-sm">
                    ⚙️ 设置
                </button>
            </div>
        </div>
    </div>
</section>

{{-- 统计卡片 --}}
<section class="py-8 -mt-8">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover border border-gray-100">
                <div class="text-5xl mb-3">📄</div>
                <div class="text-4xl font-bold gradient-bg bg-clip-text text-transparent mb-2">{{ $stats['posts'] ?? 0 }}</div>
                <div class="text-gray-600 font-medium">我的发布</div>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover border border-gray-100">
                <div class="text-5xl mb-3">⭐</div>
                <div class="text-4xl font-bold gradient-bg bg-clip-text text-transparent mb-2">{{ $stats['favorites'] ?? 0 }}</div>
                <div class="text-gray-600 font-medium">我的收藏</div>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover border border-gray-100">
                <div class="text-5xl mb-3">👁️</div>
                <div class="text-4xl font-bold gradient-bg bg-clip-text text-transparent mb-2">{{ $stats['views'] ?? 0 }}</div>
                <div class="text-gray-600 font-medium">浏览历史</div>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover border border-gray-100">
                <div class="text-5xl mb-3">💰</div>
                <div class="text-4xl font-bold gradient-bg bg-clip-text text-transparent mb-2">{{ $stats['points'] ?? 0 }}</div>
                <div class="text-gray-600 font-medium">我的积分</div>
            </div>
        </div>
    </div>
</section>

{{-- 主内容区 --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8">
            {{-- 左侧菜单 --}}
            <div class="md:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-8 border border-gray-100">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">📋 功能菜单</h3>
                    <nav class="space-y-2">
                        <a href="#" class="flex items-center gap-3 px-4 py-3 gradient-bg text-white rounded-xl font-medium shadow-md">
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
                        <a href="{{ route('history.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">👁️</span>
                            <span>浏览历史</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">💬</span>
                            <span>我的评论</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 rounded-xl font-medium transition">
                            <span class="text-xl">⚙️</span>
                            <span>账号设置</span>
                        </a>
                    </nav>
                </div>
            </div>
            
            {{-- 右侧内容 --}}
            <div class="md:col-span-3 space-y-8">
                {{-- 最近发布 --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">📝 最近发布</h2>
                        <a href="#" class="text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1 transition">
                            查看全部 <span>→</span>
                        </a>
                    </div>
                    
                    @if(isset($recentPosts) && count($recentPosts) > 0)
                    <div class="space-y-4">
                        @foreach($recentPosts as $post)
                        <div class="flex items-start gap-4 p-5 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:shadow-lg transition border border-gray-100">
                            <div class="text-4xl">📄</div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 mb-2 text-lg">{{ $post['title'] ?? '示例标题' }}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($post['summary'] ?? '暂无摘要', 120) }}</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">📅 {{ $post['created_at'] ?? '刚刚' }}</span>
                                    <span class="flex items-center gap-1">👁️ {{ $post['views'] ?? 0 }}</span>
                                    <span class="flex items-center gap-1">👍 {{ $post['likes'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button class="text-primary-600 hover:text-primary-700 font-medium text-sm px-3 py-1 rounded-lg hover:bg-primary-50 transition">编辑</button>
                                <button class="text-red-600 hover:text-red-700 font-medium text-sm px-3 py-1 rounded-lg hover:bg-red-50 transition">删除</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-16">
                        <div class="text-7xl mb-6">📝</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">还没有发布内容</h3>
                        <p class="text-gray-600 mb-6">开始分享你的副业经验和 AI 使用心得吧</p>
                        <button class="gradient-bg text-white px-8 py-4 rounded-full font-bold hover:opacity-90 transition shadow-lg transform hover:scale-105">
                            ➕ 立即发布
                        </button>
                    </div>
                    @endif
                </div>
                
                {{-- 我的收藏 --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800">⭐ 我的收藏</h2>
                        <a href="{{ route('favorites.index') }}" class="text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1 transition">
                            查看全部 <span>→</span>
                        </a>
                    </div>
                    
                    @if(isset($recentFavorites) && count($recentFavorites) > 0)
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($recentFavorites as $favorite)
                        <div class="p-5 border-2 border-gray-100 rounded-xl hover:border-primary-300 hover:shadow-lg transition bg-gradient-to-br from-white to-gray-50">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="font-bold text-gray-800">{{ $favorite['title'] ?? '收藏内容' }}</h3>
                                <span class="text-xs bg-gradient-to-r from-primary-100 to-primary-200 text-primary-800 px-3 py-1 rounded-full font-medium">{{ $favorite['type'] ?? '文章' }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">收藏于 {{ $favorite['created_at'] ?? '最近' }}</p>
                            <a href="#" class="text-primary-600 hover:text-primary-700 font-medium text-sm flex items-center gap-1 transition">
                                查看详情 <span>→</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">⭐</div>
                        <p class="text-gray-600 mb-4">还没有收藏内容</p>
                        <a href="{{ route('max.home') }}" class="text-primary-600 hover:text-primary-700 font-medium transition">去逛逛 →</a>
                    </div>
                    @endif
                </div>
                
                {{-- VIP 专属推荐 --}}
                @if(!(auth()->user()->isVip ?? false))
                <div class="relative overflow-hidden bg-gradient-to-r from-purple-600 via-pink-600 to-orange-500 rounded-2xl shadow-2xl p-8 text-white">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-5xl mb-4">👑</div>
                                <h3 class="text-2xl font-bold mb-3">升级 VIP，解锁更多功能</h3>
                                <div class="grid grid-cols-3 gap-4 text-sm mb-6">
                                    <div class="bg-white/20 backdrop-blur-sm px-4 py-3 rounded-xl">
                                        <div class="font-bold mb-1">📰</div>
                                        <div>解锁全部 50+ 副业案例</div>
                                    </div>
                                    <div class="bg-white/20 backdrop-blur-sm px-4 py-3 rounded-xl">
                                        <div class="font-bold mb-1">🛠️</div>
                                        <div>解锁 20+ 工具变现地图</div>
                                    </div>
                                    <div class="bg-white/20 backdrop-blur-sm px-4 py-3 rounded-xl">
                                        <div class="font-bold mb-1">📱</div>
                                        <div>解锁 10+ 运营 SOP</div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('max.pricing') }}" class="bg-white text-purple-600 px-8 py-4 rounded-full font-bold hover:bg-gray-100 transition shadow-xl transform hover:scale-105 whitespace-nowrap">
                                💎 立即开通 VIP
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 主题切换器 --}}
@include('components.max.theme-switcher')
@endsection
