@extends('layouts.app')

@section('title', $job->title . ' - ' . $job->company_name)

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
    
    {{-- 返回按钮 --}}
    <a href="{{ route('jobs.index') }}" 
       style="display: inline-flex; align-items: center; gap: 8px; color: var(--primary-light); text-decoration: none; font-weight: 600; margin-bottom: 24px;"
       onmouseover="this.style.color='var(--primary)'"
       onmouseout="this.style.color='var(--primary-light)'">
        <span>←</span> 返回职位列表
    </a>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 40px;">
        
        {{-- 左侧：职位详情 --}}
        <div>
            {{-- 职位头部 --}}
            <div style="background: var(--dark-light); border-radius: 16px; padding: 32px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; flex-wrap: wrap; margin-bottom: 24px;">
                    <div style="flex: 1;">
                        <h1 style="font-size: 28px; font-weight: 800; color: var(--white); margin: 0 0 12px;">
                            💼 {{ $job->title }}
                        </h1>
                        <div style="font-size: 18px; color: var(--primary-light); margin-bottom: 16px;">
                            🏢 {{ $job->company_name }}
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 12px; color: var(--gray-light); font-size: 14px;">
                            @if($job->location)
                                <span>📍 {{ $job->location }}</span>
                            @endif
                            @if($job->salary_range)
                                <span>·</span>
                                <span style="color: #10b981;">💰 {{ $job->salary_range }}</span>
                            @endif
                            <span>·</span>
                            <span>👁️ {{ $job->view_count }} 次浏览</span>
                            <span>·</span>
                            <span>📩 {{ $job->apply_count }} 次申请</span>
                        </div>
                        @if(!empty($job->source_url))
                            <div style="margin-top: 16px;">
                                <a href="{{ $job->source_url }}" target="_blank" rel="noopener noreferrer"
                                   style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.35); color: var(--primary-light); font-weight: 600; text-decoration: none; font-size: 14px;">
                                    🔗 查看来源 / 投递页面
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    @if($job->is_contact_vip && !$canViewContact)
                        <a href="{{ route('vip', ['redirect' => route('jobs.show', $job)]) }}" style="display: block; padding: 12px 20px; background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(245, 158, 11, 0.15)); border: 1px solid rgba(251, 191, 36, 0.3); border-radius: 12px; text-align: center; text-decoration: none;">
                            <div style="font-size: 24px; margin-bottom: 4px;">⭐</div>
                            <div style="font-size: 13px; color: #fbbf24; font-weight: 600;">VIP 可见联系方式 · 点击开通</div>
                        </a>
                    @endif
                </div>

                {{-- 职位描述（富文本；VIP 专属时未开通仅展示摘要） --}}
                @if($job->description)
                    <div style="margin-bottom: 24px;">
                        <h2 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0 0 12px;">📋 职位描述</h2>
                        @if(!$job->is_vip_only || $canViewFullContent)
                            <div class="job-html-content rich-html-content" style="line-height: 1.85;">{!! $job->description !!}</div>
                        @else
                            <p style="color: var(--gray-light); line-height: 1.8;">{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 320) }}</p>
                            <div style="margin-top: 16px; padding: 20px; border-radius: 12px; border: 1px solid rgba(251, 191, 36, 0.35); background: rgba(251, 191, 36, 0.08); text-align: center;">
                                <div style="font-size: 22px; margin-bottom: 8px;">👑</div>
                                <div style="font-size: 14px; color: #fbbf24; font-weight: 700; margin-bottom: 8px;">正文为 VIP 专属内容</div>
                                <a href="{{ route('vip', ['redirect' => request()->fullUrl()]) }}" style="display: inline-block; padding: 10px 22px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #0f172a; border-radius: 10px; font-weight: 700; text-decoration: none;">开通 VIP 查看完整描述</a>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 任职要求 --}}
                @if($job->requirements)
                    <div>
                        <h2 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0 0 12px;">✅ 任职要求</h2>
                        @if(!$job->is_vip_only || $canViewFullContent)
                            <div style="color: var(--gray-light); line-height: 1.8; white-space: pre-wrap;">{{ $job->requirements }}</div>
                        @else
                            <p style="color: var(--gray-light); line-height: 1.8;">{{ \Illuminate\Support\Str::limit($job->requirements, 200) }}</p>
                            <p style="font-size: 13px; color: var(--gray); margin-top: 8px;">完整任职要求与正文均为 VIP 可见</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- 联系方式 --}}
            <div style="background: var(--dark-light); border-radius: 16px; padding: 32px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
                <h2 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0 0 20px;">📞 联系方式</h2>
                
                @if($canViewContact)
                    <div style="display: grid; gap: 16px;">
                        @if($job->contact_email)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 16px; background: rgba(99, 102, 241, 0.1); border-radius: 12px; border: 1px solid rgba(99, 102, 241, 0.2);">
                                <span style="font-size: 24px;">📧</span>
                                <div>
                                    <div style="font-size: 12px; color: var(--gray); margin-bottom: 4px;">邮箱</div>
                                    <div style="font-size: 16px; color: var(--white); font-weight: 600;">{{ $job->contact_email }}</div>
                                </div>
                            </div>
                        @endif
                        
                        @if($job->contact_phone)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 16px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.2);">
                                <span style="font-size: 24px;">📱</span>
                                <div>
                                    <div style="font-size: 12px; color: var(--gray); margin-bottom: 4px;">电话</div>
                                    <div style="font-size: 16px; color: var(--white); font-weight: 600;">{{ $job->contact_phone }}</div>
                                </div>
                            </div>
                        @endif
                        
                        @if($job->contact_wechat)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 16px; background: rgba(34, 197, 94, 0.1); border-radius: 12px; border: 1px solid rgba(34, 197, 94, 0.2);">
                                <span style="font-size: 24px;">💬</span>
                                <div>
                                    <div style="font-size: 12px; color: var(--gray); margin-bottom: 4px;">微信</div>
                                    <div style="font-size: 16px; color: var(--white); font-weight: 600;">{{ $job->contact_wechat }}</div>
                                </div>
                            </div>
                        @endif
                        
                        @if(!$job->contact_email && !$job->contact_phone && !$job->contact_wechat)
                            <div style="text-align: center; padding: 30px; color: var(--gray-light);">
                                暂无联系方式，请直接申请职位
                            </div>
                        @endif
                    </div>
                @else
                    <div style="text-align: center; padding: 40px;">
                        <div style="font-size: 48px; margin-bottom: 16px;">🔒</div>
                        <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin-bottom: 8px;">联系方式仅对 VIP 开放</h3>
                        <p style="color: var(--gray-light); margin-bottom: 24px;">升级 VIP 立即可查看联系方式，直接联系招聘方</p>
                        <a href="{{ route('vip', ['redirect' => route('jobs.show', $job)]) }}" 
                           style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 15px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(251, 191, 36, 0.5)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(251, 191, 36, 0.4)'">
                            ⭐ 升级 VIP
                        </a>
                    </div>
                @endif
            </div>

            {{-- 申请职位 --}}
            @auth
                <div style="background: var(--dark-light); border-radius: 16px; padding: 32px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
                    <h2 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0 0 20px;">📮 申请职位</h2>
                    <textarea id="apply-message" rows="4" 
                              placeholder="写给招聘方的话（可选）..." 
                              style="width: 100%; padding: 16px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px; resize: vertical; margin-bottom: 16px;"
                              onfocus="this.style.borderColor='var(--primary)'"
                              onblur="this.style.borderColor='rgba(255,255,255,0.15)'"></textarea>
                    <button onclick="applyJob({{ $job->id }})" 
                            style="width: 100%; padding: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        🚀 立即申请
                    </button>
                </div>
            @else
                <div style="text-align: center; padding: 40px; background: rgba(99, 102, 241, 0.1); border-radius: 16px; border: 1px solid rgba(99, 102, 241, 0.2);">
                    <p style="color: var(--gray-light); margin-bottom: 20px;">登录后才能申请职位</p>
                    <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" 
                       style="display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 15px;">
                        立即登录
                    </a>
                </div>
            @endauth

            {{-- 评论区 --}}
            <div style="margin-top: 40px;">
                <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 24px;">
                    💬 评论 (<span id="comment-count">{{ $commentsTotal }}</span>)
                </h2>
                
                @auth
                    <div style="margin-bottom: 32px;">
                        <textarea id="comment-content" 
                                  placeholder="分享你的看法..."
                                  style="
                                      width: 100%;
                                      min-height: 100px;
                                      padding: 16px;
                                      border: 2px solid #e2e8f0;
                                      border-radius: 12px;
                                      font-size: 14px;
                                      font-family: inherit;
                                      resize: vertical;
                                      transition: border-color 0.3s;
                                  "
                                  onfocus="this.style.borderColor='#667eea'"
                                  onblur="this.style.borderColor='#e2e8f0'"></textarea>
                        <div style="text-align: right; margin-top: 12px;">
                            <button onclick="submitComment()"
                                    style="
                                        padding: 12px 32px;
                                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                        color: white;
                                        border: none;
                                        border-radius: 50px;
                                        font-size: 14px;
                                        font-weight: 700;
                                        cursor: pointer;
                                        transition: all 0.3s;
                                    "
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                发表评论
                            </button>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 32px; background: rgba(102, 126, 234, 0.05); border-radius: 12px; margin-bottom: 32px;">
                        <p style="color: #64748b; margin-bottom: 16px;">登录后才能发表评论</p>
                        <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}" 
                           style="
                               display: inline-block;
                               padding: 12px 32px;
                               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                               color: white;
                               border-radius: 50px;
                               text-decoration: none;
                               font-weight: 700;
                           ">
                            立即登录
                        </a>
                    </div>
                @endauth

                {{-- 评论列表 --}}
                <div id="comment-list" style="display: grid; gap: 16px;">
                    @forelse($comments as $comment)
                        <div class="comment-item" style="
                            padding: 20px;
                            border-radius: 12px;
                            background: #f0f7ff;
                            word-wrap: break-word;
                            word-break: break-word;
                            overflow-wrap: break-word;
                        ">
                            <div style="display: flex; gap: 16px; align-items: flex-start;">
                                <a href="{{ route('users.show', $comment->user_id) }}" title="查看用户主页" style="flex-shrink: 0; text-decoration: none;">
                                    <img src="{{ $comment->user?->avatarUrl() }}" alt="" width="40" height="40" loading="lazy" decoding="async"
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; display: block; border: 1px solid rgba(99,102,241,0.25); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                </a>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                        <div>
                                            <a href="{{ route('users.show', $comment->user_id) }}" style="color: #1e293b; font-weight: 600; font-size: 15px; text-decoration: none;">{{ $comment->user->name ?? '匿名用户' }}</a>
                                            <span style="color: #94a3b8; font-size: 13px; margin-left: 12px;">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                        <div style="display: flex; gap: 8px; align-items:center;">
                                            <button type="button"
                                                onclick="toggleCommentLike({{ $comment->id }}, this)"
                                                style="
                                                    border: 1px solid #e2e8f0;
                                                    background: #fff;
                                                    border-radius: 999px;
                                                    padding: 4px 10px;
                                                    color: #ef4444;
                                                    cursor: pointer;
                                                    font-size: 12px;
                                                    font-weight: 600;
                                                    transition: all 0.2s;
                                                    opacity: 1;
                                                    visibility: visible;
                                                "
                                                class="reply-btn"
                                                onmouseover="this.style.background='#f1f5f9'; this.style.opacity='1'"
                                                onmouseout="this.style.background='#fff'; this.style.opacity='0'">
                                                👍 {{ $comment->like_count ?? 0 }}
                                            </button>
                                        </div>
                                    </div>
                                    <p style="color: #64748b; line-height: 1.6; margin: 0; font-size: 14px;">{{ $comment->content }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div id="comment-empty" style="text-align: center; padding: 60px; color: #64748b;">
                            <div style="font-size: 64px; margin-bottom: 16px;">💬</div>
                            <p>暂无评论，快来抢沙发吧！</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 右侧：相关信息 --}}
        <div>
            {{-- 相关职位 --}}
            @if($relatedJobs->count())
                <div style="background: var(--dark-light); border-radius: 16px; padding: 20px; margin-bottom: 24px; border: 1px solid rgba(255,255,255,0.08);">
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--white); margin: 0 0 16px;">
                        📌 相关职位
                    </h3>
                    <div style="display: grid; gap: 12px;">
                        @foreach($relatedJobs as $related)
                            <a href="{{ route('jobs.show', $related) }}" 
                               style="padding: 16px; background: rgba(99, 102, 241, 0.1); border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.3s; border: 1px solid transparent;"
                               onmouseover="this.style.borderColor='rgba(99, 102, 241, 0.3)'; this.style.background='rgba(99, 102, 241, 0.15)'; this.style.transform='translateX(5px)'"
                               onmouseout="this.style.borderColor='transparent'; this.style.background='rgba(99, 102, 241, 0.1)'; this.style.transform='translateX(0)'">
                                <div style="font-weight: 600; color: var(--white); font-size: 14px; margin-bottom: 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $related->title }}
                                </div>
                                <div style="font-size: 12px; color: var(--gray-light);">
                                    {{ $related->company_name }} · {{ $related->location ?: '地点不限' }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// 申请职位
function applyJob(jobId) {
    const message = document.getElementById('apply-message').value.trim();
    
    fetch(`/jobs/${jobId}/apply`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ message })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById('apply-message').value = '';
        } else {
            alert(data.message || '申请失败');
        }
    })
    .catch(() => alert('网络异常，请稍后重试'));
}

// 发表评论
function submitComment() {
    const content = document.getElementById('comment-content').value.trim();
    if (!content) {
        alert('请输入评论内容');
        return;
    }

    fetch(`/jobs/{{ $job->id }}/comments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ content })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '发表失败');
        }
    })
    .catch(() => alert('网络异常，请稍后重试'));
}
</script>
@endsection
