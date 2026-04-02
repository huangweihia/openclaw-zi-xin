@extends('layouts.app')

@section('title', '浏览历史 - AI 副业情报局')

@section('content')

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">

    {{-- 页面标题 --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">
        <div>
            <h1 style="font-size: 36px; font-weight: 800; color: var(--white); margin-bottom: 8px;">
                📜 浏览历史
            </h1>
            <p style="color: var(--gray-light); font-size: 16px;">
                查看你最近浏览过的项目和文章
            </p>
        </div>
        
        <button onclick="clearHistory()"
                style="
                    padding: 12px 24px;
                    background: rgba(239, 68, 68, 0.15);
                    color: #ef4444;
                    border: 2px solid rgba(239, 68, 68, 0.3);
                    border-radius: 50px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                "
                onmouseover="this.style.background='#ef4444'; this.style.color='white'; this.style.borderColor='#ef4444'"
                onmouseout="this.style.background='rgba(239, 68, 68, 0.15)'; this.style.color='#ef4444'; this.style.borderColor='rgba(239, 68, 68, 0.3)'"
        >
            🗑️ 清空历史
        </button>
    </div>

    {{-- 统计卡片 --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <a href="{{ route('history.index', ['type' => 'all']) }}" 
           style="
               padding: 24px;
               background: {{ $type === 'all' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'var(--dark-light)' }};
               border-radius: 16px;
               text-decoration: none;
               color: {{ $type === 'all' ? 'white' : 'var(--white)' }};
               box-shadow: 0 4px 20px rgba(0,0,0,0.2);
               transition: all 0.3s;
               text-align: center;
               border: 1px solid rgba(255,255,255,0.1);
           "
           onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 40px rgba(0,0,0,0.3)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.2)'"
        >
            <div style="font-size: 32px; font-weight: 800; margin-bottom: 8px;">
                {{ $stats['all'] }}
            </div>
            <div style="font-size: 14px; opacity: 0.8;">全部历史</div>
        </a>
        
        <a href="{{ route('history.index', ['type' => 'projects']) }}" 
           style="
               padding: 24px;
               background: {{ $type === 'projects' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'var(--dark-light)' }};
               border-radius: 16px;
               text-decoration: none;
               color: {{ $type === 'projects' ? 'white' : 'var(--white)' }};
               box-shadow: 0 4px 20px rgba(0,0,0,0.2);
               transition: all 0.3s;
               text-align: center;
               border: 1px solid rgba(255,255,255,0.1);
           "
           onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 40px rgba(0,0,0,0.3)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.2)'"
        >
            <div style="font-size: 32px; font-weight: 800; margin-bottom: 8px;">
                {{ $stats['projects'] }}
            </div>
            <div style="font-size: 14px; opacity: 0.8;">项目历史</div>
        </a>
        
        <a href="{{ route('history.index', ['type' => 'articles']) }}" 
           style="
               padding: 24px;
               background: {{ $type === 'articles' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'var(--dark-light)' }};
               border-radius: 16px;
               text-decoration: none;
               color: {{ $type === 'articles' ? 'white' : 'var(--white)' }};
               box-shadow: 0 4px 20px rgba(0,0,0,0.2);
               transition: all 0.3s;
               text-align: center;
               border: 1px solid rgba(255,255,255,0.1);
           "
           onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 40px rgba(0,0,0,0.3)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.2)'"
        >
            <div style="font-size: 32px; font-weight: 800; margin-bottom: 8px;">
                {{ $stats['articles'] }}
            </div>
            <div style="font-size: 14px; opacity: 0.8;">文章历史</div>
        </a>
    </div>

    {{-- 筛选标签 --}}
    <div style="display: flex; gap: 12px; margin-bottom: 32px; flex-wrap: wrap;">
        <a href="{{ route('history.index', ['type' => 'all']) }}" 
           style="
               padding: 10px 20px;
               background: {{ $type === 'all' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'var(--dark-light)' }};
               color: {{ $type === 'all' ? 'white' : 'var(--white)' }};
               border: {{ $type === 'all' ? 'none' : '1px solid rgba(255,255,255,0.2)' }};
               border-radius: 50px;
               font-weight: 600;
               text-decoration: none;
               transition: all 0.3s;
           "
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
        >
            📂 全部
        </a>
        <a href="{{ route('history.index', ['type' => 'projects']) }}" 
           style="
               padding: 10px 20px;
               background: {{ $type === 'projects' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'var(--dark-light)' }};
               color: {{ $type === 'projects' ? 'white' : 'var(--white)' }};
               border: {{ $type === 'projects' ? 'none' : '1px solid rgba(255,255,255,0.2)' }};
               border-radius: 50px;
               font-weight: 600;
               text-decoration: none;
               transition: all 0.3s;
           "
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
        >
            🚀 项目
        </a>
        <a href="{{ route('history.index', ['type' => 'articles']) }}" 
           style="
               padding: 10px 20px;
               background: {{ $type === 'articles' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'var(--dark-light)' }};
               color: {{ $type === 'articles' ? 'white' : 'var(--white)' }};
               border: {{ $type === 'articles' ? 'none' : '1px solid rgba(255,255,255,0.2)' }};
               border-radius: 50px;
               font-weight: 600;
               text-decoration: none;
               transition: all 0.3s;
           "
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
        >
            📚 文章
        </a>
    </div>

    {{-- 浏览历史列表 --}}
    @if($histories->count())
        <div style="background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden;">
            @foreach($histories as $history)
                @php
                    $item = $history->viewable;
                    $isProject = $item instanceof \App\Models\Project;
                    $viewedTime = $history->viewed_at;
                @endphp
                
                <div style="
                    padding: 20px;
                    border-bottom: {{ $loop->last ? 'none' : '1px solid #e2e8f0' }};
                    display: flex;
                    gap: 20px;
                    align-items: center;
                    transition: all 0.3s;
                "
                onmouseover="this.style.background='rgba(102, 126, 234, 0.05)'"
                onmouseout="this.style.background='white'"
                >
                    {{-- 类型图标 --}}
                    <div style="
                        width: 60px;
                        height: 60px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 28px;
                        flex-shrink: 0;
                    ">
                        {{ $isProject ? '🚀' : '📚' }}
                    </div>
                    
                    {{-- 内容信息 --}}
                    <div style="flex: 1; min-width: 0;">
                        <a href="{{ $isProject ? route('projects.show', $item) : route('articles.show', $item) }}" 
                           style="text-decoration: none; color: inherit;"
                        >
                            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $item->name ?? $item->title }}
                            </h3>
                            <p style="color: #64748b; font-size: 14px; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $item->description ?? $item->summary ?? '暂无描述' }}
                            </p>
                        </a>
                        <div style="color: #94a3b8; font-size: 13px; margin-top: 8px;">
                            🕒 {{ $viewedTime->diffForHumans() }}
                        </div>
                    </div>
                    
                    {{-- 删除按钮 --}}
                    <button onclick="deleteHistory({{ $history->id }})"
                            style="
                                width: 36px;
                                height: 36px;
                                background: rgba(239, 68, 68, 0.1);
                                color: #ef4444;
                                border: none;
                                border-radius: 50%;
                                font-size: 18px;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                transition: all 0.3s;
                                flex-shrink: 0;
                            "
                            onmouseover="this.style.background='#ef4444'; this.style.color='white'"
                            onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.color='#ef4444'"
                            title="删除这条记录"
                    >
                        ✕
                    </button>
                </div>
            @endforeach
        </div>
        
        {{-- 分页 --}}
        <div style="margin-top: 48px; display: flex; justify-content: center;">
            <x-pagination-links :paginator="$histories" />
        </div>
    @else
        {{-- 空状态 --}}
        <div style="text-align: center; padding: 80px 20px;">
            <div style="font-size: 96px; margin-bottom: 24px;">📭</div>
            <h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">
                暂无浏览历史
            </h2>
            <p style="color: #64748b; font-size: 16px; margin-bottom: 32px;">
                快去探索有趣的项目和文章吧！
            </p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="{{ route('projects.index') }}" 
                   style="
                       padding: 14px 32px;
                       background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                       color: white;
                       border-radius: 50px;
                       font-size: 16px;
                       font-weight: 700;
                       text-decoration: none;
                       transition: all 0.3s;
                   "
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 30px rgba(102, 126, 234, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                >
                    浏览项目
                </a>
                <a href="{{ route('articles.index') }}" 
                   style="
                       padding: 14px 32px;
                       background: white;
                       color: #667eea;
                       border: 2px solid #667eea;
                       border-radius: 50px;
                       font-size: 16px;
                       font-weight: 700;
                       text-decoration: none;
                       transition: all 0.3s;
                   "
                   onmouseover="this.style.background='#667eea'; this.style.color='white'"
                   onmouseout="this.style.background='white'; this.style.color='#667eea'"
                >
                    浏览文章
                </a>
            </div>
        </div>
    @endif

</div>

<script>
// 清空历史
function clearHistory() {
    if (!confirm('确定要清空所有浏览历史吗？此操作不可恢复！')) return;
    
    fetch('/history/clear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '清空失败');
        }
    })
    .catch(err => {
        console.error(err);
        alert('清空失败，请重试');
    });
}

// 删除单条历史
function deleteHistory(historyId) {
    if (!confirm('确定要删除这条历史记录吗？')) return;
    
    fetch(`/history/${historyId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || '删除失败');
        }
    })
    .catch(err => {
        console.error(err);
        alert('删除失败，请重试');
    });
}
</script>

@endsection
