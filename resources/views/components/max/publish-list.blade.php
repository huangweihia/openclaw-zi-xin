{{-- 发布管理列表组件 --}}
{{-- 用于：我的发布、待审核内容等场景 --}}
{{-- 使用示例：@include('components.max.publish-list', ['items' => $posts, 'type' => 'my-posts']) --}}

@props([
    'items' => [],
    'type' => 'my-posts', // my-posts/pending/all
    'emptyMessage' => '还没有发布内容',
    'showActions' => true,
    'showStats' => true
])

<div class="bg-white rounded-xl shadow-lg p-6">
    {{-- 头部 --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold">
            @if($type === 'my-posts')
                📝 我的发布
            @elseif($type === 'pending')
                ⏳ 待审核
            @else
                📋 全部内容
            @endif
        </h2>
        
        @if($showActions)
        <div class="flex items-center gap-2">
            <button class="text-primary-600 hover:text-primary-700 text-sm font-semibold">
                ➕ 发布新内容
            </button>
            @if(count($items) > 0)
            <button class="text-red-600 hover:text-red-700 text-sm font-semibold">
                🗑️ 批量删除
            </button>
            @endif
        </div>
        @endif
    </div>
    
    {{-- 内容列表 --}}
    @if(count($items) > 0)
    <div class="space-y-4">
        @foreach($items as $item)
        <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:shadow-md transition">
            {{-- 复选框 --}}
            @if($showActions)
            <input type="checkbox" class="mt-2 w-4 h-4 text-primary-600 rounded" value="{{ $item['id'] ?? '' }}">
            @endif
            
            {{-- 内容 --}}
            <div class="flex-1">
                {{-- 状态标签 --}}
                <div class="flex items-center gap-2 mb-2">
                    @if($item['status'] === 'pending')
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">⏳ 审核中</span>
                    @elseif($item['status'] === 'approved')
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">✅ 已通过</span>
                    @elseif($item['status'] === 'rejected')
                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">❌ 已拒绝</span>
                    @endif
                    
                    @if($item['is_vip'] ?? false)
                    <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">👑 VIP</span>
                    @endif
                    
                    <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">
                        {{ $item['type_label'] ?? '文章' }}
                    </span>
                </div>
                
                {{-- 标题 --}}
                <h3 class="font-semibold mb-1">
                    @if($item['url'] ?? false)
                    <a href="{{ $item['url'] }}" class="hover:text-primary-600 transition">
                        {{ $item['title'] ?? '未命名' }}
                    </a>
                    @else
                    {{ $item['title'] ?? '未命名' }}
                    @endif
                </h3>
                
                {{-- 摘要 --}}
                <p class="text-sm text-gray-600 mb-2">
                    {{ Str::limit($item['summary'] ?? '', 120) }}
                </p>
                
                {{-- 统计信息 --}}
                @if($showStats)
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span>📅 {{ $item['created_at'] ?? '刚刚' }}</span>
                    <span>👁️ {{ $item['views'] ?? 0 }}</span>
                    <span>👍 {{ $item['likes'] ?? 0 }}</span>
                    <span>💬 {{ $item['comments'] ?? 0 }}</span>
                </div>
                @endif
                
                {{-- 审核备注 --}}
                @if($item['status'] === 'rejected' && ($item['audit_note'] ?? false))
                <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                    ⚠️ 审核不通过：{{ $item['audit_note'] }}
                </div>
                @endif
            </div>
            
            {{-- 操作按钮 --}}
            @if($showActions)
            <div class="flex flex-col gap-2">
                @if($item['status'] === 'approved')
                <a href="{{ $item['url'] ?? '#' }}" class="text-primary-600 hover:text-primary-700 text-sm" title="查看">
                    👁️
                </a>
                <a href="#" class="text-blue-600 hover:text-blue-700 text-sm" title="编辑">
                    ✏️
                </a>
                @endif
                <button class="text-red-600 hover:text-red-700 text-sm" title="删除">
                    🗑️
                </button>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    {{-- 分页 --}}
    @if($showActions)
    <div class="mt-8 flex justify-center">
        <nav class="flex items-center gap-2">
            <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">上一页</a>
            <a href="#" class="px-4 py-2 gradient-bg text-white rounded-lg">1</a>
            <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">2</a>
            <span class="px-2 text-gray-500">...</span>
            <a href="#" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">下一页</a>
        </nav>
    </div>
    @endif
    
    @else
    {{-- 空状态 --}}
    <div class="text-center py-16">
        <div class="text-8xl mb-6">📝</div>
        <h3 class="text-2xl font-bold text-gray-800 mb-4">{{ $emptyMessage }}</h3>
        <p class="text-gray-600 mb-8">开始分享你的副业经验和 AI 使用心得吧</p>
        <button class="gradient-bg text-white px-8 py-4 rounded-full font-semibold hover:opacity-90 transition">
            ➕ 立即发布
        </button>
    </div>
    @endif
</div>
