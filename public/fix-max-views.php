<?php
/**
 * MAX 项目 - 视图修复工具
 * 遵循原则：组件化、DRY、简洁性、稳定性、可维护性、文档原子性
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAX 项目 - 视图修复工具</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; line-height: 1.6; }
        h1 { color: #667eea; margin-bottom: 10px; }
        h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-top: 30px; }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        .step { background: #f9fafb; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #667eea; }
        .checklist { list-style: none; padding: 0; }
        .checklist li { padding: 8px 0; }
        .checklist li:before { content: "✓ "; color: #10b981; font-weight: bold; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-family: "Courier New", monospace; }
        a { color: #667eea; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 5px; }
        .button:hover { opacity: 0.9; text-decoration: none; }
    </style>
</head>
<body>
    <h1>🔧 MAX 项目 - 视图修复工具</h1>
    <p style="color: #666;">遵循原则：组件化 · DRY · 简洁性 · 稳定性 · 可维护性 · 文档原子性</p>
    
    <?php
    // 定义检查项
    $checks = [
        '视图文件' => [
            'max.home' => 'resources/views/max/home.blade.php',
            'max.auth.login' => 'resources/views/max/auth/login.blade.php',
            'max.auth.register' => 'resources/views/max/auth/register.blade.php',
            'max.projects.index' => 'resources/views/max/projects/index.blade.php',
            'max.articles.index' => 'resources/views/max/articles/index.blade.php',
        ],
        '组件文件' => [
            'max.partials.head' => 'resources/views/max/partials/head.blade.php',
            'max.partials.nav' => 'resources/views/max/partials/nav.blade.php',
            'max.partials.footer' => 'resources/views/max/partials/footer.blade.php',
            'max.partials.mobile-styles' => 'resources/views/max/partials/mobile-styles.blade.php',
        ],
    ];
    
    echo '<div class="step">';
    echo '<h2>1️⃣ 检查视图文件</h2>';
    echo '<ul class="checklist">';
    
    $allExist = true;
    foreach ($checks as $category => $files) {
        echo "<li><strong>$category</strong></li>";
        foreach ($files as $name => $path) {
            $fullPath = __DIR__ . '/../' . $path;
            $exists = file_exists($fullPath);
            if (!$exists) $allExist = false;
            $status = $exists ? '<span class="success">✓</span>' : '<span class="error">✗</span>';
            echo "<li style='margin-left: 20px;'>$status $name</li>";
        }
    }
    echo '</ul>';
    echo '</div>';
    
    if ($allExist) {
        echo '<div class="step">';
        echo '<h2>2️⃣ 清除缓存</h2>';
        
        try {
            require __DIR__.'/../vendor/autoload.php';
            $app = require_once __DIR__.'/../bootstrap/app.php';
            $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
            $kernel->bootstrap();
            
            $commands = [
                'view:clear' => '视图缓存',
                'cache:clear' => '所有缓存',
                'config:clear' => '配置缓存',
                'route:clear' => '路由缓存',
            ];
            
            foreach ($commands as $command => $label) {
                echo "<p><strong>清除 $label...</strong></p>";
                $exitCode = Illuminate\Support\Facades\Artisan::call($command);
                $output = Illuminate\Support\Facades\Artisan::output();
                echo '<pre style="background: #f3f4f6; padding: 10px; border-radius: 4px; overflow-x: auto;">' . htmlspecialchars($output) . '</pre>';
                echo $exitCode === 0 ? '<p class="success">✅ 成功</p>' : '<p class="error">❌ 失败</p>';
                echo '<br>';
            }
            
            echo '<hr>';
            echo '<h2 style="color: #10b981;">✅ 修复完成！</h2>';
            
            echo '<div class="step">';
            echo '<h3>📋 验证清单</h3>';
            echo '<ul class="checklist">';
            echo '<li><a href="/" class="button">🏠 首页</a> - 应显示："用 AI 搞副业，30 天多赚 5000+"</li>';
            echo '<li><a href="/login" class="button">🔐 登录页</a> - MAX 风格简洁设计</li>';
            echo '<li><a href="/register" class="button">📝 注册页</a> - 手机号 + 验证码表单</li>';
            echo '<li><a href="/projects" class="button">🚀 项目库</a> - 紫色渐变设计</li>';
            echo '<li><a href="/articles" class="button">📚 文章库</a> - 卡片式布局</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<hr>';
            echo '<p style="color: #999; font-size: 12px;">⚠️ 提示：使用后建议删除此文件</p>';
            
        } catch (Exception $e) {
            echo '<h2 style="color: #ef4444;">❌ 错误</h2>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<p>请检查文件路径或联系管理员</p>';
        }
    } else {
        echo '<div class="step">';
        echo '<h2 style="color: #ef4444;">❌ 文件缺失</h2>';
        echo '<p>部分视图文件不存在，请先复制文件到正确位置</p>';
        echo '</div>';
    }
    ?>
</body>
</html>
