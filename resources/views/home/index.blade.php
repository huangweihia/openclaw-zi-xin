@extends('layouts.app')

@section('title', 'AI 副业情报局 - 每天 10 分钟，发现 AI 副业机会')

@section('content')

{{-- 价值主张区 --}}
<section style="
    padding: 100px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    text-align: center;
    color: white;
">
    <div class="container">
        <h1 style="font-size: 48px; font-weight: 800; margin-bottom: 20px; line-height: 1.2;">
            🤖 每天 10 分钟，发现 AI 副业机会
        </h1>
        <p style="font-size: 20px; opacity: 0.9; max-width: 600px; margin: 0 auto 40px;">
            聚合全网 AI 副业内容，帮你发现 AI 赚钱机会
        </p>
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            @auth
                <a href="{{ route('projects.index') }}" 
                   style="
                       padding: 16px 40px;
                       background: white;
                       color: #667eea;
                       border-radius: 50px;
                       font-size: 18px;
                       font-weight: 700;
                       text-decoration: none;
                       display: inline-block;
                       transition: all 0.3s;
                       box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                   "
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.3)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)'"
                >
                    🚀 开始探索
                </a>
            @else
                <a href="{{ route('register') }}" 
                   style="
                       padding: 16px 40px;
                       background: white;
                       color: #667eea;
                       border-radius: 50px;
                       font-size: 18px;
                       font-weight: 700;
                       text-decoration: none;
                       display: inline-block;
                       transition: all 0.3s;
                       box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                   "
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.3)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)'"
                >
                    🆓 免费注册
                </a>
                <a href="{{ route('vip') }}" 
                   style="
                       padding: 16px 40px;
                       background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
                       color: white;
                       border-radius: 50px;
                       font-size: 18px;
                       font-weight: 700;
                       text-decoration: none;
                       display: inline-block;
                       transition: all 0.3s;
                       box-shadow: 0 10px 30px rgba(251, 191, 36, 0.4);
                   "
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(251, 191, 36, 0.5)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(251, 191, 36, 0.4)'"
                >
                    👑 开通 VIP
                </a>
            @endauth
        </div>
    </div>
</section>

{{-- 社会证明 --}}
<section style="padding: 60px 0; background: #f8fafc;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; text-align: center;">
            <div>
                <div style="font-size: 48px; font-weight: 800; color: #667eea; margin-bottom: 8px;">
                    {{ number_format($stats['users']) }}+
                </div>
                <div style="color: #64748b; font-size: 16px;">注册用户</div>
            </div>
            <div>
                <div style="font-size: 48px; font-weight: 800; color: #667eea; margin-bottom: 8px;">
                    {{ number_format($stats['projects']) }}+
                </div>
                <div style="color: #64748b; font-size: 16px;">AI 项目</div>
            </div>
            <div>
                <div style="font-size: 48px; font-weight: 800; color: #667eea; margin-bottom: 8px;">
                    {{ number_format($stats['articles']) }}+
                </div>
                <div style="color: #64748b; font-size: 16px;">精选文章</div>
            </div>
            <div>
                <div style="font-size: 48px; font-weight: 800; color: #667eea; margin-bottom: 8px;">
                    15+
                </div>
                <div style="color: #64748b; font-size: 16px;">每日更新</div>
            </div>
        </div>
    </div>
</section>

