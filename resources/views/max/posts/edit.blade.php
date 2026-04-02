<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>编辑发布 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'posts'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-8">编辑发布</h1>

            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form action="{{ route('posts.update', $post->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">发布类型</label>
                        <div class="text-lg font-semibold">{{ $post->type_name }}</div>
                    </div>

                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">标题 *</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required maxlength="100" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>

                    <div class="mb-6">
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">分类</label>
                        <input type="text" id="category" name="category" value="{{ old('category', $post->category) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>

                    <div class="mb-6">
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">内容 *</label>
                        <textarea id="content" name="content" rows="15" required minlength="200" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">{{ old('content', $post->content) }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="tags" class="block text-sm font-semibold text-gray-700 mb-2">标签</label>
                        <input type="text" id="tags" name="tags" value="{{ old('tags', is_array($post->tags) ? implode(',', $post->tags) : '') }}" placeholder="用逗号分隔" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">可见性 *</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="visibility" value="public" {{ old('visibility', $post->visibility) === 'public' ? 'checked' : '' }} required class="w-4 h-4 text-purple-600">
                                <div><div class="font-semibold">公开</div><div class="text-sm text-gray-500">所有人可见</div></div>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="visibility" value="vip" {{ old('visibility', $post->visibility) === 'vip' ? 'checked' : '' }} required class="w-4 h-4 text-purple-600">
                                <div><div class="font-semibold">VIP 专属</div><div class="text-sm text-gray-500">仅 VIP 会员可见</div></div>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="visibility" value="private" {{ old('visibility', $post->visibility) === 'private' ? 'checked' : '' }} required class="w-4 h-4 text-purple-600">
                                <div><div class="font-semibold">私密</div><div class="text-sm text-gray-500">仅自己可见</div></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="gradient-bg text-white px-8 py-4 rounded-lg font-semibold hover:opacity-90 transition">
                            更新并提交审核
                        </button>
                        <p class="text-sm text-gray-500">更新后需要重新审核</p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
