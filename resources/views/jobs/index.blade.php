@extends('layouts.app')

@section('title', '职位列表 - AI 副业情报局')

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
    
    {{-- 页面标题 --}}
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 36px; font-weight: 800; color: var(--white); margin-bottom: 12px;">
            💼 职位列表
        </h1>
        <p style="color: var(--gray-light); font-size: 16px;">
            发现优质工作机会，开启职业新篇章
        </p>
    </div>

    {{-- 搜索框 --}}
    <form action="{{ route('jobs.index') }}" method="GET" style="max-width: 600px; margin: 0 auto 40px;">
        <div style="display: flex; gap: 12px;">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="搜索职位、公司、地点..." 
                   style="flex: 1; padding: 14px 18px; background: var(--dark-light); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: var(--white); font-size: 15px;">
            <button type="submit" style="padding: 14px 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                🔍 搜索
            </button>
        </div>
    </form>

    {{-- 职位列表 --}}
    @if($jobs->count())
        <div style="display: grid; gap: 20px;">
            @foreach($jobs as $job)
                @php
                    $jobVipContactLocked = $job->is_contact_vip && !$job->canViewContact(auth()->user());
                    $jobVipRedirect = route('vip', ['redirect' => route('jobs.show', $job)]);
                @endphp
                <div
                    role="link"
                    tabindex="0"
                    onclick="window.location.href='{{ route('jobs.show', $job) }}'"
                    onkeydown="if(event.key==='Enter'){window.location.href='{{ route('jobs.show', $job) }}'}"
                    style="cursor: pointer; background: var(--dark-light); border-radius: 16px; padding: 24px; border: 1px solid rgba(255,255,255,0.08); color: inherit; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 40px rgba(0,0,0,0.3)'; this.style.borderColor='rgba(255,255,255,0.15)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'; this.style.borderColor='rgba(255,255,255,0.08)'">
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 280px;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="font-size: 32px;">🏢</div>
                                <div>
                                    <h3 style="font-size: 20px; font-weight: 700; color: var(--white); margin: 0;">{{ $job->title }}</h3>
                                    <div style="font-size: 15px; color: var(--primary-light); margin-top: 4px;">{{ $job->company_name }}</div>
                                </div>
                            </div>
                            
                            <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px;">
                                @if($job->location)
                                    <span style="padding: 6px 14px; background: rgba(99, 102, 241, 0.15); border-radius: 20px; font-size: 13px; color: var(--primary-light); font-weight: 600;">
                                        📍 {{ $job->location }}
                                    </span>
                                @endif
                                
                                @if($job->salary_range)
                                    <span style="padding: 6px 14px; background: rgba(16, 185, 129, 0.15); border-radius: 20px; font-size: 13px; color: #10b981; font-weight: 600;">
                                        💰 {{ $job->salary_range }}
                                    </span>
                                @endif
                                
                                <span style="padding: 6px 14px; background: rgba(255,255,255,0.08); border-radius: 20px; font-size: 13px; color: var(--gray-light);">
                                    👁️ {{ $job->view_count }}
                                </span>
                                
                                <span style="padding: 6px 14px; background: rgba(255,255,255,0.08); border-radius: 20px; font-size: 13px; color: var(--gray-light);">
                                    📩 {{ $job->apply_count }}
                                </span>
                                
                                @if($job->is_contact_vip)
                                    @if($jobVipContactLocked)
                                        <a href="{{ $jobVipRedirect }}" onclick="event.stopPropagation();" style="padding: 6px 14px; background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.2)); border-radius: 20px; font-size: 13px; color: #fbbf24; font-weight: 600; text-decoration: none;">
                                            ⭐ VIP 可见联系方式
                                        </a>
                                    @else
                                        <span style="padding: 6px 14px; background: linear-gradient(135deg, rgba(251, 191, 36, 0.2), rgba(245, 158, 11, 0.2)); border-radius: 20px; font-size: 13px; color: #fbbf24; font-weight: 600;">
                                            ⭐ VIP 可见联系方式
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        <div style="text-align: right;">
                            <div style="font-size: 13px; color: var(--gray); margin-bottom: 8px;">
                                {{ $job->published_at?->diffForHumans() ?? '近期发布' }}
                            </div>
                            <button style="padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                查看详情 →
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 分页 --}}
        @if($jobs->hasPages())
            <div style="margin-top: 40px;">
                {{ $jobs->links('pagination::simple-bootstrap-4') }}
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 80px 20px; color: var(--gray-light);">
            <div style="font-size: 80px; margin-bottom: 20px;">📭</div>
            <h2 style="font-size: 24px; font-weight: 700; color: var(--white); margin-bottom: 12px;">暂无职位</h2>
            <p>还没有发布的职位，敬请期待</p>
        </div>
    @endif
</div>
@endsection
