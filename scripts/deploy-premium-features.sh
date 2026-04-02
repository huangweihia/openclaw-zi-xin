#!/bin/bash
# OpenClaw 智信 - Premium 内容功能完整部署脚本
# 包含：数据库迁移 + 所有视图文件 + 路由配置

set -e

CONTAINER="ai_side_php_max"
VIEWS="/var/www/html/resources/views/max"
ROUTES="/var/www/html/routes"

echo "🚀 开始部署 Premium 内容功能（20 个功能）..."
echo ""

# ============================================
# 步骤 1：运行数据库迁移
# ============================================
echo "1️⃣ 运行数据库迁移..."
docker exec $CONTAINER php artisan migrate --force
echo "✅ 数据库迁移完成"
echo ""

# ============================================
# 步骤 2：创建视图目录
# ============================================
echo "2️⃣ 创建视图目录..."
docker exec $CONTAINER mkdir -p $VIEWS/{cases,tools,sops,resources}
echo "✅ 视图目录创建完成"
echo ""

# ============================================
# 步骤 3：创建副业案例列表页
# ============================================
echo "3️⃣ 创建副业案例视图..."
docker exec $CONTAINER cat > $VIEWS/cases/index.blade.php << 'CASEINDEX'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>副业案例库 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'cases'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="inline-block px-6 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mb-6">💰 VIP 专属内容</span>
            <h1 class="text-5xl md:text-6xl font-bold mb-6"><span class="gradient-bg bg-clip-text text-transparent">副业实战案例库</span></h1>
            <p class="text-xl text-gray-600 mb-8">50+ 真实副业案例，包含启动成本、时间投入、月收入、操作步骤</p>
            <form action="{{ route('premium.cases.index') }}" method="GET" class="max-w-2xl mx-auto mb-8">
                <div class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="搜索案例..." class="flex-1 px-6 py-4 bg-white border-2 border-purple-200 rounded-full focus:outline-none focus:border-purple-500"/>
                    <button type="submit" class="gradient-bg text-white px-8 py-4 rounded-full font-semibold hover:opacity-90">🔍 搜索</button>
                </div>
            </form>
        </div>
    </section>

    <section class="py-6 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-wrap gap-3 mb-4">
                <a href="{{ route('premium.cases.index') }}" class="px-5 py-2 {{ !request('category') ? 'gradient-bg text-white' : 'bg-gray-100' }} rounded-lg font-semibold">全部</a>
                <a href="{{ route('premium.cases.index', ['category' => 'online']) }}" class="px-5 py-2 {{ request('category') === 'online' ? 'gradient-bg text-white' : 'bg-gray-100' }} rounded-lg font-semibold">💻 线上</a>
                <a href="{{ route('premium.cases.index', ['category' => 'offline']) }}" class="px-5 py-2 {{ request('category') === 'offline' ? 'gradient-bg text-white' : 'bg-gray-100' }} rounded-lg font-semibold">🏪 线下</a>
            </div>
            <div class="flex justify-between items-center">
                <div>共 <span class="text-purple-600 font-semibold">{{ $cases->total() }}</span> 个案例</div>
                <div class="flex gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'income']) }}" class="px-4 py-2 {{ request('sort') === 'income' ? 'gradient-bg text-white' : 'bg-gray-100' }} rounded-lg text-sm font-semibold">💰 收入</a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" class="px-4 py-2 {{ request('sort') === 'popular' ? 'gradient-bg text-white' : 'bg-gray-100' }} rounded-lg text-sm font-semibold">🔥 最热</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($cases as $case)
                    <a href="{{ route('premium.cases.show', $case->slug) }}" class="block">
                        <div class="bg-white rounded-2xl shadow-lg card-hover p-6">
                            <h3 class="text-xl font-bold mb-3 line-clamp-2">{{ $case->title }}</h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $case->summary }}</p>
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div class="text-center p-2 bg-gray-50 rounded-lg"><div class="text-xs text-gray-500">成本</div><div class="text-sm font-bold">{{ $case->startup_cost }}</div></div>
                                <div class="text-center p-2 bg-gray-50 rounded-lg"><div class="text-xs text-gray-500">月收入</div><div class="text-sm font-bold text-green-600">¥{{ number_format($case->estimated_income) }}</div></div>
                                <div class="text-center p-2 bg-gray-50 rounded-lg"><div class="text-xs text-gray-500">难度</div><div class="text-sm font-bold">{{ $case->difficulty ?? '入门' }}</div></div>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500 pt-4 border-t">
                                <span>👁 {{ $case->view_count }}</span>
                                <span class="text-purple-600">查看详情 →</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20"><div class="text-6xl mb-4">📭</div><p>暂无案例</p></div>
                @endforelse
            </div>
            @if($cases->hasPages())<div class="mt-12">{{ $cases->links() }}</div>@endif
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
CASEINDEX

