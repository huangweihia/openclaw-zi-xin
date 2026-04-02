@extends('layouts.app')

@section('title', '申请列表 - ' . $job->title)

@section('content')
<div class="container" style="max-width: 900px; margin: 0 auto; padding: 40px 20px;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('my.jobs.index') }}" style="color: var(--primary-light); text-decoration: none; font-weight: 600;">← 返回我发布的职位</a>
    </div>

    <h1 style="font-size: 22px; font-weight: 800; color: var(--white); margin: 0 0 8px;">📩 申请列表</h1>
    <p style="color: var(--gray-light); font-size: 15px; margin: 0 0 24px;">
        <span style="color: var(--primary-light); font-weight: 600;">{{ $job->title }}</span>
        <span style="margin: 0 8px;">·</span>
        {{ $job->company_name }}
    </p>

    @if($applications->count())
        <div style="display: grid; gap: 14px;">
            @foreach($applications as $app)
                <div class="card" style="padding: 18px 20px; border: 1px solid rgba(255,255,255,0.08);">
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; margin-bottom: 10px;">
                        <div>
                            <a href="{{ route('users.show', $app->applicant) }}" style="font-weight: 700; color: var(--white); text-decoration: none;">{{ $app->applicant->name ?? '用户' }}</a>
                            <span style="font-size: 13px; color: var(--gray-light); margin-left: 10px;">{{ $app->applicant->email ?? '' }}</span>
                        </div>
                        <span style="font-size: 12px; color: var(--gray);">{{ $app->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if(filled($app->message))
                        <div style="padding: 12px 14px; background: rgba(99,102,241,0.08); border-radius: 10px; font-size: 14px; color: var(--gray-light); line-height: 1.6; white-space: pre-wrap;">{{ $app->message }}</div>
                    @else
                        <div style="font-size: 13px; color: var(--gray);">（无附言）</div>
                    @endif
                </div>
            @endforeach
        </div>
        <div style="margin-top: 24px;">
            <x-pagination-links :paginator="$applications" />
        </div>
    @else
        <div class="card" style="padding: 40px; text-align: center; color: var(--gray-light);">
            暂无申请记录
        </div>
    @endif
</div>
@endsection
