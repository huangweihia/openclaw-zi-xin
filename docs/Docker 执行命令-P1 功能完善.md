# 🐳 Docker 容器内执行命令

> 更新时间：2026-04-02 14:45  
> 容器名：`ai_side_php_max`

---

## 📋 P1 功能完善 - Docker 命令

### 步骤 1：修改文件权限

```bash
docker exec ai_side_php_max chmod 666 /var/www/html/resources/views/max/articles/show.blade.php
docker exec ai_side_php_max chmod 666 /var/www/html/resources/views/max/projects/show.blade.php
```

---

### 步骤 2：添加阅读进度条到文章详情页

```bash
# 2.1 添加进度条 HTML（在 nav 之后）
docker exec ai_side_php_max sed -i '/@include.*partials.*nav/a\
    <!-- 阅读进度条 -->\
    <div class="fixed top-16 left-0 w-full h-1 bg-gray-200 z-40">\
        <div id="reading-progress" class="h-full gradient-bg transition-all duration-100" style="width: 0%"></div>\
    </div>' /var/www/html/resources/views/max/articles/show.blade.php

# 2.2 添加 JavaScript（在</body>之前）
docker exec ai_side_php_max sed -i '/<\/body>/i\
    <script>\
    window.addEventListener("scroll", function() {\
        const articleContent = document.getElementById("article-content");\
        if (!articleContent) return;\
        const articleTop = articleContent.offsetTop;\
        const articleHeight = articleContent.offsetHeight;\
        const windowHeight = window.innerHeight;\
        const scrollTop = window.scrollY;\
        const progress = Math.min(100, Math.max(0,\
            ((scrollTop - articleTop + windowHeight) / articleHeight) * 100\
        ));\
        document.getElementById("reading-progress").style.width = progress + "%";\
    });\
    </script>' /var/www/html/resources/views/max/articles/show.blade.php
```

---

### 步骤 3：添加变现分析到项目详情页

```bash
# 3.1 添加变现分析模块（在基本信息之后）
docker exec ai_side_php_max sed -i '/<\/div>.*项目基本信息/a\
\
            <!-- 变现分析 -->\
            @if($project->monetization)\
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">\
                <h2 class="text-2xl font-bold text-gray-900 mb-6">💰 变现分析</h2>\
                <div class="bg-purple-50 rounded-xl p-6 mb-6">\
                    <p class="text-purple-800 font-semibold text-lg">{{ $project->monetization }}</p>\
                </div>\
                <div class="grid md:grid-cols-3 gap-4">\
                    <div class="bg-gray-50 rounded-xl p-4">\
                        <div class="text-sm text-gray-500 mb-2">启动成本</div>\
                        <div class="text-lg font-bold text-gray-900">{{ $project->startup_cost ?? '"'"'低'"'"' }}</div>\
                    </div>\
                    <div class="bg-gray-50 rounded-xl p-4">\
                        <div class="text-sm text-gray-500 mb-2">月收入预估</div>\
                        <div class="text-lg font-bold text-gray-900">{{ $project->income_range ?? '"'"'1000-5000 元'"'"' }}</div>\
                    </div>\
                    <div class="bg-gray-50 rounded-xl p-4">\
                        <div class="text-sm text-gray-500 mb-2">难度等级</div>\
                        <div class="text-lg font-bold text-gray-900">{{ $project->difficulty ?? '"'"'入门级'"'"' }}</div>\
                    </div>\
                </div>\
            </div>\
            @endif' /var/www/html/resources/views/max/projects/show.blade.php
```

---

### 步骤 4：清除缓存

```bash
# 清除视图缓存
docker exec ai_side_php_max php artisan view:clear

# 清除所有缓存
docker exec ai_side_php_max php artisan cache:clear

# 清除配置缓存
docker exec ai_side_php_max php artisan config:clear

# 清除路由缓存
docker exec ai_side_php_max php artisan route:clear
```

---

## 🚀 一键执行脚本

