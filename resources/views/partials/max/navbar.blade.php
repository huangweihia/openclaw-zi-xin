{{-- MAX 版本导航栏 --}}
<nav class="bg-white shadow-md fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ route('max.home') }}" class="text-2xl font-bold gradient-bg bg-clip-text text-transparent">
                    🤖 AI 副业情报局 MAX
                </a>
            </div>
            
            {{-- 导航链接（桌面端） --}}
            <div class="hidden md:flex space-x-8">
                <a href="{{ route('max.home') }}" class="text-gray-700 hover:text-primary-600 transition">首页</a>
                <a href="{{ route('max.home') }}#cases" class="text-gray-700 hover:text-primary-600 transition">副业案例</a>
                <a href="{{ route('max.vip') }}" class="text-gray-700 hover:text-primary-600 transition">VIP 会员</a>
                <a href="{{ route('max.pricing') }}" class="text-gray-700 hover:text-primary-600 transition">价格方案</a>
            </div>
            
            {{-- 用户操作 --}}
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary-600">控制台</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-primary-600">退出</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600">登录</a>
                    <a href="{{ route('register') }}" class="gradient-bg text-white px-6 py-2 rounded-full hover:opacity-90 transition">
                        免费注册
                    </a>
                @endauth
                
                {{-- 主题切换按钮 --}}
                <button 
                    data-dark-toggle
                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                    title="切换深色模式"
                >
                    <span class="text-xl">🌙</span>
                </button>
            </div>
        </div>
    </div>
</nav>

{{-- 主题切换器组件 --}}
@include('components.max.theme-switcher')
