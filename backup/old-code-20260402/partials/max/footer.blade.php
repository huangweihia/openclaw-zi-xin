{{-- MAX 版本页脚 --}}
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            {{-- 品牌信息 --}}
            <div>
                <h3 class="text-xl font-bold mb-4">🤖 AI 副业情报局 MAX</h3>
                <p class="text-gray-400">用 AI 搞副业，30 天多赚 5000+</p>
                <p class="text-gray-400 text-sm mt-2">已帮助 1,234 人找到副业方向</p>
            </div>
            
            {{-- 快速链接 --}}
            <div>
                <h4 class="font-semibold mb-4">快速链接</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('max.home') }}" class="hover:text-white">首页</a></li>
                    <li><a href="{{ route('max.vip') }}" class="hover:text-white">VIP 会员</a></li>
                    <li><a href="{{ route('max.pricing') }}" class="hover:text-white">价格方案</a></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-white">AI 文章</a></li>
                </ul>
            </div>
            
            {{-- 法律条款 --}}
            <div>
                <h4 class="font-semibold mb-4">法律条款</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('privacy') }}" class="hover:text-white">隐私政策</a></li>
                    <li><a href="#" class="hover:text-white">服务条款</a></li>
                    <li><a href="#" class="hover:text-white">退款政策</a></li>
                </ul>
            </div>
            
            {{-- 联系我们 --}}
            <div>
                <h4 class="font-semibold mb-4">联系我们</h4>
                <ul class="space-y-2 text-gray-400">
                    <li>📧 support@aifyqbj.com</li>
                    <li>💬 微信：aifyqbj</li>
                    <li class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white">知乎</a>
                        <a href="#" class="text-gray-400 hover:text-white">掘金</a>
                        <a href="#" class="text-gray-400 hover:text-white">GitHub</a>
                    </li>
                </ul>
            </div>
        </div>
        
        {{-- 版权信息 --}}
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} AI 副业情报局。All rights reserved.</p>
            <p class="text-sm mt-2">Powered by OpenClaw + Laravel</p>
        </div>
    </div>
</footer>
