<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>绑定企业微信 - AI 副业情报局 MAX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <!-- 标题 -->
            <div class="text-center mb-8">
                <div class="text-6xl mb-4">📱</div>
                <h1 class="text-3xl font-bold gradient-bg bg-clip-text text-transparent mb-2">
                    绑定企业微信
                </h1>
                <p class="text-gray-600">
                    接收每日 AI 资讯推送，不错过任何赚钱机会
                </p>
            </div>

            <!-- 绑定卡片 -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <!-- 好处列表 -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">绑定后可享受：</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 text-xl">✓</span>
                            <span class="text-gray-700">
                                <strong>每日 AI 资讯</strong>
                                <br>
                                <span class="text-sm text-gray-500">每天上午 9 点自动推送，包含热门项目、新案例、新工具</span>
                            </span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 text-xl">✓</span>
                            <span class="text-gray-700">
                                <strong>新内容上架通知</strong>
                                <br>
                                <span class="text-sm text-gray-500">VIP 内容更新第一时间通知</span>
                            </span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 text-xl">✓</span>
                            <span class="text-gray-700">
                                <strong>SVIP 定制报告</strong>
                                <br>
                                <span class="text-sm text-gray-500">每周自动推送竞品分析/数据采集报告</span>
                            </span>
                        </li>
                    </ul>
                </div>

                <!-- 二维码 -->
                <div class="text-center mb-6">
                    <div class="bg-gray-100 rounded-xl p-6 mb-4">
                        <!-- 这里放企业微信二维码 -->
                        <img 
                            src="https://work.weixin.qq.com/help/enterprise/qrcode.png" 
                            alt="企业微信二维码"
                            class="w-48 h-48 mx-auto"
                        >
                        <p class="text-sm text-gray-500 mt-2">
                            扫码下载企业微信并加入
                        </p>
                    </div>
                    <p class="text-xs text-gray-400">
                        💡 已有企业微信？直接扫码加入即可
                    </p>
                </div>

                <!-- 操作按钮 -->
                <div class="space-y-3">
                    <button 
                        onclick="markAsBound()"
                        class="w-full gradient-bg text-white py-3 rounded-lg font-semibold hover:opacity-90 transition"
                    >
                        ✅ 我已扫码加入
                    </button>
                    
                    <button 
                        onclick="skipBind()"
                        class="w-full border-2 border-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-50 transition"
                    >
                        ⏭️ 稍后绑定
                    </button>
                </div>

                <!-- 提示 -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <span class="text-blue-500 mr-2 text-xl">💡</span>
                        <div class="text-sm text-blue-800">
                            <strong>温馨提示：</strong>
                            <br>
                            企业微信是腾讯官方产品，完全免费。
                            <br>
                            绑定后随时可以解绑，无强制消费。
                        </div>
                    </div>
                </div>
            </div>

            <!-- 底部链接 -->
            <p class="text-center mt-6 text-gray-600">
                <a href="/max" class="text-purple-600 hover:text-purple-700 font-semibold">
                    跳过，直接进入首页
                </a>
            </p>
        </div>
    </div>

    <script>
        function markAsBound() {
            // 调用 API 标记为已绑定
            fetch('/auth/bind-enterprise-wechat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('绑定成功！开始接收推送消息');
                    window.location.href = '/max';
                } else {
                    alert('绑定失败，请重试');
                }
            })
            .catch(err => {
                console.error(err);
                alert('绑定失败，请检查网络');
            });
        }

        function skipBind() {
            // 跳过绑定，直接去首页
            window.location.href = '/max?bind_later=1';
        }
    </script>
</body>
</html>
