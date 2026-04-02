<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>发布内容 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'posts'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-8">发布内容</h1>

            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form action="{{ route('posts.store') }}" method="POST">
                    @csrf

                    <!-- 发布类型 -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">发布类型 *</label>
                        <div class="grid grid-cols-5 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="case" class="peer sr-only" required>
                                <div class="p-4 text-center border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                    <div class="text-2xl mb-2">💰</div>
                                    <div class="text-sm font-semibold">副业案例</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="tool" class="peer sr-only" required>
                                <div class="p-4 text-center border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                    <div class="text-2xl mb-2">🛠️</div>
                                    <div class="text-sm font-semibold">工具推荐</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="experience" class="peer sr-only" required>
                                <div class="p-4 text-center border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                    <div class="text-2xl mb-2">💡</div>
                                    <div class="text-sm font-semibold">经验心得</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="resource" class="peer sr-only" required>
                                <div class="p-4 text-center border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                    <div class="text-2xl mb-2">📦</div>
                                    <div class="text-sm font-semibold">资源分享</div>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="question" class="peer sr-only" required>
                                <div class="p-4 text-center border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                    <div class="text-2xl mb-2">❓</div>
                                    <div class="text-sm font-semibold">问答求助</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 标题 -->
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">标题 *</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required maxlength="100" placeholder="请输入标题（100 字以内）" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- 分类 -->
                    <div class="mb-6">
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">分类</label>
                        <input type="text" id="category" name="category" value="{{ old('category') }}" placeholder="例如：电商/内容/服务" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- 内容 -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">内容 *</label>
                        <textarea id="content" name="content" rows="15" required minlength="200" placeholder="请详细描述（最少 200 字）..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">{{ old('content') }}</textarea>
                        <p class="text-sm text-gray-500 mt-2">支持 Markdown 格式</p>
                    </div>

                    <!-- 标签 -->
                    <div class="mb-6">
                        <label for="tags" class="block text-sm font-semibold text-gray-700 mb-2">标签</label>
                        <input type="text" id="tags" name="tags" value="{{ old('tags') }}" placeholder="用逗号分隔，例如：AI，副业，变现" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- 可见性 -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">可见性 *</label>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="visibility" value="public" {{ old('visibility') === 'public' ? 'checked' : '' }} required class="w-4 h-4 text-purple-600">
                                <div>
                                    <div class="font-semibold">公开</div>
                                    <div class="text-sm text-gray-500">所有人可见</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="visibility" value="vip" {{ old('visibility') === 'vip' ? 'checked' : '' }} required class="w-4 h-4 text-purple-600">
                                <div>
                                    <div class="font-semibold">VIP 专属</div>
                                    <div class="text-sm text-gray-500">仅 VIP 会员可见</div>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="visibility" value="private" {{ old('visibility') === 'private' ? 'checked' : '' }} required class="w-4 h-4 text-purple-600">
                                <div>
                                    <div class="font-semibold">私密</div>
                                    <div class="text-sm text-gray-500">仅自己可见</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 提交按钮 -->
                    <div class="flex items-center gap-4">
                        <button type="submit" class="gradient-bg text-white px-8 py-4 rounded-lg font-semibold hover:opacity-90 transition">
                            提交审核
                        </button>
                        <p class="text-sm text-gray-500">提交后将在 24 小时内审核，审核通过后展示</p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
