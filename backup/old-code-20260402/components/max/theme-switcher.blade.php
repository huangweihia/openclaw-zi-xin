{{-- 主题切换器组件 --}}
{{-- 使用示例：@include('components.max.theme-switcher') --}}

<div class="fixed bottom-20 right-4 z-50">
    {{-- 主题切换按钮 --}}
    <div class="relative" x-data="{ open: false }">
        {{-- 触发按钮 --}}
        <button 
            @click="open = !open"
            class="bg-white dark:bg-gray-800 rounded-full p-3 shadow-lg hover:shadow-xl transition border border-gray-200 dark:border-gray-700"
            title="主题设置"
        >
            <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
            </svg>
        </button>
        
        {{-- 主题面板 --}}
        <div 
            x-show="open" 
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="absolute bottom-14 right-0 bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-4 w-64 border border-gray-200 dark:border-gray-700"
        >
            <h3 class="font-bold text-gray-800 dark:text-white mb-3">🎨 主题设置</h3>
            
            {{-- 主题选择 --}}
            <div class="mb-4">
                <label class="text-sm text-gray-600 dark:text-gray-400 mb-2 block">选择主题</label>
                <div class="grid grid-cols-4 gap-2">
                    <button 
                        data-theme-switch="purple"
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 hover:scale-110 transition transform ring-2 ring-offset-2 ring-purple-500"
                        title="紫色主题"
                    ></button>
                    <button 
                        data-theme-switch="blue"
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 hover:scale-110 transition transform"
                        title="蓝色主题"
                    ></button>
                    <button 
                        data-theme-switch="green"
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 hover:scale-110 transition transform"
                        title="绿色主题"
                    ></button>
                    <button 
                        data-theme-switch="orange"
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 hover:scale-110 transition transform"
                        title="橙色主题"
                    ></button>
                </div>
            </div>
            
            {{-- 深色模式 --}}
            <div class="mb-4">
                <label class="text-sm text-gray-600 dark:text-gray-400 mb-2 block">显示模式</label>
                <button 
                    data-dark-toggle
                    class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                >
                    <span class="text-sm text-gray-700 dark:text-gray-300">深色模式</span>
                    <span class="text-xl">🌙</span>
                </button>
            </div>
            
            {{-- 字体大小 --}}
            <div>
                <label class="text-sm text-gray-600 dark:text-gray-400 mb-2 block">字体大小</label>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">小</button>
                    <button class="px-3 py-1 text-base bg-primary-500 text-white rounded">中</button>
                    <button class="px-3 py-1 text-lg bg-gray-100 dark:bg-gray-700 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition">大</button>
                </div>
            </div>
        </div>
    </div>
</div>
