<?php
/**
 * 清除缓存并验证视图文件
 */

echo "<h1>🔄 清除缓存 + 验证视图</h1>";
echo "<hr>";

// 检查视图文件
echo "<h2>1. 检查视图文件</h2>";
$viewPaths = [
    'max.auth.login' => resources_path('views/max/auth/login.blade.php'),
    'max.auth.register' => resources_path('views/max/auth/register.blade.php'),
    'max.home' => resources_path('views/max/home.blade.php'),
];

foreach ($viewPaths as $name => $path) {
    $exists = file_exists($path);
    echo "<p>" . ($exists ? '✅' : '❌') . " $name: " . ($exists ? '存在' : '不存在') . "</p>";
}

echo "<hr>";
echo "<h2>2. 清除缓存</h2>";

try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();
    
    $commands = ['view:clear', 'cache:clear', 'config:clear', 'route:clear'];
    foreach ($commands as $command) {
        echo "<p>执行：$command</p>";
        $exitCode = Illuminate\Support\Facades\Artisan::call($command);
        echo "<pre>" . htmlspecialchars(Illuminate\Support\Facades\Artisan::output()) . "</pre>";
        echo $exitCode === 0 ? "✅ 成功<br><br>" : "❌ 失败<br><br>";
    }
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ 完成！</h2>";
    echo "<p><a href='/login'>← 测试登录页</a> | <a href='/register'>测试注册页</a> | <a href='/'>返回首页</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ 错误</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