```bash
#!/bin/bash
# P1 功能完善 - 一键执行脚本

CONTAINER="ai_side_php_max"
VIEWS="/var/www/html/resources/views/max"

echo "🔧 开始 P1 功能完善..."

# 1. 修改权限
echo "1️⃣ 修改文件权限..."
docker exec $CONTAINER chmod 666 $VIEWS/articles/show.blade.php
docker exec $CONTAINER chmod 666 $VIEWS/projects/show.blade.php

# 2. 添加阅读进度条
echo "2️⃣ 添加阅读进度条..."
docker exec $CONTAINER sed -i '/@include.*partials.*nav/a\
    <!-- 阅读进度条 -->\
    <div class="fixed top-16 left-0 w-full h-1 bg-gray-200 z-40">\
        <div id="reading-progress" class="h-full gradient-bg transition-all duration-100" style="width: 0%"></div>\
    </div>' $VIEWS/articles/show.blade.php

docker exec $CONTAINER sed -i '/<\/body>/i\
    <script>\
    window.addEventListener("scroll", function() {\
        const articleContent = document.getElementById("article-content");\
        if (!articleContent) return;\
        const articleTop = articleContent.offsetTop;\
        const articleHeight = articleContent.offsetHeight;\
        const windowHeight = window.innerHeight;\
        const scrollTop = window.scrollY;\
        const progress = Math.min(100, Math.max(0,\
            ((scrollTop - articleTop + windowHeight) / articleHeight) * 100\
        ));\
        document.getElementById("reading-progress").style.width = progress + "%";\
    });\
    </script>' $VIEWS/articles/show.blade.php

# 3. 清除缓存
echo "3️⃣ 清除缓存..."
docker exec $CONTAINER php artisan view:clear
docker exec $CONTAINER php artisan cache:clear
docker exec $CONTAINER php artisan config:clear
docker exec $CONTAINER php artisan route:clear

echo "✅ P1 功能完善完成！"
echo ""
echo "📋 验证步骤："
echo "1. 访问首页查看实时动态：http://127.0.0.1:8082/"
echo "2. 访问文章详情页查看阅读进度条：http://127.0.0.1:8082/articles/1"
echo "3. 访问项目详情页查看变现分析：http://127.0.0.1:8082/projects/1"
```

---

## 📊 验证步骤

### 1. 验证首页实时动态 ✅
```bash
# 访问首页
curl -s http://127.0.0.1:8082/ | grep -o "realtime-activities" && echo "✅ 实时动态已添加"
```

### 2. 验证阅读进度条 ⏸️
```bash
# 访问文章详情页
curl -s http://127.0.0.1:8082/articles/1 | grep -o "reading-progress" && echo "✅ 阅读进度条已添加"
```

### 3. 验证实变分析 ⏸️
```bash
# 访问项目详情页
curl -s http://127.0.0.1:8082/projects/1 | grep -o "变现分析" && echo "✅ 变现分析已添加"
```

---

## 🎯 完整执行流程

### 方法 1：逐条执行（推荐）

```bash
# 1. 修改权限
docker exec ai_side_php_max chmod 666 /var/www/html/resources/views/max/articles/show.blade.php

# 2. 添加阅读进度条 HTML
docker exec ai_side_php_max sed -i '/@include.*partials.*nav/a\
    <!-- 阅读进度条 -->\
    <div class="fixed top-16 left-0 w-full h-1 bg-gray-200 z-40">\
        <div id="reading-progress" class="h-full gradient-bg transition-all duration-100" style="width: 0%"></div>\
    </div>' /var/www/html/resources/views/max/articles/show.blade.php

# 3. 添加阅读进度条 JS
docker exec ai_side_php_max sed -i '/<\/body>/i\
    <script>\
    window.addEventListener("scroll", function() {\
        const articleContent = document.getElementById("article-content");\
        if (!articleContent) return;\
        const articleTop = articleContent.offsetTop;\
        const articleHeight = articleContent.offsetHeight;\
        const windowHeight = window.innerHeight;\
        const scrollTop = window.scrollY;\
        const progress = Math.min(100, Math.max(0,\
            ((scrollTop - articleTop + windowHeight) / articleHeight) * 100\
        ));\
        document.getElementById("reading-progress").style.width = progress + "%";\
    });\
    </script>' /var/www/html/resources/views/max/articles/show.blade.php

# 4. 清除缓存
docker exec ai_side_php_max php artisan view:clear
docker exec ai_side_php_max php artisan cache:clear

echo "✅ 完成！请刷新浏览器查看效果"
```

### 方法 2：使用一键脚本

```bash
# 保存上面的脚本为 fix-p1-features.sh
bash fix-p1-features.sh
```

---

## 📝 注意事项

1. **容器名称**：确认容器名为 `ai_side_php_max`
   ```bash
   docker ps | grep php
   ```

2. **文件路径**：容器内路径为 `/var/www/html`

3. **权限问题**：如果 sed 命令失败，先修改权限

4. **缓存清除**：修改后必须清除缓存才能生效

---

_文档生成时间：2026-04-02 14:45_  
_容器：ai_side_php_max_
