<?php

/**
 * 资讯日报 / 周报 / 欢迎 / 系统通知 等基础邮件模板。
 * 由迁移 2026_03_28_150000 与 EmailTemplatePresetSeeder 共用，保证 php artisan migrate 即可初始化线上基础模版。
 */

$classicDigestHtml = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 30px; }
        .section { margin-bottom: 30px; }
        .section-title { color: #667eea; font-size: 18px; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-bottom: 15px; }
        .item { background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; border-left: 4px solid #667eea; }
        .item-title { font-weight: bold; margin-bottom: 5px; }
        .item-desc { color: #666; font-size: 14px; }
        .footer { background: #333; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🤖 AI & 副业资讯日报</h1>
        <p>{{date}}</p>
    </div>
    
    <div class="content">
        <div class="section">
            <h2 class="section-title">🔥 热门 AI 项目</h2>
            {{projects}}
        </div>
        
        <div class="section">
            <h2 class="section-title">💡 副业灵感</h2>
            {{side_hustles}}
        </div>
        
        <div class="section">
            <h2 class="section-title">📚 学习资源</h2>
            {{resources}}
        </div>
    </div>
    
    <div class="footer">
        <p>AI 副业情报局 · 用 AI 赋能副业</p>
        <p><a href="{{unsubscribe_url}}" style="color: #aaa;">退订邮件</a></p>
    </div>
</body>
</html>';

return [
    [
        'name' => '每日资讯日报',
        'key' => 'daily_digest',
        'subject' => '🤖 AI & 副业资讯日报 - {{date}}',
        'content' => $classicDigestHtml,
        'variables' => ['date', 'projects', 'side_hustles', 'resources', 'unsubscribe_url'],
        'is_active' => true,
    ],

    [
        'name' => '【经典日报】简洁风格',
        'key' => 'daily_classic',
        'subject' => '🤖 AI & 副业资讯日报 - {{date}}',
        'content' => $classicDigestHtml,
        'variables' => ['date', 'projects', 'side_hustles', 'resources', 'unsubscribe_url'],
        'is_active' => true,
    ],

    [
        'name' => '【现代日报】卡片风格',
        'key' => 'daily_modern',
        'subject' => '📬 你的 AI 副业日报已送达 - {{date}}',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background: #f1f5f9; }
        .container { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; }
        .header p { margin: 10px 0 0; opacity: 0.9; }
        .content { padding: 30px; }
        .card { background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .card-header { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; }
        .card-icon { font-size: 24px; }
        .card-title { font-size: 18px; font-weight: 600; color: #1e293b; }
        .item { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
        .item h4 { margin: 0 0 8px; color: #6366f1; }
        .item p { margin: 0; color: #64748b; font-size: 14px; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; color: #94a3b8; font-size: 13px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📬 AI 副业情报局</h1>
            <p>{{date}} · 第 {{issue_number}} 期</p>
        </div>
        
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <span class="card-icon">🔥</span>
                    <span class="card-title">热门 AI 项目</span>
                </div>
                {{projects}}
            </div>
            
            <div class="card">
                <div class="card-header">
                    <span class="card-icon">💡</span>
                    <span class="card-title">副业灵感</span>
                </div>
                {{side_hustles}}
            </div>
            
            <div class="card">
                <div class="card-header">
                    <span class="card-icon">📚</span>
                    <span class="card-title">学习资源</span>
                </div>
                {{resources}}
            </div>
        </div>
        
        <div class="footer">
            <p>感谢订阅 · <a href="{{unsubscribe_url}}" style="color: #6366f1;">退订</a></p>
        </div>
    </div>
</body>
</html>',
        'variables' => ['date', 'issue_number', 'projects', 'side_hustles', 'resources', 'unsubscribe_url'],
        'is_active' => true,
    ],

    [
        'name' => '【周报】每周精选汇总',
        'key' => 'weekly_summary',
        'subject' => '📊 本周 AI 副业精选 - {{week_range}}',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; border-radius: 10px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; }
        .header p { margin: 10px 0 0; opacity: 0.9; }
        .content { padding: 30px; }
        .section { margin-bottom: 30px; }
        .section-title { color: #11998e; font-size: 20px; border-bottom: 3px solid #38ef7d; padding-bottom: 10px; margin-bottom: 20px; }
        .top-item { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); padding: 20px; border-radius: 8px; margin-bottom: 15px; }
        .top-item h3 { margin: 0 0 10px; color: #11998e; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat { flex: 1; background: #f8f9fa; padding: 15px; text-align: center; border-radius: 8px; }
        .stat-value { font-size: 28px; font-weight: bold; color: #11998e; }
        .stat-label { font-size: 13px; color: #666; margin-top: 5px; }
        .footer { background: #333; color: white; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 每周精选汇总</h1>
            <p>{{week_range}}</p>
        </div>
        
        <div class="content">
            <div class="stats">
                <div class="stat">
                    <div class="stat-value">{{projects_count}}</div>
                    <div class="stat-label">新增项目</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{articles_count}}</div>
                    <div class="stat-label">精选文章</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{tips_count}}</div>
                    <div class="stat-label">实用技巧</div>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">🏆 Top 项目</h2>
                {{top_projects}}
            </div>
            
            <div class="section">
                <h2 class="section-title">📰 热门文章</h2>
                {{articles}}
            </div>
        </div>
        
        <div class="footer">
            <p>AI 副业情报局 · 每周精选</p>
            <p><a href="{{unsubscribe_url}}" style="color: #aaa;">退订邮件</a></p>
        </div>
    </div>
</body>
</html>',
        'variables' => ['week_range', 'projects_count', 'articles_count', 'tips_count', 'top_projects', 'articles', 'unsubscribe_url'],
        'is_active' => true,
    ],

    [
        'name' => '【欢迎邮件】新用户欢迎',
        'key' => 'welcome',
        'subject' => '🎉 欢迎加入 AI 副业情报局！',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; background: #f1f5f9; }
        .container { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 50px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 32px; }
        .content { padding: 40px 30px; }
        .welcome-box { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 25px; border-radius: 12px; margin: 25px 0; text-align: center; }
        .welcome-box h2 { margin: 0 0 15px; color: #92400e; }
        .feature-list { list-style: none; padding: 0; }
        .feature-list li { padding: 12px 0; border-bottom: 1px solid #e2e8f0; }
        .feature-list li:before { content: "✅ "; margin-right: 8px; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; text-decoration: none; padding: 16px 40px; border-radius: 12px; font-weight: 600; margin: 20px 0; }
        .footer { background: #f8fafc; padding: 25px; text-align: center; color: #64748b; font-size: 13px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 欢迎加入！</h1>
            <p style="margin: 10px 0 0; opacity: 0.9;">你好，{{name}}！</p>
        </div>
        
        <div class="content">
            <p>感谢你加入 AI 副业情报局！从今天开始，你将每天收到精心挑选的 AI 项目、副业灵感和学习资源。</p>
            
            <div class="welcome-box">
                <h2>📬 你将收到什么？</h2>
                <ul class="feature-list" style="text-align: left; margin: 0;">
                    <li><strong>每日资讯日报</strong> - 每天早上 10:00，10+ 个热门 AI 项目</li>
                    <li><strong>每周精选汇总</strong> - 每周一，上周最优质内容</li>
                    <li><strong>系统通知</strong> - 账户相关的重要通知</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{dashboard_url}}" class="cta-button">🚀 开始探索</a>
            </div>
            
            <p style="margin-top: 25px; color: #64748b;">如果有任何问题或建议，欢迎随时联系我们。</p>
            <p>祝你副业顺利！<br><strong>AI 副业情报局 团队</strong></p>
        </div>
        
        <div class="footer">
            <p>此邮件发送至：{{email}}</p>
            <p><a href="{{unsubscribe_url}}" style="color: #6366f1;">退订所有邮件</a> | <a href="{{preferences_url}}" style="color: #6366f1;">管理订阅偏好</a></p>
            <p style="margin-top: 15px;">© 2024 AI 副业情报局。All rights reserved.</p>
        </div>
    </div>
</body>
</html>',
        'variables' => ['name', 'email', 'dashboard_url', 'unsubscribe_url', 'preferences_url'],
        'is_active' => true,
    ],

    [
        'name' => '【通知】系统通知',
        'key' => 'notification',
        'subject' => '🔔 {{notification_title}}',
        'content' => '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { padding: 30px; text-align: center; border-bottom: 3px solid #f59e0b; }
        .header-icon { font-size: 48px; margin-bottom: 10px; }
        .content { padding: 30px; }
        .notice-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .button { display: inline-block; background: #f59e0b; color: white; text-decoration: none; padding: 12px 30px; border-radius: 5px; margin: 15px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">🔔</div>
            <h2 style="margin: 0;">{{notification_title}}</h2>
        </div>
        
        <div class="content">
            <p>你好，{{name}}！</p>
            
            <div class="notice-box">
                {{notification_content}}
            </div>
            
            {{action_button}}
            
            <p style="color: #666; font-size: 14px;">如果这不是你本人的操作，请及时联系我们。</p>
        </div>
        
        <div class="footer">
            <p>AI 副业情报局 · 系统通知</p>
            <p>此邮件为系统自动发送，请勿回复</p>
        </div>
    </div>
</body>
</html>',
        'variables' => ['notification_title', 'name', 'notification_content', 'action_button'],
        'is_active' => true,
    ],
];