# 创建案例详情页（简化版）
docker exec $CONTAINER cat > $VIEWS/cases/show.blade.php << 'CASESHOW'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{{ $case->title }} - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'cases'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <h1 class="text-4xl font-bold mb-4">{{ $case->title }}</h1>
                <div class="flex items-center gap-6 text-gray-600 mb-6">
                    <span>👁 {{ $case->view_count }}</span>
                    <span>👍 {{ $case->like_count }}</span>
                    <span>📅 {{ $case->created_at->format('Y-m-d') }}</span>
                </div>
                <div class="prose max-w-none">{!! $case->content !!}</div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
CASESHOW

echo "✅ 副业案例视图创建完成"

# ============================================
# 步骤 4：创建 AI 工具变现视图
# ============================================
echo "4️⃣ 创建 AI 工具变现视图..."

docker exec $CONTAINER cat > $VIEWS/tools/index.blade.php << 'TOOLSINDEX'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>AI 工具变现地图 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'tools'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="inline-block px-6 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mb-6">🛠️ 部分 VIP</span>
            <h1 class="text-5xl md:text-6xl font-bold mb-6"><span class="gradient-bg bg-clip-text text-transparent">AI 工具变现地图</span></h1>
            <p class="text-xl text-gray-600 mb-8">20+ AI 工具变现场景、定价参考、接单渠道</p>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($tools as $tool)
                    <a href="{{ route('premium.tools.show', $tool->slug) }}" class="block">
                        <div class="bg-white rounded-2xl shadow-lg card-hover p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center text-2xl">🛠️</div>
                                <div>
                                    <h3 class="text-xl font-bold">{{ $tool->tool_name }}</h3>
                                    <div class="text-sm text-gray-500">{{ $tool->category_name }}</div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $tool->summary }}</p>
                            <div class="flex items-center justify-between text-sm">
                                <span class="{{ $tool->available_in_china ? 'text-green-600' : 'text-gray-400' }}">{{ $tool->available_in_china ? '✅ 国内可用' : '❌ 需梯子' }}</span>
                                <span class="text-purple-600">详情 →</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20"><div class="text-6xl mb-4">📭</div><p>暂无工具</p></div>
                @endforelse
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
TOOLSINDEX

docker exec $CONTAINER cat > $VIEWS/tools/show.blade.php << 'TOOLSSHOW'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{{ $tool->tool_name }} - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'tools'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h1 class="text-4xl font-bold mb-4">{{ $tool->tool_name }}</h1>
                <div class="prose max-w-none">{!! $tool->content !!}</div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
TOOLSSHOW

echo "✅ AI 工具变现视图创建完成"

# ============================================
# 步骤 5：创建运营 SOP 视图
# ============================================
echo "5️⃣ 创建运营 SOP 视图..."

