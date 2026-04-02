@extends('layouts.app')

@section('title', '系统通知暂不可用 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 640px; margin: 0 auto; padding: 48px 20px;">
    <div class="card" style="padding: 32px; border-radius: 16px;">
        <h1 style="margin: 0 0 12px; font-size: 22px; font-weight: 800; color: var(--white);">系统通知暂不可用</h1>
        <p style="margin: 0 0 20px; color: var(--gray-light); line-height: 1.7;">
            当前数据库中尚未创建 <code style="color: var(--primary-light);">system_notifications</code> 表，通常是<strong>未在运行环境中执行迁移</strong>所致。
        </p>
        <div style="padding: 16px; border-radius: 12px; background: rgba(251, 191, 36, 0.12); border: 1px solid rgba(251, 191, 36, 0.35); margin-bottom: 24px;">
            <div style="font-weight: 700; color: #fbbf24; margin-bottom: 8px;">运维 / 部署请执行</div>
            <pre style="margin: 0; padding: 12px; background: rgba(0,0,0,0.25); border-radius: 8px; font-size: 13px; overflow-x: auto; color: #e2e8f0;">php artisan migrate</pre>
            <p style="margin: 12px 0 0; font-size: 13px; color: var(--gray-light);">
                确认迁移文件 <code>2026_03_28_140000_create_system_notifications_table.php</code> 已随代码部署到服务器。
            </p>
        </div>
        <p style="margin: 0 0 20px; font-size: 14px; color: var(--gray-light);">
            详细清单见项目文档：<code style="color: var(--primary-light);">docs/05-开发文档/迁移与前台功能变更记录.md</code>
        </p>
        <a href="{{ route('dashboard') }}" style="display: inline-block; padding: 12px 24px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-weight: 700; text-decoration: none;">返回个人中心</a>
    </div>
</div>
@endsection
