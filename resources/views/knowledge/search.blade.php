@extends('layouts.app')

@section('title', '搜索：' . $query . ' - 知识库')

@section('content')
<section style="padding: 48px 0 24px; background: linear-gradient(135deg, rgba(99,102,241,0.12) 0%, rgba(139,92,246,0.08) 100%); border-bottom: 1px solid rgba(255,255,255,0.08);">
    <div class="container">
        <nav style="font-size: 14px; color: var(--gray-light); margin-bottom: 16px;">
            <a href="{{ route('knowledge.index') }}" style="color: var(--primary-light); text-decoration: none;">知识库</a>
            <span style="margin: 0 8px;">/</span>
            <span style="color: var(--white);">搜索结果</span>
        </nav>
        <h1 style="font-size: 28px; font-weight: 800; margin: 0 0 8px; color: var(--white);">
            搜索「<span style="color: var(--primary-light);">{{ $query }}</span>」
        </h1>
        <p style="color: var(--gray-light); font-size: 14px; margin: 0;">
            共 {{ $results['total'] ?? 0 }} 条相关结果
            @if(isset($results['from_database'], $results['from_mcp']))
                （知识库 {{ $results['from_database'] }} · 扩展 {{ $results['from_mcp'] }}）
            @endif
        </p>
    </div>
</section>

<section style="padding: 40px 0 80px;">
    <div class="container">
        @forelse($results['results'] ?? [] as $item)
            <div class="card" style="padding: 22px; margin-bottom: 16px; border: 1px solid rgba(255,255,255,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; margin-bottom: 10px;">
                    <h2 style="font-size: 18px; font-weight: 700; margin: 0; color: var(--white);">
                        @if(($item['type'] ?? '') === 'document' && !empty($item['url']))
                            <a href="{{ $item['url'] }}" style="color: inherit; text-decoration: none;">{{ $item['title'] }}</a>
                        @else
                            {{ $item['title'] }}
                        @endif
                    </h2>
                    <div style="font-size: 12px; color: var(--gray-light); text-align: right;">
                        @if(!empty($item['is_vip']))
                            <span style="padding: 2px 8px; border-radius: 6px; background: rgba(251, 191, 36, 0.2); color: #fbbf24; font-weight: 700;">VIP</span>
                        @endif
                        <span style="margin-left: 8px;">{{ $item['source'] ?? '' }}</span>
                        @if(!empty($item['author']))
                            <span> · {{ $item['author'] }}</span>
                        @endif
                    </div>
                </div>
                <div style="color: var(--gray-light); font-size: 14px; line-height: 1.7; white-space: pre-wrap;">
                    {{ $item['content'] ?? '' }}
                </div>
                @if(($item['type'] ?? '') === 'document' && !empty($item['url']))
                    <div style="margin-top: 14px;">
                        <a href="{{ $item['url'] }}" style="color: var(--primary-light); font-size: 14px; font-weight: 600; text-decoration: none;">查看文档 →</a>
                    </div>
                @endif
            </div>
        @empty
            <div class="card" style="padding: 48px; text-align: center; color: var(--gray-light);">
                <p style="margin: 0;">没有找到相关内容，可换个关键词试试。</p>
                <a href="{{ route('knowledge.index') }}" style="display: inline-block; margin-top: 16px; color: var(--primary-light); font-weight: 600;">返回知识库</a>
            </div>
        @endforelse

        <div style="margin-top: 24px;">
            <a href="{{ route('knowledge.index') }}" style="color: var(--gray-light); font-size: 14px; text-decoration: none;">← 返回知识库首页</a>
        </div>
    </div>
</section>
@endsection
