{{-- 文章/内容卡片组件 --}}
{{-- 使用示例：@include('components.max.content-card', [...]) --}}

@props([
    'type' => 'article', // article/project/case
    'title' => '标题',
    'summary' => '摘要...',
    'coverImage' => null,
    'tags' => [],
    'meta' => [],
    'isVip' => false,
    'route' => '#'
])

<div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
    {{-- 封面图 --}}
    @if($coverImage)
    <a href="{{ $route }}" class="block relative h-48 overflow-hidden">
        <img src="{{ $coverImage }}" alt="{{ $title }}" class="w-full h-full object-cover">
        @if($isVip)
        <div class="absolute top-2 right-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
            👑 VIP
        </div>
        @endif
    </a>
    @endif
    
    <div class="p-6">
        {{-- 类型标签 --}}
        <div class="flex items-center gap-2 mb-3">
            @if($type === 'article')
            <span class="text-xs text-blue-600 font-semibold">📰 文章</span>
            @elseif($type === 'project')
            <span class="text-xs text-purple-600 font-semibold">🚀 项目</span>
            @elseif($type === 'case')
            <span class="text-xs text-green-600 font-semibold">💰 案例</span>
            @endif
            
            @if($isVip)
            <span class="text-xs text-orange-600 font-semibold">🔒 VIP 专属</span>
            @endif
        </div>
        
        {{-- 标题 --}}
        <a href="{{ $route }}" class="block">
            <h3 class="text-xl font-bold mb-2 hover:text-primary-600 transition line-clamp-2">
                {{ $title }}
            </h3>
        </a>
        
        {{-- 摘要 --}}
        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
            {{ $summary }}
        </p>
        
        {{-- 标签 --}}
        @if(count($tags) > 0)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach(array_slice($tags, 0, 3) as $tag)
            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                {{ $tag }}
            </span>
            @endforeach
        </div>
        @endif
        
        {{-- 元信息 --}}
        <div class="flex items-center justify-between text-xs text-gray-500">
            @foreach($meta as $key => $value)
            <span>{{ $value }}</span>
            @endforeach
        </div>
    </div>
</div>
