@extends('layouts.app')

@section('title', $article->title . ' - AI 副业情报局')

@section('content')

{{-- 阅读进度条 --}}
<div id="reading-progress" style="
    position: fixed;
    top: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    z-index: 9999;
    width: 0%;
    transition: width 0.1s;
"></div>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
    
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 40px;">
        
        {{-- 左侧：文章内容 --}}
        <div style="min-width: 0;">
            {{-- 返回按钮 --}}
            <a href="{{ route('articles.index') }}" 
               style="display: inline-flex; align-items: center; gap: 8px; color: #667eea; text-decoration: none; font-weight: 600; margin-bottom: 24px;"
               onmouseover="this.style.color='#764ba2'"
               onmouseout="this.style.color='#667eea'"
            >
                <span>←</span> 返回文章列表
            </a>
            
            {{-- 文章标题 --}}
            <h1 style="color: #1e293b; font-size: 36px; font-weight: 800; margin-bottom: 24px; line-height: 1.4;">
                {{ $article->title }}
            </h1>
            
            {{-- 文章元信息 --}}
            <div style="display: flex; gap: 24px; color: #64748b; font-size: 14px; margin-bottom: 24px; flex-wrap: wrap; align-items: center;">
                @if($article->author)
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="
                        width: 32px;
                        height: 32px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 14px;
                        font-weight: 700;
                        color: white;
                    ">
                        {{ substr($article->author->name ?? 'A', 0, 1) }}
                    </div>
                    <span style="color: #1e293b; font-weight: 600;">{{ $article->author->name ?? '匿名' }}</span>
                </div>
                @endif
                <span>📅 {{ $article->published_at?->format('Y-m-d') ?? $article->created_at->format('Y-m-d') }}</span>
                <span>👁️ {{ number_format($article->view_count) }} 阅读</span>
                <span>⏱️ {{ $article->reading_time }} 分钟阅读</span>
                @if($article->is_vip)
                    <span style="
                        padding: 4px 12px;
                        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
                        color: white;
                        border-radius: 20px;
                        font-size: 12px;
                        font-weight: 700;
                    ">👑 VIP</span>
                @endif
            </div>
            
            {{-- 点赞收藏按钮 --}}
            <div style="display: flex; gap: 12px; margin-bottom: 32px;">
                <button onclick="toggleLike({{ $article->id }})" 
                        id="btn-like"
                        style="
                            padding: 10px 20px;
                            background: {{ $isLiked ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : 'rgba(239, 68, 68, 0.1)' }};
                            color: {{ $isLiked ? 'white' : '#ef4444' }};
                            border: {{ $isLiked ? 'none' : '2px solid #ef4444' }};
                            border-radius: 50px;
                            font-weight: 600;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            transition: all 0.3s;
                        "
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(239, 68, 68, 0.3)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    <span id="btn-like-icon">{{ $isLiked ? '❤️' : '🤍' }}</span>
                    <span id="btn-like-text">{{ $isLiked ? '已点赞' : '点赞' }}</span>
                    <span id="btn-like-count" style="margin-left: 4px; color: {{ $isLiked ? 'rgba(255,255,255,0.9)' : '#94a3b8' }};">({{ number_format($article->like_count) }})</span>
                </button>
                
                <button onclick="toggleFavorite({{ $article->id }})" 
                        id="btn-favorite"
                        style="
                            padding: 10px 20px;
                            background: {{ $isFavorited ? 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)' : 'rgba(245, 158, 11, 0.1)' }};
                            color: {{ $isFavorited ? 'white' : '#f59e0b' }};
                            border: {{ $isFavorited ? 'none' : '2px solid #f59e0b' }};
                            border-radius: 50px;
                            font-weight: 600;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            transition: all 0.3s;
                        "
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(245, 158, 11, 0.3)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    <span id="btn-favorite-icon">{{ $isFavorited ? '⭐' : '☆' }}</span>
                    <span id="btn-favorite-text">{{ $isFavorited ? '已收藏' : '收藏' }}</span>
                    <span id="btn-favorite-count" style="margin-left: 4px; color: {{ $isFavorited ? 'rgba(255,255,255,0.9)' : '#94a3b8' }};">({{ number_format($article->favorite_count) }})</span>
                </button>
            </div>
            
            {{-- 封面图 --}}
            @if($article->cover_image)
            <figure style="margin: 0 0 32px 0;">
                <img src="{{ $article->cover_image }}"
                     alt="{{ $article->title }}"
                     style="width: 100%; max-height: 500px; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);"
                     onerror="this.style.display='none'"
                />
            </figure>
            @endif
            
            {{-- 文章来源（VIP 未授权时不展示外链，避免绕过正文限制） --}}
            @if($canViewFullArticle && $article->source_url)
            <div style="
                padding: 16px 20px;
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
                border: 1px solid rgba(99, 102, 241, 0.2);
                border-radius: 12px;
                margin-bottom: 32px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 12px;
            ">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 20px;">📰</span>
                    <div>
                        <div style="color: #64748b; font-size: 12px; margin-bottom: 4px;">文章来源</div>
                        <div style="color: #1e293b; font-weight: 600;">{{ $article->meta?->source ?? '网络整理' }}</div>
                    </div>
                </div>
                <a href="{{ $article->source_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   style="
                       padding: 10px 20px;
                       background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                       color: white;
                       border-radius: 50px;
                       font-size: 13px;
                       font-weight: 600;
                       text-decoration: none;
                       display: inline-flex;
                       align-items: center;
                       gap: 6px;
                       transition: all 0.3s;
                   "
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    <span>🔗</span>
                    查看原文
                </a>
            </div>
            @endif
            
            {{-- 文章内容：未授权时仅输出摘要（纯文本），不输出 HTML 正文 --}}
            <div style="margin-top: 30px;">
                @if($canViewFullArticle)
                    <div class="rich-html-content">
                        {!! $article->content !!}
                    </div>
                @else
                    <div style="position: relative; min-height: 68vh; border-radius: 20px; overflow: hidden; border: 2px solid rgba(251, 191, 36, 0.5); background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.88) 100%); box-shadow: inset 0 0 120px rgba(0,0,0,0.45);">
                        <div style="position: absolute; inset: 0; background: repeating-linear-gradient(135deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 2px, transparent 2px, transparent 8px); pointer-events: none;"></div>
                        <div style="position: relative; z-index: 1; padding: 48px 28px 40px; text-align: center; max-width: 560px; margin: 0 auto;">
                            <div style="font-size: 52px; margin-bottom: 16px;">👑</div>
                            <p style="color: #94a3b8; font-size: 13px; font-weight: 600; letter-spacing: 0.08em; margin: 0 0 12px;">VIP 专属正文已隐藏</p>
                            <p style="color: #cbd5e1; font-size: 15px; line-height: 1.85; margin: 0 0 28px;">
                                {{ $article->summary ?? '本文为 VIP 专属内容，开通会员后可阅读全文。' }}
                            </p>
                            <a href="{{ route('vip', ['redirect' => request()->fullUrl()]) }}" style="padding: 14px 32px; border-radius: 14px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: #fff; font-weight: 800; font-size: 16px; text-decoration: none; display: inline-block; box-shadow: 0 12px 40px rgba(245, 158, 11, 0.35);">
                                开通 VIP 阅读全文
                            </a>
                        </div>
                        <div style="position: relative; z-index: 1; padding: 0 24px 36px; text-align: center;">
                            <p style="color: #64748b; font-size: 12px; margin: 0;">正文不会在未开通前展示，避免预览泄露。</p>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- 标签 --}}
            @if($canViewFullArticle && $article->meta_keywords)
            <div style="margin-top: 40px; padding-top: 24px; border-top: 2px solid #e2e8f0;">
                <div style="color: #64748b; font-size: 14px; margin-bottom: 12px;">标签：</div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach(explode(',', $article->meta_keywords) as $keyword)
                        <span style="
                            padding: 6px 14px;
                            background: rgba(102, 126, 234, 0.1);
                            color: #667eea;
                            border-radius: 20px;
                            font-size: 13px;
                            font-weight: 600;
                        ">
                            #{{ trim($keyword) }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($showComments)
            {{-- 评论区（VIP 锁文且未解锁时不展示） --}}
            <div style="margin-top: 40px;">
                <h2 style="font-size: 20px; font-weight: 700; color: #1e293b; margin: 0 0 24px;">
                    💬 评论 (<span id="comment-count">{{ $commentsTotal ?? $comments->count() }}</span>)
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
                                  onblur="this.style.borderColor='#e2e8f0'"
                        ></textarea>
                        <div id="reply-state" style="display:none; margin-top:10px; padding:8px 12px; border-radius:10px; background:rgba(102,126,234,.08); color:#475569; font-size:13px;">
                            <span id="reply-state-text"></span>
                            <button type="button" onclick="cancelReply()" style="margin-left:10px; border:none; background:transparent; color:#667eea; cursor:pointer; font-weight:700;">取消回复</button>
                        </div>
                        <div style="text-align: right; margin-top: 12px;">
                            <button onclick="submitArticleComment()"
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
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                            >
                                发表评论
                            </button>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 32px; background: rgba(102, 126, 234, 0.05); border-radius: 12px; margin-bottom: 32px;">
                        <p style="color: #64748b; margin-bottom: 16px;">登录后才能发表评论</p>
                        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                            <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                               style="
                                   padding: 12px 32px;
                                   background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                   color: white;
                                   border-radius: 50px;
                                   font-size: 14px;
                                   font-weight: 700;
                                   text-decoration: none;
                                   display: inline-block;
                               "
                            >
                                立即登录
                            </a>
                            <button type="button" onclick="promptLoginForArticleComment()"
                                style="padding: 12px 32px; background: white; border: 2px dashed #cbd5e1; color: #475569; border-radius: 50px; font-size: 14px; font-weight: 700; cursor: pointer;">
                                发表评论
                            </button>
                        </div>
                    </div>
                @endauth

                <style>
                    .comment-item .reply-btn {
                        opacity: 0;
                        transition: opacity 0.15s ease;
                    }
                    .comment-item-head:hover .reply-btn,
                    .comment-item-head:focus-within .reply-btn,
                    .reply-item:hover .reply-btn,
                    .reply-item:focus-within .reply-btn {
                        opacity: 1;
                    }
                </style>
                <div id="comment-list" style="display: grid; gap: 16px;">
                    @php
                        $totalComments = $comments->count();
                        $showLastN = 5;
                        $shouldCollapse = $totalComments > 10;
                        $visibleCount = $shouldCollapse ? $showLastN : $totalComments;
                        $startIndex = $shouldCollapse ? max(0, $totalComments - $showLastN) : 0;
                    @endphp

                    @forelse($comments as $idx => $comment)
                        @php
                            $isFeatured = isset($featuredComment) && $featuredComment && $featuredComment->id === $comment->id;
                            $isVisible = !$shouldCollapse || $idx >= $startIndex || $isFeatured;
                        @endphp
                        <div class="comment-item"
                             data-comment-id="{{ $comment->id }}"
                             data-parent-name="{{ $comment->user->name ?? '匿名用户' }}"
                             style="
                                padding: 20px; 
                                border-radius: 12px; 
                                background: #f0f7ff;
                                {{ !$isVisible ? 'display:none;' : '' }};
                                word-wrap: break-word; 
                                word-break: break-word; 
                                overflow-wrap: break-word;
                            ">
                            @if(isset($featuredComment) && $featuredComment && $featuredComment->id === $comment->id)
                                <div style="display: inline-flex; align-items: center; gap: 6px; margin-bottom: 12px; padding: 4px 10px; border-radius: 999px; background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 12px; font-weight: 700;">
                                    <span>🏆</span><span>精品评论</span>
                                </div>
                            @endif
                            <div style="display: flex; gap: 16px; align-items: flex-start;">
                                <a href="{{ route('users.show', $comment->user_id) }}" title="查看用户主页" style="flex-shrink: 0; text-decoration: none;">
                                    <img src="{{ $comment->user?->avatarUrl() }}" alt="" width="40" height="40" loading="lazy" decoding="async"
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; display: block; border: 1px solid rgba(99,102,241,0.25); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                </a>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="comment-item-head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                        <div>
                                            <a href="{{ route('users.show', $comment->user_id) }}" style="color: #1e293b; font-weight: 600; text-decoration:none; font-size: 15px;">{{ $comment->user->name ?? '匿名用户' }}</a>
                                            <span style="color: #94a3b8; font-size: 13px; margin-left: 12px;">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                        <div style="display: flex; gap: 8px; align-items:center;">
                                            <button type="button"
                                                onclick="toggleCommentLike({{ $comment->id }}, this)"
                                                style="border: 1px solid {{ in_array($comment->id, $likedCommentIds ?? []) ? 'rgba(239, 68, 68, 0.4)' : '#e2e8f0' }}; background: {{ in_array($comment->id, $likedCommentIds ?? []) ? 'rgba(239, 68, 68, 0.12)' : '#fff' }}; border-radius: 999px; padding: 4px 10px; color: #ef4444; cursor: pointer; font-size: 12px; font-weight: 600; transition: all 0.2s;">
                                                👍 <span class="comment-like-count">{{ $comment->like_count ?? 0 }}</span>
                                            </button>
                                            <button type="button"
                                                onclick="startArticleReply({{ $comment->id }}, '{{ addslashes($comment->user->name ?? '匿名用户') }}')"
                                                style="border: 1px solid #e2e8f0; background: #fff; border-radius: 999px; padding: 4px 10px; color: #667eea; cursor: pointer; font-size: 12px; font-weight: 600; transition: all 0.2s; "
                                                class="reply-btn"
                                                
                                                >
                                                回复
                                            </button>
                                        </div>
                                    </div>
                                    <p style="color: #64748b; line-height: 1.6; margin: 0; font-size: 14px;">{{ $comment->content }}</p>

                                    @if($comment->replies->count())
                                        @php
                                            $replyCount = $comment->replies->count();
                                            $shouldCollapseReplies = $replyCount > 10;
                                            $showLastNReplies = 3;
                                            $replyStartIndex = $shouldCollapseReplies ? max(0, $replyCount - $showLastNReplies) : 0;
                                        @endphp
                                        <div class="replies-container" data-comment-id="{{ $comment->id }}" style="margin-top: 16px; padding-left: 24px; border-left: 2px solid #dbeafe;">
                                            @foreach($comment->replies as $ridx => $reply)
                                                @php
                                                    $isReplyVisible = !$shouldCollapseReplies || $ridx >= $replyStartIndex;
                                                @endphp
                                                <div class="reply-item" style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; {{ !$isReplyVisible ? 'display:none;' : '' }}; word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;">
                                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; gap:8px;">
                                                        <div style="display: flex; gap: 10px; align-items: center; min-width: 0;">
                                                            <a href="{{ route('users.show', $reply->user_id) }}" title="查看用户主页" style="flex-shrink: 0;">
                                                                <img src="{{ $reply->user?->avatarUrl() }}" alt="" width="32" height="32" loading="lazy" decoding="async"
                                                                    style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; display: block; border: 1px solid rgba(99,102,241,0.2); background: #e2e8f0;">
                                                            </a>
                                                            <div style="min-width: 0;">
                                                                <a href="{{ route('users.show', $reply->user_id) }}" style="color: #1e293b; font-weight: 600; font-size: 14px; text-decoration: none;">{{ $reply->user->name ?? '匿名用户' }}</a>
                                                                <span style="color: #94a3b8; font-size: 12px; margin-left: 8px;">{{ $reply->created_at->format('Y-m-d H:i') }}</span>
                                                            </div>
                                                        </div>
                                                        <button type="button"
                                                            onclick="startArticleReply({{ $comment->id }}, '{{ addslashes($reply->user->name ?? '匿名用户') }}', {{ $reply->id }})"
                                                            style="border: 1px solid #e2e8f0; background: #fff; border-radius: 999px; padding: 4px 10px; color: #667eea; cursor: pointer; font-size: 12px; font-weight: 600; transition: all 0.2s; "
                                                            class="reply-btn"
                                                            
                                                            >
                                                            回复
                                                        </button>
                                                    </div>
                                                    @if($reply->replyTo)
                                                        <div style="margin-bottom:6px; padding:6px 10px; border-radius:8px; background:#fff; color:#64748b; font-size:12px;">
                                                            <span style="color: #667eea; font-weight: 600;">{{ $reply->replyTo->user->name ?? '匿名用户' }}</span>：{{ \Illuminate\Support\Str::limit($reply->replyTo->content, 40) }}
                                                        </div>
                                                    @endif
                                                    <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">{{ $reply->content }}</p>
                                                </div>
                                            @endforeach

                                            @if($shouldCollapseReplies)
                                                <div style="padding: 8px 0;">
                                                    <button type="button" onclick="toggleArticleRepliesExpand('{{ $comment->id }}', {{ $replyCount }})"
                                                        style="padding: 6px 14px; border-radius: 999px; border: 1px solid #e2e8f0; background: #fff; color: #667eea; cursor: pointer; font-size: 12px; font-weight: 600; transition: all 0.2s;"
                                                        onmouseover="this.style.background='#f1f5f9'; this.style.transform='translateY(-1px)'"
                                                        onmouseout="this.style.background='#fff'; this.style.transform='translateY(0)'">
                                                        查看全部 {{ $replyCount }} 条回复
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
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

                @if($shouldCollapse)
                    <div style="text-align:center; margin-top: 20px;">
                        <button id="toggle-comments-btn" type="button" onclick="toggleArticleCommentsExpand()"
                            style="padding: 10px 24px; border-radius: 999px; border: 1px solid #e2e8f0; background: #fff; color: #667eea; cursor:pointer; font-weight:600; font-size: 14px; transition: all 0.2s;"
                            onmouseover="this.style.background='#f1f5f9'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'"
                            onmouseout="this.style.background='#fff'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            查看全部 {{ $totalComments }} 条评论
                        </button>
                    </div>
                @endif
            </div>
            @endif
            
        </div>
        
        {{-- 右侧：相关信息 --}}
        <div>
            {{-- 相关文章 --}}
            @if($relatedArticles->count())
            <div style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 16px;">
                    📚 相关文章
                </h3>
                <div style="display: grid; gap: 12px;">
                    @foreach($relatedArticles as $related)
                        <a href="{{ route('articles.show', $related) }}"
                           style="
                               padding: 12px;
                               background: rgba(102, 126, 234, 0.05);
                               border-radius: 8px;
                               text-decoration: none;
                               color: inherit;
                               transition: all 0.3s;
                               display: block;
                           "
                           onmouseover="this.style.background='rgba(102, 126, 234, 0.1)'; this.style.transform='translateX(5px)'"
                           onmouseout="this.style.background='rgba(102, 126, 234, 0.05)'; this.style.transform='translateX(0)'"
                        >
                            <div style="font-weight: 600; color: #1e293b; font-size: 14px; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ Str::limit($related->title, 40) }}
                            </div>
                            <div style="color: #64748b; font-size: 12px;">
                                👁️ {{ $related->view_count ?? 0 }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 广告位（右侧边栏留白区，置于相关文章下方） --}}
            @if(isset($adSlot) && $adSlot->shouldDisplaySidebar())
            <div class="article-ad-slot-wrap" style="margin-top: 8px;">
                <div class="site-ad-sidebar-card">
                    <div class="site-ad-sidebar-head">
                        <span class="site-ad-sidebar-head-label">推广</span>
                        <button type="button" class="site-ad-sidebar-close" onclick="this.closest('.article-ad-slot-wrap').remove()" aria-label="关闭">×</button>
                    </div>
                    <div class="site-ad-sidebar-body">
                        @if($adSlot->display_mode === 'html' && filled($adSlot->html_content))
                            <div class="site-ad-sidebar-html rich-html-content">{!! $adSlot->html_content !!}</div>
                        @else
                            <div class="site-ad-sidebar-meta">
                                <span class="site-ad-sidebar-badge">广告</span>
                            </div>
                            @php $adResolvedImg = $adSlot->resolvedImageUrl(); @endphp
                            @if($adResolvedImg)
                                <div class="site-ad-sidebar-img">
                                    @if($adSlot->link_url)
                                        <a href="{{ $adSlot->link_url }}" target="_blank" rel="noopener noreferrer">
                                            <img src="{{ $adResolvedImg }}" alt="">
                                        </a>
                                    @else
                                        <img src="{{ $adResolvedImg }}" alt="">
                                    @endif
                                </div>
                            @endif
                            @if(filled($adSlot->title))
                                <h3 class="site-ad-sidebar-title">{{ $adSlot->title }}</h3>
                            @endif
                            @if(filled($adSlot->body))
                                <div class="site-ad-sidebar-text">{!! nl2br(e($adSlot->body)) !!}</div>
                            @endif
                            @if(filled($adSlot->cta_label) && filled($adSlot->link_url))
                                <a href="{{ $adSlot->link_url }}" class="site-ad-sidebar-cta" target="_blank" rel="noopener noreferrer">{{ $adSlot->cta_label }} <span aria-hidden="true">›</span></a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            {{-- 分享功能已移除 --}}
        </div>
        
    </div>
    
