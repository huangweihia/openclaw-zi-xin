<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>免费注册 - AI 副业情报局 MAX</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'register'; @endphp
    @include('max.partials.nav')

    <!-- 注册表单 -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <!-- Logo 和标题 -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold gradient-bg bg-clip-text text-transparent mb-2">
                    免费注册 AI 副业情报局
                </h1>
                <p class="text-gray-600">
                    每天 10 分钟，获取最新 AI 资讯 + 工具 + 资源
                </p>
            </div>

            <!-- 注册卡片 -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                @if(session('success'))
                    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-4">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    
                    <!-- 手机号 -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            手机号
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            required
                            pattern="^1[3-9]\d{9}$"
                            placeholder="请输入手机号"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <!-- 验证码 -->
                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            验证码
                        </label>
                        <div class="flex space-x-2">
                            <input 
                                type="text" 
                                id="code" 
                                name="code" 
                                required
                                maxlength="6"
                                placeholder="6 位验证码"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                            <button 
                                type="button" 
                                id="sendCodeBtn"
                                onclick="sendSmsCode()"
                                class="gradient-bg text-white px-6 py-3 rounded-lg hover:opacity-90 transition whitespace-nowrap"
                            >
                                获取验证码
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">验证码 5 分钟内有效</p>
                    </div>

                    <!-- 昵称（可选） -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            昵称 <span class="text-gray-400">（可选）</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            placeholder="给自己起个名字吧"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <!-- 注册按钮 -->
                    <button 
                        type="submit" 
                        class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition mb-4"
                    >
                        同意协议并注册
                    </button>

                    <!-- 分隔线 -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">其他方式</span>
                        </div>
                    </div>

                    <!-- 微信扫码 -->
                    <button 
                        type="button" 
                        class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold hover:bg-green-600 transition flex items-center justify-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.5 10.5h1v1h-1v-1zm5 0h1v1h-1v-1z"/>
                            <path d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 3 1 4.3V22l4.1-1.1C8.14 21.6 10.04 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18c-1.7 0-3.3-.4-4.8-1.1L4 20l1.1-3.2C4.4 15.5 4 13.8 4 12c0-4.4 3.6-8 8-8s8 3.6 8 8-3.6 8-8 8z"/>
                        </svg>
                        <span>微信扫码注册</span>
                    </button>
                </form>

                <!-- 登录链接 -->
                <p class="text-center mt-6 text-gray-600">
                    已有账号？
                    <a href="/login" class="text-purple-600 hover:text-purple-700 font-semibold">
                        立即登录
                    </a>
                </p>
            </div>

            <!-- 权益说明 -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 mb-4">注册即享免费权益</p>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="text-2xl mb-2">📰</div>
                        <div class="text-sm text-gray-700">每日 AI 资讯</div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="text-2xl mb-2">📚</div>
                        <div class="text-sm text-gray-700">基础教程</div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="text-2xl mb-2">📧</div>
                        <div class="text-sm text-gray-700">每周日报</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        let countdown = 0;

        async function sendSmsCode() {
            const phoneInput = document.getElementById('phone');
            const btn = document.getElementById('sendCodeBtn');
            const phone = phoneInput.value.trim();

            // 验证手机号
            if (!phone || !/^1[3-9]\d{9}$/.test(phone)) {
                alert('请输入正确的手机号');
                phoneInput.focus();
                return;
            }

            // 倒计时中
            if (countdown > 0) return;

            // 禁用按钮
            btn.disabled = true;
            btn.classList.add('opacity-50');
            btn.textContent = '发送中...';

            try {
                const response = await fetch('/register/send-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ phone }),
                });

                const result = await response.json();

                if (result.success) {
                    alert('验证码已发送，请注意查收');
                    startCountdown();
                } else {
                    alert(result.message || '发送失败，请稍后重试');
                    btn.disabled = false;
                    btn.classList.remove('opacity-50');
                    btn.textContent = '获取验证码';
                }
            } catch (error) {
                alert('发送失败，请检查网络');
                btn.disabled = false;
                btn.classList.remove('opacity-50');
                btn.textContent = '获取验证码';
            }
        }

        function startCountdown() {
            countdown = 60;
            const btn = document.getElementById('sendCodeBtn');
            
            const timer = setInterval(() => {
                if (countdown <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.classList.remove('opacity-50');
                    btn.textContent = '获取验证码';
                    countdown = 0;
                } else {
                    btn.textContent = `${countdown}秒后重试`;
                    countdown--;
                }
            }, 1000);
        }
    </script>

    @include('max.partials.footer')
</body>
</html>
