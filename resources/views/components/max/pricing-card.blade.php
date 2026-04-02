{{-- 价格方案卡片组件 --}}
{{-- 使用示例：@include('components.max.pricing-card', [...]) --}}

@props([
    'name' => 'VIP 会员',
    'icon' => '💎',
    'price' => '199 元',
    'period' => '/年',
    'originalPrice' => null,
    'discount' => null,
    'features' => [],
    'isPopular' => false,
    'ctaText' => '立即开通',
    'ctaRoute' => 'max.pricing',
    'ctaColor' => 'primary',
    'urgency' => null
])

<div class="bg-white rounded-2xl shadow-lg p-8 {{ $isPopular ? 'relative transform scale-105 border-4 border-' . $ctaColor . '-500' : '' }}">
    
    {{-- 热门标签 --}}
    @if($isPopular)
    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 gradient-bg text-white px-6 py-2 rounded-full text-sm font-semibold shadow-lg">
        🔥 {{ $isPopular === true ? '最受欢迎' : $isPopular }}
    </div>
    @endif
    
    {{-- 卡片头部 --}}
    <div class="text-center mb-8">
        <div class="text-5xl mb-4">{{ $icon }}</div>
        <h3 class="text-2xl font-bold mb-2">{{ $name }}</h3>
        
        <div class="flex items-center justify-center mb-2">
            <span class="text-5xl font-bold gradient-bg bg-clip-text text-transparent">{{ $price }}</span>
            <span class="text-gray-500 ml-2">{{ $period }}</span>
        </div>
        
        @if($originalPrice)
        <div class="text-gray-500 line-through">原价 {{ $originalPrice }}</div>
        @endif
        
        @if($discount)
        <div class="bg-red-50 text-red-600 px-3 py-2 rounded-lg font-semibold text-sm mt-2">
            {{ $discount }}
        </div>
        @endif
        
        @if($urgency)
        <div class="text-sm text-gray-500 mt-2">
            ⏰ {{ $urgency }}
        </div>
        @endif
    </div>
    
    {{-- 功能列表 --}}
    <ul class="space-y-4 mb-8">
        @foreach($features as $feature)
        <li class="flex items-center">
            <span class="text-green-500 mr-2 text-xl">{{ $feature['icon'] ?? '✓' }}</span>
            <span class="{{ $feature['disabled'] ?? false ? 'text-gray-400' : 'text-gray-700' }}">
                {{ $feature['text'] }}
            </span>
        </li>
        @endforeach
    </ul>
    
    {{-- CTA 按钮 --}}
    <a href="{{ route($ctaRoute) }}" class="block w-full gradient-bg text-white py-4 rounded-lg text-lg font-semibold hover:opacity-90 transition text-center mb-4">
        {{ $ctaText }}
    </a>
    
    {{-- 保障信息 --}}
    @if($isPopular)
    <div class="text-center text-sm text-gray-500 space-y-2">
        <div>✅ 7 天无理由退款</div>
        <div>✅ 1,234+ 人已加入</div>
        <div>✅ 98% 好评率</div>
    </div>
    @endif
</div>
