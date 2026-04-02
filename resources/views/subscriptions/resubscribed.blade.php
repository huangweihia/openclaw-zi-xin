@extends('layouts.app')

@section('title', '重新订阅 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 600px; margin: 60px auto;">
    <div class="card" style="padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 64px; margin-bottom: 16px;">🎉</div>
            <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;">欢迎回来！</h1>
            <p style="color: var(--gray-light); font-size: 15px;">
                您已成功重新订阅：<strong>{{ $subscription->email }}</strong>
            </p>
        </div>

        <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); padding: 24px; border-radius: 12px; margin-bottom: 30px;">
            <p style="text-align: center; color: #10b981; font-weight: 600;">
                ✅ 您已恢复接收所有邮件
            </p>
            <ul style="color: var(--gray-light); margin-top: 16px; padding-left: 20px;">
                <li>📅 每日资讯日报 - 每天早上 10:00</li>
                <li>📊 每周精选汇总 - 每周一</li>
                <li>🔔 系统通知 - 重要账户通知</li>
            </ul>
        </div>

        <div style="text-align: center;">
            @auth
                <a href="{{ route('dashboard') }}" class="btn" style="background: var(--primary); color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; display: inline-block;">
                    返回首页
                </a>
            @else
                <a href="{{ route('login') }}" class="btn" style="background: var(--primary); color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; display: inline-block;">
                    登录账号
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
