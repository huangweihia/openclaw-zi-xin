@extends('layouts.app')

@section('title', '系统通知 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
        <h1 style="margin: 0; font-size: 26px; font-weight: 800; color: var(--white);">🔔 系统通知</h1>
        @if(($unreadCount ?? 0) > 0)
            <form method="post" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" style="padding: 10px 18px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2); background: var(--dark-light); color: var(--primary-light); font-weight: 600; cursor: pointer;">
                    全部标为已读
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(16,185,129,0.15); border-radius: 12px; color: #6ee7b7;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(239,68,68,0.15); border-radius: 12px; color: #fca5a5;">{{ session('error') }}</div>
    @endif

    <div style="display: grid; gap: 12px;">
        @forelse($notifications as $n)
            @php
                $isAdmin = $n->is_from_admin;
                $border = $isAdmin ? 'border: 2px solid rgba(251, 191, 36, 0.45);' : 'border: 1px solid rgba(255,255,255,0.08);';
                $bg = $isAdmin ? 'background: linear-gradient(135deg, rgba(251, 191, 36, 0.12) 0%, rgba(15, 23, 42, 0.5) 100%);' : 'background: var(--dark-light);';
            @endphp
            <div style="border-radius: 14px; padding: 18px 20px; {{ $border }} {{ $bg }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 0;">
                        @if($isAdmin)
                            <span style="display: inline-block; padding: 2px 10px; border-radius: 999px; background: rgba(251, 191, 36, 0.25); color: #fbbf24; font-size: 11px; font-weight: 800; margin-bottom: 8px;">置顶 · 官方</span>
                        @endif
                        <div style="font-weight: 700; color: var(--white); font-size: 16px; margin-bottom: 6px;">{{ $n->title }}</div>
                        @if($n->body)
                            <p style="margin: 0; color: var(--gray-light); font-size: 14px; line-height: 1.6;">{{ $n->body }}</p>
                        @endif
                        @if(!empty($n->meta['article_id']))
                            <a href="{{ route('articles.show', $n->meta['article_id']) }}" style="display: inline-block; margin-top: 10px; color: var(--primary-light); font-size: 13px; font-weight: 600;">查看文章 →</a>
                        @endif
                    </div>
                    <div style="text-align: right; font-size: 12px; color: var(--gray);">
                        <div>{{ $n->created_at->format('Y-m-d H:i') }}</div>
                        @if($n->read_at)
                            <span style="color: #64748b;">已读</span>
                        @else
                            <form method="post" action="{{ route('notifications.read', $n) }}" style="margin-top: 8px;">
                                @csrf
                                <button type="submit" style="padding: 6px 12px; border-radius: 8px; border: none; background: rgba(99,102,241,0.3); color: #a5b4fc; font-size: 12px; cursor: pointer;">标为已读</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p style="color: var(--gray-light); text-align: center; padding: 60px; font-size: 15px;">暂无通知</p>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div style="margin-top: 28px;">
            <x-pagination-links :paginator="$notifications" />
        </div>
    @endif
</div>
@endsection