{{-- 今日精选项目 --}}
<section style="padding: 80px 0;">
    <div class="container">
        <h2 style="font-size: 36px; font-weight: 800; text-align: center; margin-bottom: 16px; color: #1e293b;">
            🔥 今日精选项目
        </h2>
        <p style="text-align: center; color: #64748b; font-size: 16px; margin-bottom: 48px;">
            精选全网最热门的 AI 副业项目
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            @forelse($featuredProjects as $project)
                <a href="{{ route('projects.show', $project) }}" 
                   style="
                       background: white;
                       border-radius: 16px;
                       padding: 24px;
                       text-decoration: none;
                       color: inherit;
                       box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                       transition: all 0.3s;
                       border: 2px solid transparent;
                   "
                   onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 40px rgba(0,0,0,0.12)'; this.style.borderColor='#667eea'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'; this.style.borderColor='transparent'"
                >
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <div style="
                            width: 48px;
                            height: 48px;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            border-radius: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 24px;
                        ">
                            🚀
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 18px; font-weight: 700; margin: 0; color: #1e293b;">
                                {{ Str::limit($project->name, 30) }}
                            </h3>
                            <div style="color: #64748b; font-size: 14px; margin-top: 4px;">
                                ⭐ {{ $project->stars ?? 0 }} stars
                            </div>
                        </div>
                    </div>
                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $project->description ?? '暂无描述' }}
                    </p>
                    @if($project->monetization)
                        <div style="
                            margin-top: 16px;
                            padding: 8px 12px;
                            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
                            border-radius: 8px;
                            color: #667eea;
                            font-size: 13px;
                            font-weight: 600;
                        ">
                            💰 {{ $project->monetization }}
                        </div>
                    @endif
                </a>
            @empty
                <div style="text-align: center; padding: 60px; color: #64748b;">
                    <div style="font-size: 64px; margin-bottom: 16px;">📭</div>
                    <p>暂无项目，敬请期待</p>
                </div>
            @endforelse
        </div>
        
        <div style="text-align: center; margin-top: 48px;">
            <a href="{{ route('projects.index') }}" 
               style="
                   padding: 14px 40px;
                   background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                   color: white;
                   border-radius: 50px;
                   font-size: 16px;
                   font-weight: 700;
                   text-decoration: none;
                   display: inline-block;
                   transition: all 0.3s;
               "
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
            >
                查看更多项目 →
            </a>
        </div>
    </div>
</section>

