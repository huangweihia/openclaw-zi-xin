@extends('layouts.app')

@section('title', 'AI 副业项目 - AI 副业情报局')

@section('content')
@php
    $vipOnly = $vipOnly ?? false;
    $projectsListRoute = $vipOnly ? 'projects.vip' : 'projects.index';
@endphp
<!-- Hero Section -->
<section style="padding: 100px 0 60px; background: linear-gradient(135deg, rgba(99,102,241,0.15) 0%, rgba(139,92,246,0.15) 100%); border-bottom: 1px solid rgba(255,255,255,0.1);">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <span style="display: inline-block; padding: 8px 20px; background: rgba(99,102,241,0.2); border: 1px solid rgba(99,102,241,0.3); border-radius: 50px; font-size: 14px; color: var(--primary-light); font-weight: 600; margin-bottom: 20px;">
                🚀 发现下一个百万级副业
            </span>
            <h1 style="font-size: 56px; font-weight: 800; margin-bottom: 20px; background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                @if($vipOnly)
                    👑 VIP 专属项目
                @else
                    AI 副业项目库
                @endif
            </h1>
            <p style="font-size: 18px; color: var(--gray-light); line-height: 1.8; margin-bottom: 40px;">
                @if($vipOnly)
                    仅展示标记为 VIP 的项目；开通 VIP 后可查看完整变现分析、技术栈与资源。
                @else
                    精选 GitHub 热门 AI 项目，包含详细教程、技术栈和变现路径<br>
                    帮你找到适合自己的副业方向
                @endif
            </p>
            
            <!-- 搜索栏 -->
            <div style="max-width: 600px; margin: 0 auto;">
                <form action="{{ route($projectsListRoute) }}" method="GET" style="display: flex; gap: 12px;">
                    <input 
                        type="text" 
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="搜索项目、技术栈、变现方式..." 
                        style="flex: 1; padding: 18px 24px; background: rgba(255,255,255,0.05); border: 2px solid rgba(255,255,255,0.1); border-radius: 16px; color: white; font-size: 16px; transition: all 0.3s;"
                        onfocus="this.style.borderColor='var(--primary)';this.style.background='rgba(255,255,255,0.1)'"
                        onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.background='rgba(255,255,255,0.05)'"
                    />
                    <button type="submit" style="padding: 18px 32px; background: var(--gradient-primary); border: none; border-radius: 16px; color: white; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        🔍 搜索
                    </button>
                </form>
            </div>
            
            <!-- 热门标签 -->
            <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; margin-top: 30px;">
                <span style="color: var(--gray-light); font-size: 14px;">热门搜索：</span>
                <a href="?tag=gpt" style="padding: 6px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; color: var(--gray-light); font-size: 13px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.2)';this.style.borderColor='var(--primary)';this.style.color='white'">GPT</a>
                <a href="?tag=midjourney" style="padding: 6px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; color: var(--gray-light); font-size: 13px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.2)';this.style.borderColor='var(--primary)';this.style.color='white'">Midjourney</a>
                <a href="?tag=stable-diffusion" style="padding: 6px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; color: var(--gray-light); font-size: 13px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.2)';this.style.borderColor='var(--primary)';this.style.color='white'">Stable Diffusion</a>
                <a href="?tag=rag" style="padding: 6px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; color: var(--gray-light); font-size: 13px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.2)';this.style.borderColor='var(--primary)';this.style.color='white'">RAG</a>
                <a href="?tag=agent" style="padding: 6px 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; color: var(--gray-light); font-size: 13px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.2)';this.style.borderColor='var(--primary)';this.style.color='white'">Agent</a>
            </div>
        </div>
    </div>
</section>

<!-- Filter Bar（与文章列表：首行分类，次行统计 + 最新/最热） -->
<section style="padding: 30px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
    <div class="container">
        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;">
            <a href="{{ route($projectsListRoute, array_filter(request()->only(['search', 'sort']))) }}" style="padding: 10px 20px; background: {{ !request('category') ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ !request('category') ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;">全部</a>
            <a href="{{ route($projectsListRoute, array_merge(array_filter(request()->only(['search', 'sort'])), ['category' => 'ai-tools'])) }}" style="padding: 10px 20px; background: {{ request('category') === 'ai-tools' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'ai-tools' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;">🤖 AI 工具</a>
            <a href="{{ route($projectsListRoute, array_merge(array_filter(request()->only(['search', 'sort'])), ['category' => 'side-projects'])) }}" style="padding: 10px 20px; background: {{ request('category') === 'side-projects' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'side-projects' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;">💡 副业项目</a>
            <a href="{{ route($projectsListRoute, array_merge(array_filter(request()->only(['search', 'sort'])), ['category' => 'monetization'])) }}" style="padding: 10px 20px; background: {{ request('category') === 'monetization' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('category') === 'monetization' ? 'white' : 'var(--gray-light)' }}; border-radius: 12px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;">💰 变现案例</a>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="color: var(--gray-light); font-size: 14px;">
                共 <span style="color: var(--primary-light); font-weight: 600;">{{ $projects->total() }}</span> 个项目
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" style="padding: 8px 16px; background: {{ request('sort') === 'latest' || !request('sort') ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('sort') === 'latest' || !request('sort') ? 'white' : 'var(--gray-light)' }}; border: {{ request('sort') === 'latest' || !request('sort') ? 'none' : '1px solid rgba(255,255,255,0.1)' }}; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none;">📅 最新</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" style="padding: 8px 16px; background: {{ request('sort') === 'popular' ? 'var(--primary)' : 'rgba(255,255,255,0.05)' }}; color: {{ request('sort') === 'popular' ? 'white' : 'var(--gray-light)' }}; border: {{ request('sort') === 'popular' ? 'none' : '1px solid rgba(255,255,255,0.1)' }}; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none;">🔥 最热</a>
            </div>
        </div>
    </div>
