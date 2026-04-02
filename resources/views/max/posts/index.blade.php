<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>我的发布 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'posts'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-4xl font-bold">我的发布</h1>
                <a href="{{ route('posts.create') }}" class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    ✍️ 发布内容
                </a>
            </div>

            <!-- 筛选栏 -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <form action="{{ route('posts.index') }}" method="GET" class="flex gap-4">
                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">全部类型</option>
                        <option value="case" {{ request('type') === 'case' ? 'selected' : '' }}>副业案例</option>
                        <option value="tool" {{ request('type') === 'tool' ? 'selected' : '' }}>工具推荐</option>
                        <option value="experience" {{ request('type') === 'experience' ? 'selected' : '' }}>经验心得</option>
                        <option value="resource" {{ request('type') === 'resource' ? 'selected' : '' }}>资源分享</option>
                        <option value="question" {{ request('type') === 'question' ? 'selected' : '' }}>问答求助</option>
                    </select>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">全部状态</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>审核中</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>已通过</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>已拒绝</option>
                    </select>
                    <button type="submit" class="gradient-bg text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">筛选</button>
                </form>
            </div>

            <!-- 发布列表 -->
            <div class="space-y-4">
                @forelse($posts as $post)
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">{{ $post->type_name }}</span>
                                    <span class="px-3 py-1 {{ $post->status === 'approved' ? 'bg-green-100 text-green-700' : ($post->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }} rounded-full text-sm font-semibold">
                                        {{ $post->status === 'approved' ? '✅ 已通过' : ($post->status === 'rejected' ? '❌ 已拒绝' : '⏳ 审核中') }}
                                    </span>
                                    <span class="px-3 py-1 {{ $post->visibility === 'public' ? 'bg-blue-100 text-blue-700' : ($post->visibility === 'vip' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700') }} rounded-full text-sm">
                                        {{ $post->visibility === 'public' ? '公开' : ($post->visibility === 'vip' ? 'VIP 专属' : '私密') }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold mb-2">{{ $post->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($post->content, 150) }}</p>
                                <div class="flex items-center gap-6 text-sm text-gray-500">
                                    <span>👁 {{ $post->view_count }}</span>
                                    <span>👍 {{ $post->like_count }}</span>
                                    <span>💬 {{ $post->comment_count }}</span>
                                    <span>📅 {{ $post->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('posts.show', $post->id) }}" class="text-purple-600 hover:text-purple-700 font-semibold">查看详情</a>
                                <a href="{{ route('posts.edit', $post->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold">编辑</a>
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('确定删除？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-semibold">删除</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-gray-600 text-lg mb-6">暂无发布</p>
                        <a href="{{ route('posts.create') }}" class="gradient-bg text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                            发布第一个内容
                        </a>
                    </div>
                @endforelse
            </div>

            @if($posts->hasPages())
                <div class="mt-12">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
