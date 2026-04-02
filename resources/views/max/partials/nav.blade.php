{{-- MAX 风格统一导航栏 --}}
<nav class="bg-white shadow-md fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <a href="/" class="flex items-center">
                <span class="text-xl font-bold gradient-bg bg-clip-text text-transparent">
                    🦀 OpenClaw 智信
                </span>
            </a>
            
            {{-- 导航链接 --}}
            <div class="hidden md:flex space-x-8">
                @if(isset($currentPage) && $currentPage === 'home')
                    <a href="#features" class="text-gray-700 hover:text-purple-600 transition">核心功能</a>
                    <a href="#cases" class="text-gray-700 hover:text-purple-600 transition">副业案例</a>
                    <a href="#pricing" class="text-gray-700 hover:text-purple-600 transition">价格方案</a>
                    <a href="#testimonials" class="text-gray-700 hover:text-purple-600 transition">用户评价</a>
                @else
                    <a href="/" class="{{ ($currentPage ?? '') === 'home' ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600' }} transition">首页</a>
                    <a href="/projects" class="{{ ($currentPage ?? '') === 'projects' ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600' }} transition">项目库</a>
                    <a href="/articles" class="{{ ($currentPage ?? '') === 'articles' ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600' }} transition">文章</a>
                    <a href="/max/cases" class="text-gray-700 hover:text-purple-600 transition">副业案例</a>
                    <a href="/max/pricing" class="{{ ($currentPage ?? '') === 'pricing' ? 'text-purple-600 font-semibold' : 'text-gray-700 hover:text-purple-600' }} transition">价格</a>
                @endif
            </div>
            
            {{-- 用户操作 --}}
            <div class="flex items-center space-x-4">
                @auth
                    <a href="/dashboard" class="text-gray-700 hover:text-purple-600">个人中心</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-purple-600">退出</button>
                    </form>
                @else
                    <a href="/login" class="text-gray-700 hover:text-purple-600">登录</a>
                    <a href="/register" class="gradient-bg text-white px-6 py-2 rounded-full hover:opacity-90 transition">免费注册</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
