{{-- VIP 内容锁定卡片组件 --}}
{{-- 使用示例：@include('components.max.vip-lock-card', [...]) --}}

@props([
    'category' => '副业案例',
    'categoryColor' => 'purple',
    'title' => '案例标题',
    'summary' => '案例摘要...',
    'tags' => [],
    'locked' => true,
    'route' => '#'
])

<div class="border-2 border-gray-200 rounded-2xl overflow-hidden card-hover relative">
    {{-- 锁定标识 --}}
    @if($locked)
    <div class="absolute top-4 right-4 text-4xl lock-icon" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
        🔒
    </div>
    @endif
    
    <div class="p-8">
        {{-- 分类标签 --}}
        <div class="text-sm text-{{ $categoryColor }}-600 font-semibold mb-2">
            {{ $category }}
        </div>
        
        {{-- 标题 --}}
        <h3 class="text-2xl font-bold mb-4">{{ $title }}</h3>
        
        {{-- 摘要 --}}
        <p class="text-gray-600 mb-4">{{ $summary }}</p>
        
        {{-- 标签 --}}
        @if(count($tags) > 0)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($tags as $tag)
            <span class="bg-{{ $tag['color'] ?? 'blue' }}-100 text-{{ $tag['color'] ?? 'blue' }}-800 px-3 py-1 rounded-full text-sm">
                {{ $tag['label'] }}
            </span>
            @endforeach
        </div>
        @endif
        
        {{-- VIP 解锁提示 --}}
        @if($locked)
        <div class="border-t pt-4">
            <div class="text-sm text-gray-500 mb-2">🔓 开通 VIP 解锁：</div>
            <ul class="text-sm text-gray-700 space-y-1">
                {{ $slot }}
            </ul>
        </div>
        
        {{-- 行动按钮 --}}
        <a href="{{ $route }}" class="block w-full gradient-bg text-white text-center py-3 rounded-lg mt-4 hover:opacity-90 transition">
            开通 VIP 查看完整内容
        </a>
        @else
        {{-- 已解锁内容 --}}
        <a href="{{ $route }}" class="block w-full border-2 border-primary-600 text-primary-600 text-center py-3 rounded-lg mt-4 hover:bg-primary-50 transition">
            查看详情
        </a>
        @endif
    </div>
</div>
