<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{{ $post->title }} - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'posts'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <!-- 头部信息 -->
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">{{ $post->type_name }}</span>
                        <span class="px-3 py-1 {{ $post->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} rounded-full text-sm">{{ $post->status === 'approved' ? '✅ 已通过' : '⏳ 审核中' }}</span>
                    </div>
                    <h1 class="text-4xl font-bold mb-4">{{ $post->title }}</h1>
                    <div class="flex items-center gap-6 text-gray-600">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-xl">👤</div>
                            <span class="font-semibold">{{ $post->user->name }}</span>
                        </div>
                        <span>📅 {{ $post->created_at->format('Y-m-d H:i') }}</span>
                        <span>👁 {{ $post->view_count }}</span>
                        <span>👍 {{ $post->like_count }}</span>
                        <span>💬 {{ $post->comment_count }}</span>
                    </div>
                </div>

                <!-- 内容 -->
                <div class="prose max-w-none mb-8">
                    {!! $post->content !!}
                </div>

                <!-- 标签 -->
                @if($post->tags)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($post->tags as $tag)
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif

                <!-- 操作按钮 -->
                @if($post->user_id === auth()->id())
                    <div class="flex gap-4 pt-6 border-t">
                        <a href="{{ route('posts.edit', $post->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold">编辑</a>
                        <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('确定删除？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 font-semibold">删除</button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- 评论区 -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold mb-6">💬 评论（{{ $post->comments->count() }}）</h3>
                
                @auth
                    <form action="#" method="POST" class="mb-8">
                        @csrf
                        <textarea name="content" rows="4" placeholder="发表评论..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"></textarea>
                        <button type="submit" class="mt-3 gradient-bg text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">发表评论</button>
                    </form>
                @else
                    <div class="bg-gray-50 rounded-lg p-6 text-center mb-8">
                        <p class="text-gray-600 mb-4">登录后才能发表评论</p>
                        <a href="{{ route('login') }}" class="gradient-bg text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition">立即登录</a>
                    </div>
                @endauth

                <div class="space-y-6">
                    @forelse($post->comments as $comment)
                        <div class="border-b pb-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-xl">👤</div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-semibold">{{ $comment->user->name }}</span>
                                        <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ $comment->content }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">暂无评论，快来抢沙发吧</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
