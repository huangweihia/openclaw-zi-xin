<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - AI 副业情报局 MAX</title>
    <meta name="description" content="登录你的 AI 副业情报局账号，发现 AI 副业机会">
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'login'; @endphp
    @include('max.partials.nav')

    <!-- 登录表单 -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- 欢迎卡片 -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <div class="text-6xl mb-4">👋</div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">欢迎回来</h1>
                    <p class="text-gray-600">登录你的 AI 副业情报局账号</p>
                </div>

                <!-- 错误提示 -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- 登录表单 -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    @if(request()->filled('redirect'))
                        <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                    @endif

                    <!-- 邮箱 -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            邮箱地址
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            placeholder="your@email.com"
                            class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition"
                        >
                    </div>

                    <!-- 密码 -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            密码
                        </label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="••••••••"
                            class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition"
                        >
                    </div>

                    <!-- 记住我 & 忘记密码 -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                            >
                            <span class="ml-2 text-sm text-gray-600">记住我</span>
                        </label>
                        <a href="#" class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                            忘记密码？
                        </a>
                    </div>

                    <!-- 登录按钮 -->
                    <button 
                        type="submit" 
                        class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition shadow-lg"
                    >
                        登录
                    </button>
                </form>

                <!-- 注册链接 -->
                <div class="text-center mt-6 pt-6 border-t border-gray-200">
                    <p class="text-gray-600">
                        还没有账号？
                        <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-700 font-semibold">
                            立即注册
                        </a>
                    </p>
                </div>
            </div>

            <!-- 返回首页 -->
            <div class="text-center mt-6">
                <a href="/" class="text-gray-600 hover:text-gray-800 text-sm">
                    ← 返回首页
                </a>
            </div>
        </div>
    </div>

    @include('max.partials.footer')
</body>
</html>