</div>

<script>
// 阅读进度条
window.addEventListener('scroll', function() {
    const article = document.querySelector('.container');
    if (!article) return;

    const articleTop = article.offsetTop;
    const articleHeight = article.offsetHeight;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const windowHeight = window.innerHeight;

    let progress = ((scrollTop - articleTop) / (articleHeight - windowHeight)) * 100;
    progress = Math.max(0, Math.min(100, progress));

    document.getElementById('reading-progress').style.width = progress + '%';

    const key = `article-progress-{{ $article->id }}`;
    localStorage.setItem(key, String(scrollTop));
});

window.addEventListener('load', function() {
    const key = `article-progress-{{ $article->id }}`;
    const saved = Number(localStorage.getItem(key) || 0);
    if (saved > 200) {
        setTimeout(() => window.scrollTo({ top: saved, behavior: 'smooth' }), 120);
    }
});

// 点赞功能
function toggleLike(articleId) {
    @auth
        fetch(`/articles/${articleId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || '操作失败');
                return;
            }

            const btn = document.getElementById('btn-like');
            const icon = document.getElementById('btn-like-icon');
            const text = document.getElementById('btn-like-text');
            const count = document.getElementById('btn-like-count');

            if (data.liked) {
                btn.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                btn.style.color = 'white';
                btn.style.border = 'none';
                btn.animate([{ transform: 'scale(1)' }, { transform: 'scale(1.08)' }, { transform: 'scale(1)' }], { duration: 260 });
                icon.textContent = '❤️';
                text.textContent = '已点赞';
                count.style.color = 'rgba(255,255,255,0.9)';
            } else {
                btn.style.background = 'rgba(239, 68, 68, 0.1)';
                btn.style.color = '#ef4444';
                btn.style.border = '2px solid #ef4444';
                icon.textContent = '🤍';
                text.textContent = '点赞';
                count.style.color = '#94a3b8';
            }

            count.textContent = `(${data.like_count ?? 0})`;
        })
        .catch(() => alert('网络异常，请稍后重试'));
    @else
        alert('请先登录后点赞');
        window.location.href = '{{ route("login") }}';
    @endauth
}

// 收藏功能
function toggleFavorite(articleId) {
    @auth
        fetch(`/articles/${articleId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message || '操作失败');
                return;
            }

            const btn = document.getElementById('btn-favorite');
            const icon = document.getElementById('btn-favorite-icon');
            const text = document.getElementById('btn-favorite-text');
            const count = document.getElementById('btn-favorite-count');

            if (data.isFavorited) {
                btn.style.background = 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)';
                btn.style.color = 'white';
                btn.style.border = 'none';
                btn.animate([{ transform: 'scale(1)' }, { transform: 'scale(1.08)' }, { transform: 'scale(1)' }], { duration: 260 });
                icon.textContent = '⭐';
                text.textContent = '已收藏';
                count.style.color = 'rgba(255,255,255,0.9)';
            } else {
                btn.style.background = 'rgba(245, 158, 11, 0.1)';
                btn.style.color = '#f59e0b';
                btn.style.border = '2px solid #f59e0b';
                icon.textContent = '☆';
                text.textContent = '收藏';
                count.style.color = '#94a3b8';
            }

            count.textContent = `(${data.favorites_count ?? 0})`;
        })
        .catch(() => alert('网络异常，请稍后重试'));
    @else
        alert('请先登录后收藏');
        window.location.href = '{{ route("login") }}';
    @endauth
}
</script>

