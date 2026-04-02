@extends('layouts.app')

@section('title', '已退订 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 600px; margin: 60px auto;">
    <div class="card" style="padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 64px; margin-bottom: 16px;">✅</div>
            <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;">退订成功</h1>
            <p style="color: var(--gray-light); font-size: 15px;">
                您已成功退订：<strong>{{ $subscription->email }}</strong>
            </p>
        </div>

        <div style="background: rgba(255,255,255,0.05); padding: 24px; border-radius: 12px; margin-bottom: 30px;">
            @if($type === 'all')
                <p style="text-align: center; color: var(--gray-light);">
                    您将不再收到任何来自 AI 副业情报局的邮件。
                </p>
            @elseif($type === 'daily')
                <p style="text-align: center; color: var(--gray-light);">
                    您将不再收到每日资讯日报。
                </p>
            @elseif($type === 'weekly')
                <p style="text-align: center; color: var(--gray-light);">
                    您将不再收到每周精选汇总。
                </p>
            @elseif($type === 'notifications')
                <p style="text-align: center; color: var(--gray-light);">
                    您将不再收到系统通知（账户相关的重要通知仍会发送）。
                </p>
            @endif
        </div>

        <div style="text-align: center;">
            <a href="{{ route('resubscribe', $subscription->unsubscribe_token) }}" class="btn" style="background: var(--primary); color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; display: inline-block;">
                🔄 重新订阅
            </a>
        </div>

        <div style="text-align: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);">
            <p style="color: var(--gray-light); font-size: 14px;">
                改变主意随时可以重新订阅，我们不会忘记你 🤗
            </p>
        </div>
    </div>
</div>
@endsection
