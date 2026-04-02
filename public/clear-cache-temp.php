<?php
/**
 * 临时缓存清除工具
 * 使用后请删除此文件！
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔄 Laravel 缓存清除工具</h1>";
echo "<hr>";

try {
    if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
        throw new Exception('找不到 vendor/autoload.php');
    }
    
    require __DIR__.'/../vendor/autoload.php';
    
    if (!file_exists(__DIR__.'/../bootstrap/app.php')) {
        throw new Exception('找不到 bootstrap/app.php');
    }
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    
    echo "<h2>开始清除缓存...</h2>";
    
    echo "<p>1. 清除配置缓存...</p>";
    $exitCode = Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    echo $exitCode === 0 ? "✅ 成功" : "❌ 失败";
    echo "<br><br>";
    
    echo "<p>2. 清除路由缓存...</p>";
    $exitCode = Illuminate\Support\Facades\Artisan::call('route:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    echo $exitCode === 0 ? "✅ 成功" : "❌ 失败";
    echo "<br><br>";
    
    echo "<p>3. 清除视图缓存...</p>";
    $exitCode = Illuminate\Support\Facades\Artisan::call('view:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    echo $exitCode === 0 ? "✅ 成功" : "❌ 失败";
    echo "<br><br>";
    
    echo "<p>4. 清除所有缓存...</p>";
    $exitCode = Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    echo $exitCode === 0 ? "✅ 成功" : "❌ 失败";
    echo "<br><br>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ 缓存清除完成！</h2>";
    echo "<p><strong>请刷新页面查看新版本（Ctrl+Shift+R）</strong></p>";
    echo "<p><a href='/'>← 返回首页</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ 错误</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

echo "<hr>";
echo "<p style='color: #999; font-size: 12px;'>使用后请删除此文件</p>";