@if($showComments)
@auth
<script>
let articleCommentsExpanded = false;
let articleReplyParentId = null;
let articleReplyToId = null;
const ARTICLE_SHOW_LAST_N = 5;
const ARTICLE_SHOW_LAST_N_REPLIES = 3;
const articleReplyExpandedState = {};

function commentUserAvatarUrl(user, fallbackName) {
    const name = fallbackName || user?.name || 'U';
    const a = user?.avatar;
    if (a && String(a).trim()) {
        const s = String(a).trim();
        if (s.startsWith('http://') || s.startsWith('https://')) return s;
        return s.startsWith('/') ? s : '/' + s.replace(/^\//, '');
    }
    return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=6366f1&color=fff&size=128';
}

function toggleArticleCommentsExpand() {
    const items = document.querySelectorAll('#comment-list .comment-item');
    const btn = document.getElementById('toggle-comments-btn');
    if (!btn || items.length === 0) return;

    const totalItems = items.length;
    const anchorTop = btn.getBoundingClientRect().top + window.scrollY;

    if (!articleCommentsExpanded) {
        items.forEach((item) => {
            item.style.display = 'block';
        });
        articleCommentsExpanded = true;
        btn.textContent = `收起评论（共 ${totalItems} 条）`;
        btn.style.background = 'linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%)';
    } else {
        const startIndex = Math.max(0, totalItems - ARTICLE_SHOW_LAST_N);
        items.forEach((item, idx) => {
            item.style.display = idx >= startIndex ? 'block' : 'none';
        });
        articleCommentsExpanded = false;
        btn.textContent = `查看全部 ${totalItems} 条评论`;
        btn.style.background = 'linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%)';
    }

    window.scrollTo({ top: anchorTop - 100, behavior: 'smooth' });
}

function toggleArticleRepliesExpand(commentId, replyCount) {
    const container = document.querySelector(`.replies-container[data-comment-id="${commentId}"]`);
    if (!container) return;

    const btn = container.querySelector('button[onclick^="toggleArticleRepliesExpand"]');
    if (!btn) return;

    const replyItems = container.querySelectorAll('.reply-item');
    const isExpanded = articleReplyExpandedState[commentId] || false;

    if (!isExpanded) {
        replyItems.forEach((item) => {
            item.style.display = 'block';
        });
        articleReplyExpandedState[commentId] = true;
        btn.textContent = `收起回复（共 ${replyCount} 条）`;
        btn.style.background = '#e2e8f0';
    } else {
        const startIndex = Math.max(0, replyCount - ARTICLE_SHOW_LAST_N_REPLIES);
        replyItems.forEach((item, idx) => {
            item.style.display = idx >= startIndex ? 'block' : 'none';
        });
        articleReplyExpandedState[commentId] = false;
        btn.textContent = `查看全部 ${replyCount} 条回复`;
        btn.style.background = '#f8fafc';
    }
}

function startArticleReply(commentId, userName, targetCommentId = null) {
    articleReplyParentId = commentId;
    articleReplyToId = targetCommentId || commentId;
    const textarea = document.getElementById('comment-content');
    if (!textarea) return;
    textarea.focus();
    if (!textarea.value.trim()) {
        textarea.value = `回复 @${userName}：`;
    }
    showArticleReplyState(userName);
}

function showArticleReplyState(userName) {
    const box = document.getElementById('reply-state');
    const text = document.getElementById('reply-state-text');
    if (!box || !text) return;
    text.textContent = `正在回复 ${userName}`;
    box.style.display = 'block';
}

function cancelReply() {
    articleReplyParentId = null;
    const box = document.getElementById('reply-state');
    const text = document.getElementById('reply-state-text');
    const textarea = document.getElementById('comment-content');
    if (text) text.textContent = '';
    if (box) box.style.display = 'none';
    if (textarea) textarea.value = '';
}

function promptLoginForArticleComment() {
    alert('请先登录后评论');
    window.location.href = '{{ route("login") }}?redirect={{ urlencode(request()->fullUrl()) }}';
}

function renderArticleReplyItem(reply) {
    const userName = reply?.user?.name ?? '匿名用户';
    const uid = reply?.user_id ?? reply?.user?.id ?? '';
    const profileUrl = uid ? `{{ url('/users') }}/${uid}` : '#';
    const avatarSrc = commentUserAvatarUrl(reply?.user, userName);
    const quotedUser = reply?.reply_to?.user?.name || '';
    const quotedContent = reply?.reply_to?.content || '';

    return `
        <div class="reply-item" style="padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; gap:8px;">
                <div style="display: flex; gap: 10px; align-items: center; min-width: 0;">
                    <a href="${profileUrl}" title="查看用户主页" style="flex-shrink: 0;">
                        <img src="${avatarSrc}" alt="" width="32" height="32" decoding="async" style="width:32px;height:32px;border-radius:50%;object-fit:cover;display:block;border:1px solid rgba(99,102,241,0.2);background:#e2e8f0;">
                    </a>
                    <div style="min-width:0;">
                        <a href="${profileUrl}" style="color: #1e293b; font-weight: 600; font-size: 14px; text-decoration: none;">${userName}</a>
                        <span style="color: #94a3b8; font-size: 12px; margin-left: 8px;">刚刚</span>
                    </div>
                </div>
                <button type="button"
                    class="reply-btn"
                    onclick="startArticleReply(${reply?.parent_id ?? 0}, '${String(userName).replace(/'/g, "\\'")}', ${reply?.id ?? 0})"
                    style="border: 1px solid #e2e8f0; background: #fff; border-radius: 999px; padding: 4px 10px; color: #667eea; cursor: pointer; font-size: 12px; font-weight: 600;">
                    回复
                </button>
            </div>
            ${quotedUser ? `<div style="margin-bottom:6px; padding:6px 10px; border-radius:8px; background:#f8fafc; color:#64748b; font-size:12px;">引用 ${quotedUser}：${String(quotedContent).slice(0, 40)}</div>` : ''}
            <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin: 0;">${reply?.content ?? ''}</p>
        </div>
    `;
}

function appendArticleReplyToComment(reply) {
    const parentId = reply?.parent_id;
    if (!parentId) return;

    const commentItem = document.querySelector(`.comment-item[data-comment-id="${parentId}"]`);
    if (!commentItem) return;

    let container = commentItem.querySelector('.replies-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'replies-container';
        container.setAttribute('data-comment-id', String(parentId));
        container.style.marginTop = '16px';
        container.style.paddingLeft = '24px';
        container.style.borderLeft = '2px solid #e2e8f0';
        commentItem.querySelector('div[style*="flex: 1"]')?.appendChild(container);
    }

    container.insertAdjacentHTML('beforeend', renderArticleReplyItem(reply));

    const lastReply = container.lastElementChild;
    if (lastReply) {
        lastReply.style.background = 'rgba(102,126,234,0.06)';
        lastReply.style.borderRadius = '8px';
        lastReply.style.padding = '8px';
        lastReply.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => {
            lastReply.style.background = 'transparent';
            lastReply.style.padding = '0';
        }, 1200);
    }
}

