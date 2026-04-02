@extends('layouts.app')

@section('title', '投稿文章互动 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
    <div style="margin-bottom: 24px;">
        <a href="{{ route('submissions.index') }}" style="color: var(--primary-light); text-decoration: none; font-weight: 600;">← 返回投稿</a>
    </div>
    <h1 style="font-size: 26px; font-weight: 800; color: var(--white); margin: 0 0 8px;">📊 投稿文章互动</h1>
    <p style="color: var(--gray-light); margin: 0 0 32px; font-size: 14px;">展示已通过审核并发布为文章的数据：点赞与收藏用户列表（各最多 50 条）。</p>

    @forelse($articles as $article)
        @php
            $d = $details[$article->id] ?? ['likes' => collect(), 'favorites' => collect()];
        @endphp
        <div style="background: var(--dark-light); border-radius: 16px; padding: 24px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.08);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; margin-bottom: 16px;">
                <div>
                    <h2 style="margin: 0 0 8px; font-size: 18px; font-weight: 700; color: var(--white);">{{ $article->title }}</h2>
                    <div style="font-size: 13px; color: var(--gray);">
                        ❤️ 点赞 {{ number_format($article->like_count) }} &nbsp;·&nbsp; ⭐ 收藏 {{ number_format($article->favorite_count) }}
                    </div>
                </div>
                <a href="{{ route('articles.show', $article->id) }}" style="padding: 10px 18px; border-radius: 10px; background: rgba(99,102,241,0.2); color: var(--primary-light); text-decoration: none; font-weight: 600; font-size: 14px;">打开文章</a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                <div>
                    <h3 style="font-size: 14px; font-weight: 700; color: #fca5a5; margin: 0 0 10px;">点赞用户</h3>
                    <ul style="margin: 0; padding-left: 18px; color: var(--gray-light); font-size: 14px; line-height: 1.8;">
                        @forelse($d['likes'] as $ua)
                            <li>
                                <a href="{{ route('users.show', $ua->user_id) }}" style="color: var(--white); text-decoration: none;">{{ $ua->user->name ?? '用户' }}</a>
                                <span style="color: var(--gray); font-size: 12px;">{{ $ua->created_at?->format('Y-m-d H:i') }}</span>
                            </li>
                        @empty
                            <li style="list-style: none; padding-left: 0; color: var(--gray);">暂无</li>
                        @endforelse
                    </ul>
                </div>
                <div>
                    <h3 style="font-size: 14px; font-weight: 700; color: #fbbf24; margin: 0 0 10px;">收藏用户</h3>
                    <ul style="margin: 0; padding-left: 18px; color: var(--gray-light); font-size: 14px; line-height: 1.8;">
                        @forelse($d['favorites'] as $fav)
                            <li>
                                <a href="{{ route('users.show', $fav->user_id) }}" style="color: var(--white); text-decoration: none;">{{ $fav->user->name ?? '用户' }}</a>
                                <span style="color: var(--gray); font-size: 12px;">{{ $fav->created_at?->format('Y-m-d H:i') }}</span>
                            </li>
                        @empty
                            <li style="list-style: none; padding-left: 0; color: var(--gray);">暂无</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @empty
        <p style="color: var(--gray-light); text-align: center; padding: 48px;">暂无已通过投稿发布的文章。</p>
    @endforelse
</div>
@endsection
