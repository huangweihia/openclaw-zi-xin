@extends('layouts.app')

@section('title', '个人中心 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 1000px; margin: 60px auto;">
    <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 30px; text-align: center;">
        👤 个人中心
    </h1>
    
    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 30px;">
        
        <!-- 左侧：个人信息 -->
        <div>
            <!-- 头像和基本信息 -->
            <div class="card" style="padding: 30px; text-align: center;">
                <!-- 头像显示 -->
                <div style="position: relative; display: inline-block; margin-bottom: 20px;">
                    <div id="avatar-preview-wrapper" style="width: 100px; height: 100px; background: {{ $user->avatar ? 'transparent' : 'linear-gradient(135deg, #6366f1, #8b5cf6)' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white; font-weight: 700; overflow: hidden; margin: 0 auto;">
                        @if($user->avatar)
                            <img id="avatar-preview" src="{{ $user->avatar }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <span id="avatar-fallback">{{ substr($user->name, 0, 1) }}</span>
                            <img id="avatar-preview" src="" alt="{{ $user->name }}" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                        @endif
                    </div>
                    <button id="avatar-upload-btn" type="button" onclick="document.getElementById('avatar-upload').click()" style="position: absolute; bottom: 0; right: 0; width: 36px; height: 36px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid var(--dark);">
                        📷
                    </button>
                    <input type="file" id="avatar-upload" name="avatar" accept="image/*" style="display: none;" onchange="uploadAvatarAjax(this)">
                </div>
                
                <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 8px;">{{ $user->name }}</h2>
                <p style="color: var(--gray-light); font-size: 14px; margin-bottom: 12px;">{{ $user->email }}</p>
                
                @if($user->isVip())
                    <span style="display: inline-block; padding: 6px 16px; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 20px;">
                        ⭐ VIP 会员
                    </span>
                @else
                    <span style="display: inline-block; padding: 6px 16px; background: rgba(255,255,255,0.1); color: var(--gray-light); border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 20px;">
                        普通会员
                    </span>
                @endif
                
                <!-- 统计数据 -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                    <div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--primary-light);">{{ $stats['favorites'] }}</div>
                        <div style="font-size: 12px; color: var(--gray-light);">收藏</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--primary-light);">{{ $stats['comments'] }}</div>
                        <div style="font-size: 12px; color: var(--gray-light);">评论</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--primary-light);">{{ $stats['histories'] }}</div>
                        <div style="font-size: 12px; color: var(--gray-light);">浏览</div>
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--primary-light);">{{ $stats['profile_messages'] ?? 0 }}</div>
                        <div style="font-size: 12px; color: var(--gray-light);">主页留言</div>
                    </div>
                </div>
            </div>
            
            <!-- 快捷菜单 -->
            <div class="card" style="padding: 20px; margin-top: 20px;">
                <a href="{{ route('favorites.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>⭐</span>
                    <span>我的收藏</span>
                </a>
                <a href="{{ route('history.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>👁️</span>
                    <span>浏览历史</span>
                </a>
                <a href="{{ route('users.show', $user->id) }}#profile-messages" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>💬</span>
                    <span>主页留言</span>
                    @if(($stats['profile_messages'] ?? 0) > 0)
                        <span style="margin-left: auto; font-size: 11px; padding: 2px 8px; border-radius: 999px; background: rgba(99,102,241,0.25); color: var(--primary-light); font-weight: 700;">{{ $stats['profile_messages'] }}</span>
                    @endif
                </a>
                @if($user->isVip() || $user->isAdmin())
                <a href="{{ route('my.jobs.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>💼</span>
                    <span>我发布的职位</span>
                </a>
                @endif
                @if(auth()->user()?->isAdmin())
                <a href="/admin" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.12)'" onmouseout="this.style.background='transparent'">
                    <span>🔒</span>
                    <span>后台入口</span>
                </a>
                @endif
                <a href="{{ route('notifications.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>🔔</span>
                    <span>系统通知</span>
                </a>
                <a href="{{ route('feedback.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>🐞</span>
                    <span>反馈中心</span>
                </a>
                @if(auth()->user()?->isVip() || auth()->user()?->isAdmin())
                <a href="{{ route('submissions.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>📝</span>
                    <span>VIP 投稿</span>
                </a>
                <a href="{{ route('articles.my-engagement') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; margin-bottom: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>📊</span>
                    <span>投稿文章互动</span>
                </a>
                @endif
                <a href="{{ route('subscriptions.preferences') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 8px; text-decoration: none; color: inherit; transition: background 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='transparent'">
                    <span>📧</span>
                    <span>订阅偏好</span>
                </a>
            </div>
        </div>
        
        <!-- 右侧：内容区域 -->
        <div>
            <!-- 收到的主页留言 -->
            <div class="card" style="padding: 24px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0;">
                        💬 收到的主页留言
                    </h3>
                    <a href="{{ route('users.show', $user->id) }}#profile-messages" style="font-size: 13px; color: var(--primary-light); text-decoration: none;">在我的主页查看全部 →</a>
                </div>
                <p style="color: var(--gray-light); font-size: 13px; margin: -8px 0 16px;">访客在你公开主页上发送的留言，仅自己可见。</p>
                @if($profileMessagesReceived->count() > 0)
                    <div style="display: grid; gap: 12px;">
                        @foreach($profileMessagesReceived as $msg)
                            <div style="padding: 16px; background: rgba(255,255,255,0.05); border-radius: 12px; border: 1px solid rgba(255,255,255,0.08);">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px; flex-wrap: wrap;">
                                    <a href="{{ route('users.show', $msg->sender_id) }}" style="font-weight: 600; color: var(--primary-light); text-decoration: none;">{{ $msg->sender?->name ?? '用户' }}</a>
                                    <span style="font-size: 12px; color: var(--gray-light);">{{ $msg->created_at->diffForHumans() }}</span>
                                </div>
                                <p style="margin: 0; color: var(--gray-light); font-size: 14px; line-height: 1.6; white-space: pre-wrap;">{{ Str::limit($msg->body, 280) }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 28px; color: var(--gray-light);">
                        <div style="font-size: 36px; margin-bottom: 10px;">📭</div>
                        <p style="margin: 0;">暂无主页留言</p>
                    </div>
                @endif
            </div>

            <!-- 我的收藏 -->
            <div class="card" style="padding: 24px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0;">
                        ⭐ 我的收藏
                    </h3>
                    <a href="{{ route('favorites.index') }}" style="font-size: 13px; color: var(--primary-light); text-decoration: none;">查看全部 →</a>
                </div>
                
                @if($favorites->count() > 0)
                    <div style="display: grid; gap: 12px;">
                        @foreach($favorites as $favorite)
                            @php
                                $item = $favorite->favoritable;
                                $type = str_contains($favorite->favoritable_type, 'Project') ? 'project' : 'article';
                            @endphp
                            <a href="{{ route($type . 's.show', $item->id) }}" style="display: flex; align-items: center; gap: 16px; padding: 16px; background: rgba(255,255,255,0.05); border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                    {{ $type === 'project' ? '🚀' : '📝' }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: white; margin-bottom: 4px;">{{ Str::limit($item->name ?? $item->title, 50) }}</div>
                                    <div style="font-size: 12px; color: var(--gray-light);">{{ $item->created_at->diffForHumans() }}</div>
                                </div>
                                <span style="padding: 4px 12px; background: rgba(245, 158, 11, 0.1); color: #fbbf24; border-radius: 20px; font-size: 11px; font-weight: 600;">
                                    {{ $type === 'project' ? '项目' : '文章' }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-light);">
                        <div style="font-size: 48px; margin-bottom: 16px;">📭</div>
                        <p>还没有收藏任何内容</p>
                        <a href="{{ route('projects.index') }}" style="display: inline-block; margin-top: 12px; padding: 10px 24px; background: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600;">去逛逛</a>
                    </div>
                @endif
            </div>
            
            <!-- 我的评论 -->
            <div class="card" style="padding: 24px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0;">
                        💬 我的评论
                    </h3>
                </div>
                
                @if($comments->count() > 0)
                    <div style="display: grid; gap: 16px;">
                        @foreach($comments->take(5) as $comment)
                            @php
                                $item = $comment->commentable;
                                $type = str_contains(get_class($item), 'Project') ? 'project' : 'article';
                            @endphp
                            <div style="padding: 16px; background: rgba(255,255,255,0.05); border-radius: 12px; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                <div style="margin-bottom: 10px;">
                                    <a href="{{ route($type . 's.show', $item->id) }}" style="color: var(--primary-light); text-decoration: none; font-size: 14px; font-weight: 600; display: block; margin-bottom: 8px;">
                                        {{ Str::limit($item->name ?? $item->title, 60) }}
                                    </a>
                                </div>
                                <div style="position: relative;">
                                    <p id="comment-preview-{{ $comment->id }}" style="color: var(--gray-light); font-size: 14px; line-height: 1.7; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $comment->content }}</p>
                                    @if(strlen($comment->content) > 80)
                                        <button onclick="toggleCommentExpand({{ $comment->id }})" style="margin-top: 8px; padding: 4px 12px; background: rgba(99, 102, 241, 0.15); color: var(--primary-light); border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(99, 102, 241, 0.25)'" onmouseout="this.style.background='rgba(99, 102, 241, 0.15)'">
                                            <span id="comment-btn-text-{{ $comment->id }}">展开</span>
                                        </button>
                                    @endif
                                </div>
                                <div style="margin-top: 12px; font-size: 12px; color: var(--gray);">{{ $comment->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                        @if($comments->count() > 5)
                            <div style="text-align: center; padding: 16px; color: var(--gray-light); font-size: 13px;">
                                显示最近 5 条评论，共 {{ $comments->count() }} 条
                            </div>
                        @endif
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-light);">
                        <div style="font-size: 48px; margin-bottom: 16px;">💭</div>
                        <p>还没有发表过评论</p>
                    </div>
                @endif
            </div>
            
            <!-- 浏览历史 -->
            <div class="card" style="padding: 24px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0;">
                        👁️ 浏览历史
                    </h3>
                    <a href="{{ route('history.index') }}" style="font-size: 13px; color: var(--primary-light); text-decoration: none;">查看全部 →</a>
                </div>
                
                @if($histories->count() > 0)
                    <div style="display: grid; gap: 12px;">
                        @foreach($histories as $history)
                            @php
                                $item = $history->viewable;
                                $type = str_contains($history->viewable_type, 'Project') ? 'project' : 'article';
                            @endphp
                            @if($item)
                                <a href="{{ route($type . 's.show', $item->id) }}" style="display: flex; align-items: center; gap: 16px; padding: 12px; background: rgba(255,255,255,0.05); border-radius: 8px; text-decoration: none; color: inherit; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                    <div style="width: 40px; height: 40px; background: rgba(99,102,241,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                        {{ $type === 'project' ? '🚀' : '📝' }}
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: white; font-size: 14px;">{{ Str::limit($item->name ?? $item->title, 50) }}</div>
                                        <div style="font-size: 11px; color: var(--gray-light);">{{ $history->viewed_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-light);">
                        <div style="font-size: 48px; margin-bottom: 16px;">📖</div>
                        <p>暂无浏览记录</p>
                    </div>
                @endif
            </div>
        </div>
        
    </div>
</div>

<script>
// 评论展开/收缩功能
function toggleCommentExpand(commentId) {
    const preview = document.getElementById(`comment-preview-${commentId}`);
    const btnText = document.getElementById(`comment-btn-text-${commentId}`);
    if (!preview || !btnText) return;
    
    if (preview.style.webkitLineClamp === 'unset') {
        preview.style.webkitLineClamp = '2';
        btnText.textContent = '展开';
    } else {
        preview.style.webkitLineClamp = 'unset';
        btnText.textContent = '收缩';
    }
}

function uploadAvatarAjax(input) {
    const file = input.files && input.files[0];
    if (!file) return;

    const btn = document.getElementById('avatar-upload-btn');
    const preview = document.getElementById('avatar-preview');
    const fallback = document.getElementById('avatar-fallback');
    const wrapper = document.getElementById('avatar-preview-wrapper');

    if (btn) {
        btn.disabled = true;
        btn.textContent = '⏳';
    }

    const formData = new FormData();
    formData.append('avatar', file);

    fetch('{{ route('profile.upload-avatar') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
            throw new Error(data.message || '上传失败');
        }

        if (preview) {
            preview.src = data.avatar_url;
            preview.style.display = 'block';
        }
        if (fallback) {
            fallback.style.display = 'none';
        }
        if (wrapper) {
            wrapper.style.background = 'transparent';
        }

        alert(data.message || '头像上传成功');
    })
    .catch((err) => {
        alert(err.message || '上传失败，请稍后重试');
    })
    .finally(() => {
        if (btn) {
            btn.disabled = false;
            btn.textContent = '📷';
        }
        input.value = '';
    });
}
</script>
@endsection