function toggleCommentLike(commentId, btnEl) {
    fetch(`/interactions/comments/${commentId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || '操作失败');
            return;
        }

        const countEl = btnEl.querySelector('.comment-like-count');
        if (countEl) {
            countEl.textContent = String(data.count ?? 0);
        }
        const active = data.liked;
        btnEl.style.background = active ? 'rgba(239, 68, 68, 0.12)' : '#fff';
        btnEl.style.borderColor = active ? 'rgba(239, 68, 68, 0.4)' : '#e2e8f0';
        if (active) {
            btnEl.animate([{ transform: 'scale(1)' }, { transform: 'scale(1.08)' }, { transform: 'scale(1)' }], { duration: 220 });
        }
    })
    .catch(() => alert('网络异常，请稍后重试'));
}

function renderArticleCommentItem(comment) {
    const userName = comment?.user?.name ?? '匿名用户';
    const content = comment?.content ?? '';
    const uid = comment?.user_id ?? comment?.user?.id ?? '';
    const profileUrl = uid ? `{{ url('/users') }}/${uid}` : '#';
    const avatarSrc = commentUserAvatarUrl(comment?.user, userName);

    return `
        <div class="comment-item" data-comment-id="${comment?.id ?? ''}" style="padding: 20px; border-bottom: 1px solid #e2e8f0;">
            <div style="display: flex; gap: 16px; align-items: flex-start;">
                <a href="${profileUrl}" title="查看用户主页" style="flex-shrink:0; text-decoration:none;">
                    <img src="${avatarSrc}" alt="" width="48" height="48" decoding="async" style="width:48px;height:48px;border-radius:50%;object-fit:cover;display:block;border:1px solid rgba(225, 231, 239, 0.9);background:linear-gradient(135deg,#667eea,#764ba2);">
                </a>
                <div style="flex: 1;">
                    <div class="comment-item-head" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <div>
                            <a href="${profileUrl}" style="color: #1e293b; font-weight: 600; text-decoration: none;">${userName}</a>
                            <span style="color: #94a3b8; font-size: 13px; margin-left: 12px;">刚刚</span>
                        </div>
                        <div style="display: flex; gap: 8px; align-items:center; color: #64748b; font-size: 13px;">
                            <button type="button"
                                onclick="toggleCommentLike(${comment?.id ?? 0}, this)"
                                style="border: 1px solid #e2e8f0; background: #fff; border-radius: 999px; padding: 6px 10px; color: #ef4444; cursor: pointer; font-size: 12px; font-weight: 600;">
                                👍 <span class="comment-like-count">${comment?.like_count ?? 0}</span>
                            </button>
                            <button type="button"
                                class="reply-btn"
                                onclick="startArticleReply(${comment?.id ?? 0}, '${String(userName).replace(/'/g, "\\'")}')"
                                style="border: 1px solid #e2e8f0; background: #fff; border-radius: 999px; padding: 6px 10px; color: #667eea; cursor: pointer; font-size: 12px; font-weight: 600;">
                                回复
                            </button>
                        </div>
                    </div>
                    <p style="color: #64748b; line-height: 1.6; margin: 0;">${content}</p>
                </div>
            </div>
        </div>
    `;
}

function submitArticleComment() {
    const textarea = document.getElementById('comment-content');
    const content = textarea.value.trim();
    if (!content) {
        alert('请输入评论内容');
        return;
    }

    fetch('{{ route("articles.comments.store", $article->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            content: content,
            parent_id: articleReplyParentId,
            reply_to_id: articleReplyToId,
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const list = document.getElementById('comment-list');
            const empty = document.getElementById('comment-empty');
            const countEl = document.getElementById('comment-count');

            if (empty) {
                empty.remove();
            }

            if (articleReplyParentId) {
                appendArticleReplyToComment(data.comment);
            } else if (list) {
                list.insertAdjacentHTML('afterbegin', renderArticleCommentItem(data.comment));
                const next = Number(data.total ?? (parseInt(countEl?.textContent || '0', 10) + 1));
                if (countEl) countEl.textContent = String(next);
            }

            articleReplyParentId = null;
            articleReplyToId = null;
            cancelReply();
            textarea.value = '';
        } else {
            alert(data.message || '发表失败');
        }
    })
    .catch(() => alert('网络异常，请稍后重试'));
}
</script>
@endauth

@guest
<script>
function submitArticleComment() {
    alert('请先登录后评论');
    window.location.href = '{{ route("login") }}?redirect={{ urlencode(request()->fullUrl()) }}';
}
</script>
@endguest
@endif

@endsection
