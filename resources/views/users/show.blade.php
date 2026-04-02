@extends('layouts.app')

@section('title', ($user->name ?? '用户') . ' - 个人主页')

@section('content')
<div class="container" style="max-width: 980px; margin: 0 auto; padding: 32px 20px;">
    @if(session('success'))
        <div style="margin-bottom:16px; padding:12px 16px; background:#ecfdf5; color:#047857; border-radius:12px; font-size:14px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="margin-bottom:16px; padding:12px 16px; background:#fef2f2; color:#b91c1c; border-radius:12px; font-size:14px;">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div style="margin-bottom:16px; padding:12px 16px; background:#fef2f2; color:#b91c1c; border-radius:12px; font-size:14px;">{{ $errors->first() }}</div>
    @endif

    <div style="background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 24px rgba(0,0,0,.06); margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <img src="{{ $user->avatarUrl() }}"
                 alt="{{ $user->name }}"
                 style="width:72px; height:72px; border-radius:50%; object-fit:cover;">
            <div>
                <h1 style="margin:0; color:#1e293b;">{{ $user->name ?? '匿名用户' }}</h1>
                <p style="margin:6px 0 0; color:#64748b;">加入于 {{ optional($user->created_at)->format('Y-m-d') }}</p>
            </div>
        </div>

        <p style="margin:16px 0 0; color:#64748b; font-size:13px; line-height:1.6;">
            为保护隐私，主页仅展示汇总数据，不公开评论内容；公开讨论请在对应文章、项目或知识库文档页面查看。
        </p>

        <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin-top:18px;">
            <div style="background:#f8fafc; border-radius:12px; padding:14px; text-align:center;">
                <div style="color:#0f172a; font-size:20px; font-weight:800;">{{ $stats['comments'] ?? 0 }}</div>
                <div style="color:#64748b; font-size:13px;">评论数（汇总）</div>
            </div>
            <div style="background:#f8fafc; border-radius:12px; padding:14px; text-align:center;">
                <div style="color:#0f172a; font-size:20px; font-weight:800;">{{ $stats['favorites'] ?? 0 }}</div>
                <div style="color:#64748b; font-size:13px;">收藏数</div>
            </div>
            <div style="background:#f8fafc; border-radius:12px; padding:14px; text-align:center;">
                <div style="color:#0f172a; font-size:20px; font-weight:800;">{{ $stats['histories'] ?? 0 }}</div>
                <div style="color:#64748b; font-size:13px;">浏览数</div>
            </div>
            <div style="background:#f8fafc; border-radius:12px; padding:14px; text-align:center;">
                <div style="color:#0f172a; font-size:20px; font-weight:800;">{{ $stats['profile_messages'] ?? 0 }}</div>
                <div style="color:#64748b; font-size:13px;">主页留言</div>
            </div>
        </div>
    </div>

    <div id="profile-messages" style="background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 24px rgba(0,0,0,.06); margin-bottom:20px;">
        <h2 style="margin-top:0; color:#1e293b;">留言</h2>
        @auth
            @if((int) auth()->id() === (int) $user->id)
                @if($profileMessages)
                    <p style="color:#64748b; font-size:14px; margin-top:0;">以下为访客发给你的留言（仅自己可见）。</p>
                    @forelse($profileMessages as $msg)
                        <div style="padding:16px 0; border-bottom:1px solid #e2e8f0;">
                            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                                <div>
                                    <strong style="color:#1e293b;">{{ $msg->sender?->name ?? '用户' }}</strong>
                                    <span style="color:#94a3b8; font-size:12px; margin-left:8px;">{{ $msg->created_at->diffForHumans() }}</span>
                                </div>
                                @if(!$user->isAdmin() && auth()->user()->isVip() && !$urgentSentToday)
                                    <form action="{{ route('users.messages.urgent', [$user, $msg]) }}" method="post" style="display:flex; flex-direction:column; align-items:flex-end; gap:8px; min-width:200px;">
                                        @csrf
                                        <textarea name="urgent_note" rows="2" placeholder="紧急附言（可选，留空用默认文案）" style="width:100%; max-width:280px; padding:8px 10px; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; resize:vertical;"></textarea>
                                        <button type="submit" style="padding:8px 14px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">📣 紧急通知（邮件）</button>
                                    </form>
                                @elseif(!$user->isAdmin() && auth()->user()->isVip() && $urgentSentToday)
                                    <span style="font-size:12px; color:#94a3b8;">今日紧急通知已使用</span>
                                @elseif($user->isAdmin())
                                    <span style="font-size:12px; color:#64748b;">管理员主页：留言后已向对方邮箱自动发送通知</span>
                                @endif
                            </div>
                            <p style="margin:10px 0 0; color:#334155; font-size:14px; white-space:pre-wrap;">{{ $msg->body }}</p>
                        </div>
                    @empty
                        <p style="color:#64748b;">暂无留言</p>
                    @endforelse
                    @if($profileMessages->hasPages())
                        <div style="margin-top:16px;">
                            <x-pagination-links :paginator="$profileMessages" />
                        </div>
                    @endif
                @endif
            @else
                <h3 style="margin:0 0 12px; font-size:16px; font-weight:700; color:#1e293b;">我发给 TA 的留言</h3>
                <p style="color:#64748b; font-size:13px; margin:0 0 16px; line-height:1.5;">仅展示你在此主页向对方发送的留言，对方可在自己的主页查看。</p>
                @forelse($profileMessagesSent as $msg)
                    <div style="padding:14px 16px; margin-bottom:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;">
                        <div style="color:#94a3b8; font-size:12px; margin-bottom:8px;">{{ $msg->created_at->format('Y-m-d H:i') }} · {{ $msg->created_at->diffForHumans() }}</div>
                        <p style="margin:0; color:#334155; font-size:14px; white-space:pre-wrap; line-height:1.6;">{{ $msg->body }}</p>
                    </div>
                @empty
                    <p style="color:#64748b; font-size:14px; margin:0 0 20px;">暂无留言记录，发送后会在上方显示。</p>
                @endforelse
                @if($profileMessagesSent->hasPages())
                    <div style="margin:16px 0 20px;">
                        <x-pagination-links :paginator="$profileMessagesSent" />
                    </div>
                @endif

                <h3 style="margin:0 0 12px; font-size:16px; font-weight:700; color:#1e293b;">{{ $profileMessagesSent->isNotEmpty() ? '发送新留言' : '给 TA 留言' }}</h3>
                <form action="{{ route('users.messages.store', $user) }}" method="post">
                    @csrf
                    <label for="profile-msg-body" style="display:block; color:#64748b; font-size:13px; margin-bottom:8px;">登录用户可留言</label>
                    <textarea id="profile-msg-body" name="body" rows="4" required maxlength="2000" placeholder="写下你想说的话…" style="width:100%; padding:12px 14px; border:1px solid #e2e8f0; border-radius:12px; font-size:14px; resize:vertical;">{{ old('body') }}</textarea>
                    <button type="submit" style="margin-top:12px; padding:10px 20px; background:#6366f1; color:#fff; border:none; border-radius:10px; font-weight:600; cursor:pointer;">发送留言</button>
                </form>
            @endif
        @else
            <p style="color:#64748b; margin:0;">
                <a href="{{ route('login', ['redirect' => url()->current()]) }}" style="color:#6366f1; font-weight:600;">登录</a>
                后可向该用户留言。
            </p>
        @endauth
    </div>
</div>
@endsection
