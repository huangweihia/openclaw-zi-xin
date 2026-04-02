@extends('layouts.app')

@section('title', '文章列表 - AI 副业情报局')

@section('content')
<!-- Page Header -->
<section style="padding: 60px 0; background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(139,92,246,0.1) 100%); border-bottom: 1px solid rgba(255,255,255,0.1);">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <span style="color: var(--primary-light); font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">📚 知识库</span>
                <h1 style="font-size: 42px; margin: 12px 0; background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    精选文章
                </h1>
                <p style="color: var(--gray-light); font-size: 16px; max-width: 600px;">
                    深度教程、变现案例、行业资讯，帮你从入门到精通
                </p>
            </div>
            
            <!-- 搜索框 -->
            <div style="flex: 1; max-width: 400px;">
                <div style="display: flex; gap: 12px;">
                    <input 
                        type="text" 
                        id="searchInput"
                        placeholder="搜索文章..." 
                        style="flex: 1; padding: 14px 20px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: white; font-size: 15px;"
                    />
                    <button onclick="searchArticles()" style="padding: 14px 24px; background: var(--gradient-primary); border: none; border-radius: 12px; color: white; font-weight: 600; cursor: pointer;">
                        🔍
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="padding: 40px 0;">
    <div class="container">
        <div>
            <!-- 顶部筛选栏：首行分类 -->
            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;">
                <a href="{{ route('articles.index', request()->only(['search', 'sort'])) }}" style="padding: 10px 20px; background: {{ !request('category') ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ !request('category') ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none;">📝 全部</a>
                <a href="{{ request()->fullUrlWithQuery(['category' => 'ai-tools']) }}" style="padding: 10px 20px; background: {{ request('category') === 'ai-tools' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'ai-tools' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none;">🤖 AI 工具</a>
                <a href="{{ request()->fullUrlWithQuery(['category' => 'side-projects']) }}" style="padding: 10px 20px; background: {{ request('category') === 'side-projects' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'side-projects' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none;">💡 副业</a>
                <a href="{{ request()->fullUrlWithQuery(['category' => 'learning']) }}" style="padding: 10px 20px; background: {{ request('category') === 'learning' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'learning' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none;">📖 教程</a>
                <a href="{{ request()->fullUrlWithQuery(['category' => 'monetization']) }}" style="padding: 10px 20px; background: {{ request('category') === 'monetization' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'monetization' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none;">💰 变现</a>
                <a href="{{ request()->fullUrlWithQuery(['category' => 'news']) }}" style="padding: 10px 20px; background: {{ request('category') === 'news' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'news' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none;">📰 资讯</a>
            </div>

            <!-- 次行：统计 + 最新/最热 -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
                    <div style="color: var(--gray-light); font-size: 14px;">
                        共 <span style="color: var(--primary-light); font-weight: 600;">{{ $articles->total() }}</span> 篇文章
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" style="padding: 8px 16px; background: {{ request('sort') !== 'popular' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('sort') !== 'popular' ? 'white' : 'var(--gray-light)' }}; border: {{ request('sort') !== 'popular' ? 'none' : '1px solid rgba(255,255,255,0.1)' }}; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none;">📅 最新</a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" style="padding: 8px 16px; background: {{ request('sort') === 'popular' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('sort') === 'popular' ? 'white' : 'var(--gray-light)' }}; border: {{ request('sort') === 'popular' ? 'none' : '1px solid rgba(255,255,255,0.1)' }}; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none;">🔥 最热</a>
                    </div>
                </div>

            <!-- 文章列表 -->

                <!-- 文章卡片列表 -->
                <div style="display: grid; gap: 24px;">
                    @forelse($articles as $article)
                        @php
                            $vipLocked = $article->is_vip && (!auth()->check() || (!auth()->user()->isVip() && !auth()->user()->isAdmin()));
                        @endphp
                        @if($vipLocked)
                        <div class="card article-card"
                           data-article-id="{{ $article->id }}"
                           data-article-url="{{ route('articles.show', $article->id) }}"
                           data-is-vip="1"
                           data-vip-locked="1"
                           style="display: grid; grid-template-columns: 240px 1fr; gap: 24px; padding: 0; overflow: hidden; transition: all 0.3s ease;"
                           onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 20px 40px rgba(99,102,241,0.2)'"
                           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                            <a href="{{ route('articles.show', $article->id) }}"
                               style="height: 160px; background: linear-gradient(135deg, {{ ['6366f1', '8b5cf6', 'ec4899', '10b981'][array_rand([0,1,2,3])] }} 0%, {{ ['8b5cf6', 'ec4899', '6366f1', '14b8a6'][array_rand([0,1,2,3])] }} 100%); display: flex; align-items: center; justify-content: center; font-size: 64px; text-decoration: none;">
                                {{ ['📝', '💡', '🤖', '💰', '📰'][array_rand([0,1,2,3,4])] }}
                            </a>
                            <div style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                                <a href="{{ route('articles.show', $article->id) }}" style="text-decoration: none; color: inherit;">
                                    <div>
                                        <span style="display: inline-block; padding: 4px 12px; background: rgba(99,102,241,0.1); color: var(--primary-light); border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 12px;">
                                            {{ $article->category?->name ?? '未分类' }}
                                        </span>
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                            <h2 style="font-size: 22px; color: white; margin: 0; line-height: 1.4;">
                                                {{ $article->title }}
                                            </h2>
                                            <a href="{{ route('vip', ['redirect' => route('articles.show', $article->id)]) }}" style="
                                                padding: 4px 10px;
                                                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
                                                color: white;
                                                border-radius: 6px;
                                                font-size: 11px;
                                                font-weight: 700;
                                                text-transform: uppercase;
                                                letter-spacing: 0.5px;
                                                box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);
                                                text-decoration: none;
                                            ">👑 VIP</a>
                                        </div>
                                        <p style="color: var(--gray-light); font-size: 14px; line-height: 1.8; margin: 0 0 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $article->summary ?? '暂无摘要' }}
                                        </p>
                                        <div style="display: flex; align-items: center; gap: 20px; font-size: 13px; color: var(--gray);">
                                            <span style="display: flex; align-items: center; gap: 6px;">
                                                <span>👤</span> {{ $article->author?->name ?? '匿名' }}
                                            </span>
                                            <span style="display: flex; align-items: center; gap: 6px;">
                                                <span>📅</span> {{ $article->published_at?->diffForHumans() ?? '近期' }}
                                            </span>
                                            <span style="display: flex; align-items: center; gap: 6px;">
                                                <span>👁️</span> {{ rand(100, 5000) }}
                                            </span>
                                            <span style="display: flex; align-items: center; gap: 6px;">
                                                <span>❤️</span> {{ rand(10, 500) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('vip', ['redirect' => route('articles.show', $article->id)]) }}" class="vip-readmore-link" style="display: flex; align-items: center; gap: 8px; color: var(--primary-light); font-weight: 600; font-size: 14px; margin-top: 16px; text-align: left; text-decoration: none;">
                                    <span>阅读全文</span>
                                    <span>→</span>
                                </a>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('articles.show', $article->id) }}"
                           class="card article-card"
                           data-article-id="{{ $article->id }}"
                           data-article-url="{{ route('articles.show', $article->id) }}"
                           data-is-vip="{{ $article->is_vip ? '1' : '0' }}"
                           data-vip-locked="0"
                           style="display: grid; grid-template-columns: 240px 1fr; gap: 24px; padding: 0; overflow: hidden; text-decoration: none; transition: all 0.3s ease;"
                           onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 20px 40px rgba(99,102,241,0.2)'"
                           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                            <!-- 封面图 -->
                            <div style="height: 160px; background: linear-gradient(135deg, {{ ['6366f1', '8b5cf6', 'ec4899', '10b981'][array_rand([0,1,2,3])] }} 0%, {{ ['8b5cf6', 'ec4899', '6366f1', '14b8a6'][array_rand([0,1,2,3])] }} 100%); display: flex; align-items: center; justify-content: center; font-size: 64px;">
                                {{ ['📝', '💡', '🤖', '💰', '📰'][array_rand([0,1,2,3,4])] }}
                            </div>
                            
                            <!-- 文章内容 -->
                            <div style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <!-- 分类标签 -->
                                    <span style="display: inline-block; padding: 4px 12px; background: rgba(99,102,241,0.1); color: var(--primary-light); border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 12px;">
                                        {{ $article->category?->name ?? '未分类' }}
                                    </span>
                                    
                                    <!-- 标题 + VIP 标识 -->
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                                        <h2 style="font-size: 22px; color: white; margin: 0; line-height: 1.4;">
                                            {{ $article->title }}
                                        </h2>
                                        @if($article->is_vip)
                                            <span style="
                                                padding: 4px 10px;
                                                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
                                                color: white;
                                                border-radius: 6px;
                                                font-size: 11px;
                                                font-weight: 700;
                                                text-transform: uppercase;
                                                letter-spacing: 0.5px;
                                                box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);
                                            ">
                                                👑 VIP
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- 摘要 -->
                                    <p style="color: var(--gray-light); font-size: 14px; line-height: 1.8; margin: 0 0 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $article->summary ?? '暂无摘要' }}
                                    </p>
                                    
                                    <!-- 元信息 -->
                                    <div style="display: flex; align-items: center; gap: 20px; font-size: 13px; color: var(--gray);">
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <span>👤</span> {{ $article->author?->name ?? '匿名' }}
                                        </span>
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <span>📅</span> {{ $article->published_at?->diffForHumans() ?? '近期' }}
                                        </span>
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <span>👁️</span> {{ rand(100, 5000) }}
                                        </span>
                                        <span style="display: flex; align-items: center; gap: 6px;">
                                            <span>❤️</span> {{ rand(10, 500) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- 阅读全文 -->
                                <div style="display: flex; align-items: center; gap: 8px; color: var(--primary-light); font-weight: 600; font-size: 14px; margin-top: 16px;">
                                    <span>阅读全文</span>
                                    <span>→</span>
                                </div>
                            </div>
                        </a>
                        @endif
                    @empty
                        <div class="card" style="padding: 60px; text-align: center;">
                            <div style="font-size: 64px; margin-bottom: 20px;">📭</div>
                            <h3 style="font-size: 24px; color: white; margin-bottom: 12px;">暂无文章</h3>
                            <p style="color: var(--gray-light);">还没有发布的文章，敬请期待</p>
                        </div>
                    @endforelse
                </div>

                <!-- 分页 -->
                @if($articles->hasPages())
                    <div style="display: flex; justify-content: center; margin-top: 60px; gap: 8px;">
                        {{ $articles->links('pagination::simple-bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<script>
function searchArticles() {
    const query = document.getElementById('searchInput').value.trim();
    if (query) {
        window.location.href = '?search=' + encodeURIComponent(query);
    } else {
        showToast('请输入搜索关键词', 'info');
    }
}

// 回车搜索
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchArticles();
    }
});
</script>
@endsection

<style>
/* 响应式 */
@media (max-width: 768px) {
    .card[style*="grid-template-columns: 240px"] {
        grid-template-columns: 1fr !important;
    }
    .card[style*="grid-template-columns: 240px"] > *:first-child {
        height: 200px !important;
    }
    aside {
        display: none;
    }
}
</style>
