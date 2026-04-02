@extends('layouts.app')

@section('title', '关于我们 - AI 副业情报局')

@section('content')
<section style="padding: 80px 0;">
    <div class="container" style="max-width: 800px;">
        <h1 style="font-size: 42px; margin-bottom: 30px; text-align: center;">关于我们</h1>
        
        <div class="card" style="padding: 40px; margin-bottom: 40px;">
            <h2 style="font-size: 28px; margin-bottom: 20px;">🤖 AI 副业情报局是什么？</h2>
            <p style="color: var(--gray-light); line-height: 2; font-size: 16px; margin-bottom: 20px;">
                AI 副业情报局成立于 2024 年，是一个专注于 AI 副业变现的社区平台。我们的使命是帮助普通人利用 AI 技术找到适合自己的副业方向，实现收入增长。
            </p>
            <p style="color: var(--gray-light); line-height: 2; font-size: 16px;">
                每天，我们会为你精选最新的 AI 项目、变现灵感和学习资源，让你不错过任何一个机会。
            </p>
        </div>

        <div class="card" style="padding: 40px; margin-bottom: 40px;">
            <h2 style="font-size: 28px; margin-bottom: 20px;">🎯 我们的愿景</h2>
            <p style="color: var(--gray-light); line-height: 2; font-size: 16px;">
                让每个人都能享受到 AI 技术带来的红利，用 AI 赋能副业，让赚钱变得更简单。
            </p>
        </div>

        <div class="card" style="padding: 40px; margin-bottom: 40px;">
            <h2 style="font-size: 28px; margin-bottom: 20px;">📊 我们的成绩</h2>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; text-align: center; margin-top: 30px;">
                <div>
                    <div style="font-size: 48px; font-weight: 800; color: var(--primary);">10000+</div>
                    <div style="color: var(--gray-light); margin-top: 8px;">订阅用户</div>
                </div>
                <div>
                    <div style="font-size: 48px; font-weight: 800; color: var(--primary);">500+</div>
                    <div style="color: var(--gray-light); margin-top: 8px;">精选项目</div>
                </div>
                <div>
                    <div style="font-size: 48px; font-weight: 800; color: var(--primary);">1000+</div>
                    <div style="color: var(--gray-light); margin-top: 8px;">深度文章</div>
                </div>
            </div>
        </div>

        <div class="card" style="padding: 40px;">
            <h2 style="font-size: 28px; margin-bottom: 20px;">📬 联系我们</h2>
            <p style="color: var(--gray-light); line-height: 2; font-size: 16px; margin-bottom: 20px;">
                如果你有任何问题、建议或合作意向，欢迎通过以下方式联系我们：
            </p>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <strong>邮箱：</strong>contact@example.com
                </li>
                <li style="padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <strong>微信：</strong>ai_side_hustle
                </li>
                <li style="padding: 12px 0;">
                    <strong>工作时间：</strong>周一至周五 9:00-18:00
                </li>
            </ul>
        </div>
    </div>
</section>
@endsection
