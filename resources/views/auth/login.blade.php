@extends('layouts.app')

@section('title', '登录 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 480px; margin: 80px auto;">
    <div class="card" style="padding: 40px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 48px; margin-bottom: 16px;">👋</div>
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 8px;">欢迎回来</h1>
            <p style="color: var(--gray-light); font-size: 15px;">登录你的 AI 副业情报局账号</p>
        </div>
        
        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            @if(request()->filled('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <div class="form-group">
                <label class="form-label" for="email">邮箱地址</label>
                <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="your@email.com">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">密码</label>
                <input class="form-input" id="password" type="password" name="password" required placeholder="••••••••">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="remember" style="margin-right: 8px; width: 16px; height: 16px;">
                    <span style="color: var(--gray-light); font-size: 14px;">记住我</span>
                </label>
                <a href="#" style="color: var(--primary-light); text-decoration: none; font-size: 14px; font-weight: 500;">忘记密码？</a>
            </div>

            <button class="btn btn-primary" type="submit" style="width: 100%; padding: 14px; font-size: 15px;">
                登录
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);">
            <p style="color: var(--gray-light); font-size: 14px;">
                还没有账号？
                <a href="{{ route('register') }}" style="color: var(--primary-light); text-decoration: none; font-weight: 600;">立即注册</a>
            </p>
        </div>
    </div>
    
    <p style="text-align: center; color: var(--gray); font-size: 14px; margin-top: 24px;">
        <a href="{{ route('home') }}" style="color: var(--gray); text-decoration: none;">
            <span>←</span> 返回首页
        </a>
    </p>
</div>
@endsection