docker exec $CONTAINER cat > $VIEWS/sops/index.blade.php << 'SOPSINDEX'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>运营 SOP - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'sops'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="inline-block px-6 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mb-6">📝 VIP 专属</span>
            <h1 class="text-5xl md:text-6xl font-bold mb-6"><span class="gradient-bg bg-clip-text text-transparent">私域运营 SOP</span></h1>
            <p class="text-xl text-gray-600 mb-8">10+ 完整运营流程、检查清单、话术模板</p>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8">
                @forelse($sops as $sop)
                    <a href="{{ route('premium.sops.show', $sop->slug) }}" class="block">
                        <div class="bg-white rounded-2xl shadow-lg card-hover p-6">
                            <h3 class="text-2xl font-bold mb-3">{{ $sop->title }}</h3>
                            <p class="text-gray-600 mb-4">{{ $sop->summary }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span>📱 {{ $sop->platform_name }}</span>
                                <span>📊 {{ $sop->type_name }}</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20"><div class="text-6xl mb-4">📭</div><p>暂无 SOP</p></div>
                @endforelse
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
SOPSINDEX

docker exec $CONTAINER cat > $VIEWS/sops/show.blade.php << 'SOPSSHOW'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{{ $sop->title }} - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'sops'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h1 class="text-4xl font-bold mb-4">{{ $sop->title }}</h1>
                <div class="prose max-w-none">{!! $sop->content !!}</div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
SOPSSHOW

echo "✅ 运营 SOP 视图创建完成"

# ============================================
# 步骤 6：创建付费资源视图
# ============================================
echo "6️⃣ 创建付费资源视图..."

docker exec $CONTAINER cat > $VIEWS/resources/index.blade.php << 'RESOURCESINDEX'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>付费资源 - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'resources'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="inline-block px-6 py-2 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold mb-6">📦 VIP 专属</span>
            <h1 class="text-5xl md:text-6xl font-bold mb-6"><span class="gradient-bg bg-clip-text text-transparent">付费资源合集</span></h1>
            <p class="text-xl text-gray-600 mb-8">课程笔记、行业报告、模板工具包</p>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($resources as $resource)
                    <a href="{{ route('premium.resources.show', $resource->slug) }}" class="block">
                        <div class="bg-white rounded-2xl shadow-lg card-hover p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 gradient-bg rounded-xl flex items-center justify-center text-2xl">
                                    @if($resource->type === 'pdf')📄
                                    @elseif($resource->type === 'video')🎥
                                    @elseif($resource->type === 'cloud_drive')☁️
                                    @else📚
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">{{ $resource->title }}</h3>
                                    <div class="text-sm text-gray-500">{{ $resource->type_name }}</div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-sm mb-4">{{ $resource->summary }}</p>
                            <div class="flex items-center justify-between text-sm">
                                <span>⬇️ {{ $resource->download_count }} 次</span>
                                <span class="text-purple-600">详情 →</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-20"><div class="text-6xl mb-4">📭</div><p>暂无资源</p></div>
                @endforelse
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
RESOURCESINDEX

docker exec $CONTAINER cat > $VIEWS/resources/show.blade.php << 'RESOURCESSHOW'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>{{ $resource->title }} - OpenClaw 智信</title>
    @include('max.partials.head')
</head>
<body class="bg-gray-50">
    @php $currentPage = 'resources'; @endphp
    @include('max.partials.nav')

    <section class="pt-32 pb-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h1 class="text-4xl font-bold mb-4">{{ $resource->title }}</h1>
                @if($resource->download_link)
                    <a href="{{ $resource->download_link }}" class="inline-block gradient-bg text-white px-8 py-4 rounded-full font-semibold mb-6">⬇️ 立即下载</a>
                @endif
                <div class="prose max-w-none">{!! $resource->content !!}</div>
            </div>
        </div>
    </section>

    @include('max.partials.footer')
</body>
</html>
RESOURCESSHOW

echo "✅ 付费资源视图创建完成"

# ============================================
# 步骤 7：注册路由
# ============================================
echo ""
echo "7️⃣ 注册路由..."
if ! docker exec $CONTAINER test -f $ROUTES/premium.php; then
    docker cp routes/premium.php $CONTAINER:$ROUTES/premium.php
    docker exec $CONTAINER sh -c "grep -q \"require __DIR__.'/premium.php'\" $ROUTES/web.php || echo \"require __DIR__.'/premium.php';\" >> $ROUTES/web.php"
    echo "✅ 路由注册完成"
else
    echo "⚠️ 路由文件已存在"
fi

# ============================================
# 步骤 8：清除缓存
# ============================================
echo ""
echo "8️⃣ 清除缓存..."
docker exec $CONTAINER php artisan view:clear
docker exec $CONTAINER php artisan cache:clear
docker exec $CONTAINER php artisan config:clear
docker exec $CONTAINER php artisan route:clear
echo "✅ 缓存清除完成"

# ============================================
# 完成
# ============================================
echo ""
echo "✅ Premium 内容功能部署完成！"
echo ""
echo "📋 访问地址："
echo "  - 副业案例：http://127.0.0.1:8082/premium/cases"
echo "  - AI 工具变现：http://127.0.0.1:8082/premium/tools"
echo "  - 运营 SOP: http://127.0.0.1:8082/premium/sops"
echo "  - 付费资源：http://127.0.0.1:8082/premium/resources"
echo ""
echo "⚠️ 注意：需要登录 VIP 会员才能访问完整内容"
