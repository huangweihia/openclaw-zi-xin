<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>我的订阅 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'subscriptions'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-7xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-8">我的订阅</h1>

            <!-- 当前订阅 -->
            @php
                $activeSubscription = $subscriptions->where('status', 'active')->first();
            @endphp

            @if($activeSubscription)
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="text-sm opacity-80 mb-2">当前订阅</div>
                            <div class="text-3xl font-bold mb-2">{{ $activeSubscription->plan_name ?? 'VIP 会员' }}</div>
                            <div class="text-sm opacity-80">
                                @if($activeSubscription->auto_renew)
                                    ✅ 自动续费已开启
                                @else
                                    ⚠️ 自动续费已关闭
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">{{ $activeSubscription->expires_at->diffInDays(now()) }} 天</div>
                            <div class="text-sm opacity-80">到期</div>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <form action="{{ route('subscriptions.renew', $activeSubscription->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                                💳 立即续费
                            </button>
                        </form>
                        <form action="{{ route('subscriptions.toggle-auto-renew', $activeSubscription->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-white/20 text-white px-6 py-3 rounded-lg font-semibold hover:bg-white/30 transition">
                                {{ $activeSubscription->auto_renew ? '关闭自动续费' : '开启自动续费' }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 text-center">
                    <div class="text-6xl mb-4">📭</div>
                    <h3 class="text-2xl font-bold mb-4">暂无活跃订阅</h3>
                    <p class="text-gray-600 mb-6">开通 VIP 解锁全部内容</p>
                    <a href="{{ route('max.pricing') }}" class="gradient-bg text-white px-8 py-4 rounded-lg font-semibold hover:opacity-90 transition">
                        查看价格方案
                    </a>
                </div>
            @endif

            <!-- 订阅历史 -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold mb-6">订阅历史</h3>
                <div class="space-y-4">
                    @forelse($subscriptions as $subscription)
                        <div class="border rounded-xl p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-lg">{{ $subscription->plan_name ?? 'VIP 会员' }}</div>
                                    <div class="text-sm text-gray-500">{{ $subscription->created_at->format('Y-m-d') }} - {{ $subscription->expires_at?->format('Y-m-d') }}</div>
                                </div>
                                <div>
                                    <span class="px-3 py-1 {{ $subscription->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} rounded-full text-sm">
                                        {{ $subscription->status === 'active' ? '活跃' : '已结束' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">暂无订阅记录</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