{{-- 今日精选文章 --}}
<section style="padding: 80px 0; background: #f8fafc;">
    <div class="container">
        <h2 style="font-size: 36px; font-weight: 800; text-align: center; margin-bottom: 16px; color: #1e293b;">
            📚 今日精选文章
        </h2>
        <p style="text-align: center; color: #64748b; font-size: 16px; margin-bottom: 48px;">
            深度教程、变现案例、行业资讯
        </p>
        
        <div style="display: grid; gap: 20px;">
            @forelse($featuredArticles as $article)
                <a href="{{ route('articles.show', $article) }}" 
                   style="
                       background: white;
                       border-radius: 16px;
                       padding: 24px;
                       text-decoration: none;
                       color: inherit;
                       box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                       transition: all 0.3s;
                       display: grid;
                       grid-template-columns: 200px 1fr;
                       gap: 24px;
                       align-items: center;
                   "
                   onmouseover="this.style.transform='translateX(5px)'; this.style.boxShadow='0 10px 40px rgba(0,0,0,0.12)'"
                   onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'"
                >
                    <div style="
                        height: 120px;
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 48px;
                    ">
                        📝
                    </div>
                    <div>
                        <h3 style="font-size: 20px; font-weight: 700; margin: 0 0 12px; color: #1e293b;">
                            {{ Str::limit($article->title, 50) }}
                        </h3>
                        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $article->summary ?? '暂无摘要' }}
                        </p>
                        <div style="display: flex; gap: 20px; font-size: 13px; color: #94a3b8;">
                            <span>👁️ {{ $article->view_count ?? 0 }}</span>
                            <span>❤️ {{ $article->like_count ?? 0 }}</span>
                            <span>📅 {{ $article->created_at?->diffForHumans() ?? '近期' }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div style="text-align: center; padding: 60px; color: #64748b;">
                    <div style="font-size: 64px; margin-bottom: 16px;">📭</div>
                    <p>暂无文章，敬请期待</p>
                </div>
            @endforelse
        </div>
        
        <div style="text-align: center; margin-top: 48px;">
            <a href="{{ route('articles.index') }}" 
               style="
                   padding: 14px 40px;
                   background: white;
                   color: #667eea;
                   border: 2px solid #667eea;
                   border-radius: 50px;
                   font-size: 16px;
                   font-weight: 700;
                   text-decoration: none;
                   display: inline-block;
                   transition: all 0.3s;
               "
               onmouseover="this.style.background='#667eea'; this.style.color='white'"
               onmouseout="this.style.background='white'; this.style.color='#667eea'"
            >
                查看更多文章 →
            </a>
        </div>
    </div>
</section>

{{-- 热门分类 --}}
<section style="padding: 80px 0;">
    <div class="container">
        <h2 style="font-size: 36px; font-weight: 800; text-align: center; margin-bottom: 16px; color: #1e293b;">
            🗂️ 热门分类
        </h2>
        <p style="text-align: center; color: #64748b; font-size: 16px; margin-bottom: 48px;">
            找到你感兴趣的内容
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            @forelse($categories as $category)
                <a href="{{ route('projects.index') }}?category={{ $category->slug }}" 
                   style="
                       padding: 30px 20px;
                       background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
                       border: 2px solid rgba(102, 126, 234, 0.2);
                       border-radius: 16px;
                       text-align: center;
                       text-decoration: none;
                       color: inherit;
                       transition: all 0.3s;
                   "
                   onmouseover="this.style.borderColor='#667eea'; this.style.background='linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%)'; this.style.transform='translateY(-3px)'"
                   onmouseout="this.style.borderColor='rgba(102, 126, 234, 0.2)'; this.style.background='linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%)'; this.style.transform='translateY(0)'"
                >
                    <div style="font-size: 40px; margin-bottom: 12px;">
                        @switch($category->slug)
                            @case('ai-tools') 🤖 @break
                            @case('side-projects') 💡 @break
                            @case('learning') 📖 @break
                            @case('news') 📰 @break
                            @case('monetization') 💰 @break
                            @default 📁
                        @endswitch
                    </div>
                    <div style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                        {{ $category->name }}
                    </div>
                    <div style="color: #64748b; font-size: 14px;">
                        {{ $category->projects_count + $category->articles_count }} 篇内容
                    </div>
                </a>
            @empty
                <div style="text-align: center; padding: 60px; color: #64748b;">
                    <p>暂无分类</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- CTA 区域 --}}
<section style="
    padding: 100px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    text-align: center;
    color: white;
">
    <div class="container">
        <h2 style="font-size: 36px; font-weight: 800; margin-bottom: 16px;">
            准备好开始你的 AI 副业之旅了吗？
        </h2>
        <p style="font-size: 18px; opacity: 0.9; max-width: 600px; margin: 0 auto 40px;">
            立即注册，获取每日 AI 副业情报
        </p>
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
            @guest
                <a href="{{ route('register') }}" 
                   style="
                       padding: 16px 40px;
                       background: white;
                       color: #667eea;
                       border-radius: 50px;
                       font-size: 18px;
                       font-weight: 700;
                       text-decoration: none;
                       display: inline-block;
                       transition: all 0.3s;
                       box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                   "
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.3)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)'"
                >
                    🆓 免费注册
                </a>
                <a href="{{ route('login') }}" 
                   style="
                       padding: 16px 40px;
                       background: rgba(255,255,255,0.2);
                       color: white;
                       border-radius: 50px;
                       font-size: 18px;
                       font-weight: 700;
                       text-decoration: none;
                       display: inline-block;
                       transition: all 0.3s;
                   "
                   onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.2)'"
                >
                    已有账号？登录
                </a>
            @else
                <a href="{{ route('dashboard') }}" 
                   style="
                       padding: 16px 40px;
                       background: white;
                       color: #667eea;
                       border-radius: 50px;
                       font-size: 18px;
                       font-weight: 700;
                       text-decoration: none;
                       display: inline-block;
                       transition: all 0.3s;
                       box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                   "
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(0,0,0,0.3)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)'"
                >
                    进入个人中心
                </a>
            @endguest
        </div>
    </div>
</section>

@endsection