</section>

<!-- Projects Grid -->
<section style="padding: 60px 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 30px;">
            @forelse($projects as $project)
                @php
                    $vipLocked = $project->is_vip && (!auth()->check() || (!auth()->user()->isVip() && !auth()->user()->isAdmin()));
                    $vipRedirect = route('vip', ['redirect' => route('projects.show', $project->id)]);
                @endphp
                @if($vipLocked)
                <div class="card project-card" data-vip-locked="1" style="display: block; padding: 0; overflow: hidden; transition: all 0.3s ease; position: relative;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 60px rgba(99,102,241,0.3)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                    <div style="height: 6px; background: linear-gradient(90deg, {{ ['6366f1', '8b5cf6', 'ec4899', '10b981', 'f59e0b'][array_rand([0,1,2,3,4])] }} 0%, {{ ['8b5cf6', 'ec4899', '6366f1', '14b8a6', 'ef4444'][array_rand([0,1,2,3,4])] }} 100%);"></div>
                    <div style="padding: 30px;">
                        <a href="{{ route('projects.show', $project->id) }}" style="text-decoration: none; color: inherit; display: block;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, {{ ['6366f1', '8b5cf6', 'ec4899'][array_rand([0,1,2])] }} 0%, {{ ['8b5cf6', 'ec4899', '6366f1'][array_rand([0,1,2])] }} 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                        {{ ['🤖', '💡', '🚀', '⚡', '🎯'][array_rand([0,1,2,3,4])] }}
                                    </div>
                                    <div>
                                        <h3 style="font-size: 20px; color: white; margin: 0; font-weight: 700;">{{ $project->full_name ?? $project->name }}</h3>
                                        <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px; flex-wrap: wrap;">
                                            <span style="font-size: 12px; color: var(--gray-light);">{{ $project->language ?? 'Unknown' }}</span>
                                            <a href="{{ $vipRedirect }}" style="padding: 2px 8px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 10px; font-size: 11px; color: white; font-weight: 600; text-decoration: none;">👑 VIP</a>
                                            @if($project->is_featured)
                                                <span style="padding: 2px 8px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 10px; font-size: 11px; color: white; font-weight: 600;">⭐ 精选</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p style="color: var(--gray-light); font-size: 15px; line-height: 1.8; margin-bottom: 24px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 200) }}
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px;">
                                @foreach(array_slice($project->tags ?? [], 0, 4) as $tag)
                                    <span style="padding: 4px 12px; background: rgba(99,102,241,0.1); color: var(--primary-light); border-radius: 20px; font-size: 12px; font-weight: 500;">{{ $tag }}</span>
                                @endforeach
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.05);">
                                <div style="display: flex; gap: 16px; font-size: 13px; color: var(--gray);">
                                    <span style="display: flex; align-items: center; gap: 6px;"><span>⭐</span> {{ number_format($project->stars ?? 0) }}</span>
                                    <span style="display: flex; align-items: center; gap: 6px;"><span>🍴</span> {{ number_format($project->forks ?? 0) }}</span>
                                </div>
                            </div>
                        </a>
                        <a href="{{ $vipRedirect }}" class="vip-readmore-link" style="display: flex; align-items: center; gap: 8px; color: var(--primary-light); font-weight: 600; font-size: 14px; margin-top: 16px; text-align: left; text-decoration: none;">
                            <span>查看详情</span><span>→</span>
                        </a>
                    </div>
                </div>
                @else
                <a href="{{ route('projects.show', $project->id) }}" class="card" style="display: block; padding: 0; overflow: hidden; text-decoration: none; transition: all 0.3s ease; position: relative;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 60px rgba(99,102,241,0.3)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                    <!-- 顶部渐变条 -->
                    <div style="height: 6px; background: linear-gradient(90deg, {{ ['6366f1', '8b5cf6', 'ec4899', '10b981', 'f59e0b'][array_rand([0,1,2,3,4])] }} 0%, {{ ['8b5cf6', 'ec4899', '6366f1', '14b8a6', 'ef4444'][array_rand([0,1,2,3,4])] }} 100%);"></div>
                    
                    <div style="padding: 30px;">
                        <!-- 头部信息 -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, {{ ['6366f1', '8b5cf6', 'ec4899'][array_rand([0,1,2])] }} 0%, {{ ['8b5cf6', 'ec4899', '6366f1'][array_rand([0,1,2])] }} 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                    {{ ['🤖', '💡', '🚀', '⚡', '🎯'][array_rand([0,1,2,3,4])] }}
                                </div>
                                <div>
                                    <h3 style="font-size: 20px; color: white; margin: 0; font-weight: 700;">{{ $project->full_name ?? $project->name }}</h3>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                                        <span style="font-size: 12px; color: var(--gray-light);">{{ $project->language ?? 'Unknown' }}</span>
                                        @if($project->is_vip)
                                            <a href="{{ route('vip', ['redirect' => route('projects.show', $project->id)]) }}" style="padding: 2px 8px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 10px; font-size: 11px; color: white; font-weight: 600; text-decoration: none;">👑 VIP</a>
                                        @endif
                                        @if($project->is_featured)
                                            <span style="padding: 2px 8px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); border-radius: 10px; font-size: 11px; color: white; font-weight: 600;">⭐ 精选</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 描述 -->
                        <p style="color: var(--gray-light); font-size: 15px; line-height: 1.8; margin-bottom: 24px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($project->description ?? ''), 200) }}
                        </p>
                        
                        <!-- 标签 -->
                        <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 24px;">
                            @foreach(array_slice($project->tags ?? [], 0, 4) as $tag)
                                <span style="padding: 4px 12px; background: rgba(99,102,241,0.1); color: var(--primary-light); border-radius: 20px; font-size: 12px; font-weight: 500;">{{ $tag }}</span>
                            @endforeach
                        </div>
                        
                        <!-- 底部信息 -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.05);">
                            <div style="display: flex; gap: 16px; font-size: 13px; color: var(--gray);">
                                <span style="display: flex; align-items: center; gap: 6px;">
                                    <span>⭐</span> {{ number_format($project->stars ?? 0) }}
                                </span>
                                <span style="display: flex; align-items: center; gap: 6px;">
                                    <span>🍴</span> {{ number_format($project->forks ?? 0) }}
                                </span>
                            </div>
                            @if($project->revenue)
                                <span style="padding: 6px 14px; background: linear-gradient(135deg, rgba(16,185,129,0.2) 0%, rgba(16,185,129,0.1) 100%); border: 1px solid rgba(16,185,129,0.3); border-radius: 20px; font-size: 12px; color: #10b981; font-weight: 600;">
                                    💰 {{ $project->revenue }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
                @endif
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 100px 20px;">
                    <div style="font-size: 80px; margin-bottom: 24px;">📭</div>
                    <h3 style="font-size: 28px; color: white; margin-bottom: 12px; font-weight: 700;">暂无项目</h3>
                    <p style="color: var(--gray-light); font-size: 16px; margin-bottom: 30px;">
                        还没有收录项目，我们会尽快添加<br>
                        有推荐的项目？<a href="{{ route('contact') }}" style="color: var(--primary-light); text-decoration: underline;">联系我们</a>
                    </p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($projects->hasPages())
            <div style="display: flex; justify-content: center; margin-top: 60px;">
                {{ $projects->links('pagination::simple-bootstrap-4') }}
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 80px 0; background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(139,92,246,0.1) 100%);">
    <div class="container">
        <div class="card" style="background: var(--gradient-primary); padding: 60px 40px; text-align: center; border: none; max-width: 900px; margin: 0 auto;">
            <h2 style="font-size: 36px; color: white; margin-bottom: 16px; font-weight: 800;">
                发现更多优质项目
            </h2>
            <p style="color: rgba(255,255,255,0.9); font-size: 18px; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                每天订阅我们的邮件，第一时间获取最新 AI 副业机会和变现路径
            </p>
            @guest
                <a href="{{ route('register') }}" class="btn" style="background: white; color: var(--primary); padding: 18px 40px; border-radius: 16px; font-weight: 700; font-size: 16px; display: inline-flex; align-items: center; gap: 8px;">
                    <span>🚀</span> 免费订阅
                    <span>→</span>
                </a>
            @else
                <p style="color: rgba(255,255,255,0.9); font-size: 16px;">
                    ✅ 你已订阅，每天 10:00 准时收到邮件
                </p>
            @endguest
        </div>
    </div>
</section>

@endsection

<style>
@media (max-width: 768px) {
    [style*="grid-template-columns: repeat(auto-fill, minmax(380px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
