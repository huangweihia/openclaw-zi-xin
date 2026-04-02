@extends('layouts.app')

@section('title', '我发布的职位 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 960px; margin: 0 auto; padding: 40px 20px;">
    <div style="margin-bottom: 28px;">
        <a href="{{ route('dashboard') }}" style="color: var(--primary-light); text-decoration: none; font-weight: 600;">← 返回个人中心</a>
    </div>
    <h1 style="font-size: 26px; font-weight: 800; color: var(--white); margin: 0 0 8px;">💼 我发布的职位</h1>
    <p style="color: var(--gray-light); font-size: 14px; margin: 0 0 28px;">在此查看你发布的职位及收到的申请人数；点击「查看申请」可看到申请人附言。</p>

    @if(session('error'))
        <div style="margin-bottom: 20px; padding: 14px 18px; border-radius: 12px; background: rgba(248, 113, 113, 0.12); border: 1px solid rgba(248, 113, 113, 0.35); color: #fca5a5; font-size: 14px;">{{ session('error') }}</div>
    @endif

    @if(empty($applicationsTableReady))
        <div style="margin-bottom: 20px; padding: 14px 18px; border-radius: 12px; background: rgba(251, 191, 36, 0.12); border: 1px solid rgba(251, 191, 36, 0.35); color: #fbbf24; font-size: 14px;">
            申请明细功能需要先升级数据库：在服务器项目目录执行 <code style="background: rgba(0,0,0,0.25); padding: 2px 8px; border-radius: 6px;">php artisan migrate</code>
        </div>
    @endif

    @if($jobs->count())
        <div style="display: grid; gap: 16px;">
            @foreach($jobs as $job)
                <div class="card" style="padding: 20px 22px; border: 1px solid rgba(255,255,255,0.08);">
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 16px; align-items: flex-start;">
                        <div style="min-width: 0;">
                            <div style="font-size: 17px; font-weight: 700; color: var(--white); margin-bottom: 6px;">{{ $job->title }}</div>
                            <div style="font-size: 14px; color: var(--primary-light); margin-bottom: 8px;">{{ $job->company_name }}</div>
                            <div style="font-size: 13px; color: var(--gray-light);">
                                📩 收到 <strong style="color: #a5b4fc;">{{ $job->applications_count ?? 0 }}</strong> 条申请
                                @if(!$job->is_published)
                                    <span style="margin-left: 10px; color: #fbbf24;">（未上架前台）</span>
                                @endif
                            </div>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <a href="{{ route('jobs.show', $job) }}" style="padding: 10px 18px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.15); color: var(--gray-light); text-decoration: none; font-size: 14px; font-weight: 600;">前台预览</a>
                            @if(!empty($applicationsTableReady))
                                <a href="{{ route('my.jobs.applications', $job) }}" style="padding: 10px 18px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; font-size: 14px; font-weight: 700;">查看申请</a>
                            @else
                                <span style="padding: 10px 18px; border-radius: 10px; background: rgba(255,255,255,0.08); color: var(--gray); font-size: 14px; font-weight: 600; cursor: not-allowed;">查看申请（需先 migrate）</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top: 28px;">{{ $jobs->links('pagination::simple-bootstrap-4') }}</div>
    @else
        <div class="card" style="padding: 48px 24px; text-align: center; color: var(--gray-light);">
            <div style="font-size: 48px; margin-bottom: 12px;">📭</div>
            <p>你还没有发布过职位，或职位尚未同步到前台。</p>
            <p style="margin-top: 12px; font-size: 14px;">可通过 VIP 投稿发布职位，或由管理员在后台创建。</p>
        </div>
    @endif
</div>
@endsection
