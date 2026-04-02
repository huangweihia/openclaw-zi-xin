<?php
/**
 * MAX 项目 - 缓存清除工具
 * 访问：http://127.0.0.1:8082/max-clear-cache.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>清除缓存 - MAX 项目</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #667eea; }
        .success { color: green; }
        .error { color: red; }
        .step { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 8px; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🔄 MAX 项目 - 缓存清除工具</h1>
    
    <?php
    try {
        if (!file_exists(__DIR__.'/vendor/autoload.php')) {
            throw new Exception('找不到 vendor/autoload.php，请确认文件位置正确');
        }
        
        require __DIR__.'/vendor/autoload.php';
        
        if (!file_exists(__DIR__.'/bootstrap/app.php')) {
            throw new Exception('找不到 bootstrap/app.php');
        }
        
        $app = require_once __DIR__.'/bootstrap/app.php';
        $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
        $kernel->bootstrap();
        
        echo '<div class="step">';
        echo '<h2>开始清除缓存...</h2>';
        
        // 1. 清除配置缓存
        echo '<p><strong>1. 清除配置缓存...</strong></p>';
        $exitCode = Illuminate\Support\Facades\Artisan::call('config:clear');
        echo '<pre>' . htmlspecialchars(Illuminate\Support\Facades\Artisan::output()) . '</pre>';
        echo $exitCode === 0 ? '<p class="success">✅ 成功</p>' : '<p class="error">❌ 失败</p>';
        
        // 2. 清除路由缓存
        echo '<p><strong>2. 清除路由缓存...</strong></p>';
        $exitCode = Illuminate\Support\Facades\Artisan::call('route:clear');
        echo '<pre>' . htmlspecialchars(Illuminate\Support\Facades\Artisan::output()) . '</pre>';
        echo $exitCode === 0 ? '<p class="success">✅ 成功</p>' : '<p class="error">❌ 失败</p>';
        
        // 3. 清除视图缓存
        echo '<p><strong>3. 清除视图缓存...</strong></p>';
        $exitCode = Illuminate\Support\Facades\Artisan::call('view:clear');
        echo '<pre>' . htmlspecialchars(Illuminate\Support\Facades\Artisan::output()) . '</pre>';
        echo $exitCode === 0 ? '<p class="success">✅ 成功</p>' : '<p class="error">❌ 失败</p>';
        
        // 4. 清除所有缓存
        echo '<p><strong>4. 清除所有缓存...</strong></p>';
        $exitCode = Illuminate\Support\Facades\Artisan::call('cache:clear');
        echo '<pre>' . htmlspecialchars(Illuminate\Support\Facades\Artisan::output()) . '</pre>';
        echo $exitCode === 0 ? '<p class="success">✅ 成功</p>' : '<p class="error">❌ 失败</p>';
        
        echo '</div>';
        
        echo '<div class="step">';
        echo '<h2 class="success">✅ 缓存清除完成！</h2>';
        echo '<p><strong>请按以下步骤操作：</strong></p>';
        echo '<ol>';
        echo '<li>关闭所有浏览器标签页</li>';
        echo '<li>按 <code>Ctrl + Shift + R</code> 强制刷新</li>';
        echo '<li>访问 <a href="/" style="color: #667eea;">首页</a> 查看新版本</li>';
        echo '<li>验证首页标题是否为：<strong>"用 AI 搞副业，30 天多赚 5000+"</strong></li>';
        echo '</ol>';
        echo '</div>';
        
        echo '<div class="step">';
        echo '<h3>📋 验证清单</h3>';
        echo '<ul>';
        echo '<li>✅ 首页大标题：<strong>"用 AI 搞副业，30 天多赚 5000+"</strong></li>';
        echo '<li>✅ 紫色渐变背景设计</li>';
        echo '<li>✅ 有用户评价卡片</li>';
        echo '<li>✅ 有价格方案对比</li>';
        echo '<li>❌ 不是旧版："每天 10 分钟，发现 AI 副业机会"</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<hr>';
        echo '<p style="color: #999; font-size: 12px;">⚠️ 提示：使用后建议删除此文件以保证安全</p>';
        
    } catch (Exception $e) {
        echo '<div class="step">';
        echo '<h2 class="error">❌ 错误</h2>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p>请检查文件路径或联系管理员</p>';
        echo '</div>';
    }
    ?>
</body>
</html>
