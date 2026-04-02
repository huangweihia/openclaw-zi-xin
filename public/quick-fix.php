<?php
/**
 * 快速修复 - 清除缓存
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔄 清除缓存</h1>";
echo "<hr>";

try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    
    echo "<p><strong>清除视图缓存...</strong></p>";
    $exitCode = Illuminate\Support\Facades\Artisan::call('view:clear');
    echo '<pre>' . htmlspecialchars(Illuminate\Support\Facades\Artisan::output()) . '</pre>';
    echo $exitCode === 0 ? '<p style="color: green;">✅ 成功</p>' : '<p style="color: red;">❌ 失败</p>';
    
    echo "<p><strong>清除所有缓存...</strong></p>";
    $exitCode = Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo '<pre>' . htmlspecialchars(Illuminate\Support\Facades\Artisan::output()) . '</pre>';
    echo $exitCode === 0 ? '<p style="color: green;">✅ 成功</p>' : '<p style="color: red;">❌ 失败</p>';
    
    echo "<hr>";
    echo '<h2 style="color: green;">✅ 缓存清除完成！</h2>';
    echo '<p><a href="/" class="button" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 5px;">🏠 返回首页</a></p>';
    echo '<p><a href="/login" class="button" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 5px;">🔐 登录页</a></p>';
    echo '<p><a href="/register" class="button" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 5px;">📝 注册页</a></p>';
    
} catch (Exception $e) {
    echo '<h2 style="color: red;">❌ 错误</h2>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}
?>
<style>
    body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; }
</style>
