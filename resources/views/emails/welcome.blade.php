<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: white;
            font-size: 28px;
            margin: 0 0 10px 0;
        }
        .header p {
            color: rgba(255,255,255,0.9);
            font-size: 16px;
            margin: 0;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #1e293b;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .content p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 16px;
        }
        .features {
            background: #f8fafc;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .features h3 {
            color: #1e293b;
            font-size: 18px;
            margin: 0 0 16px 0;
        }
        .features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .features li {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }
        .features li:last-child {
            border-bottom: none;
        }
        .features li:before {
            content: '✅ ';
            margin-right: 8px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }
        .footer {
            background: #f8fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            color: #94a3b8;
            font-size: 14px;
            margin: 8px 0;
        }
        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 欢迎加入 AI 副业情报局！</h1>
            <p>你好，{{ $user->name }}！</p>
        </div>
        
        <div class="content">
            <h2>很高兴遇见你！👋</h2>
            <p>
                感谢你加入 AI 副业情报局！从今天开始，你将每天收到精心挑选的 AI 项目、副业灵感和学习资源。
            </p>
            <p>
                我们相信，每个人都能找到适合自己的副业方向。我们的使命就是帮你发现那些被低估的机会，让你少走弯路，快速起步。
            </p>

            <div class="features">
                <h3>📬 你将收到什么？</h3>
                <ul>
                    <li><strong>每日资讯日报</strong> - 每天早上 10:00，包含 10+ 个热门 AI 项目、5+ 个副业灵感、4+ 个学习资源</li>
                    <li><strong>每周精选汇总</strong> - 每周一发送，汇总上周最优质的内容</li>
                    <li><strong>系统通知</strong> - 账户相关的重要通知</li>
                </ul>
            </div>

            <p>
                你可以随时在个人中心管理订阅偏好，或者点击邮件底部的退订链接取消订阅。
            </p>

            <div style="text-align: center;">
                <a href="{{ url('/dashboard') }}" class="cta-button">
                    🚀 开始探索
                </a>
            </div>

            <p style="margin-top: 24px;">
                如果有任何问题或建议，欢迎随时联系我们。
            </p>
            <p>
                祝你副业顺利！<br>
                <strong>AI 副业情报局 团队</strong>
            </p>
        </div>

        <div class="footer">
            <p>
                此邮件发送至：{{ $user->email }}
            </p>
            <p>
                <a href="{{ $subscription->getUnsubscribeUrl() }}">退订所有邮件</a> | 
                <a href="{{ url('/subscriptions/preferences') }}">管理订阅偏好</a>
            </p>
            <p style="margin-top: 16px; font-size: 13px;">
                © 2024 AI 副业情报局。All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
