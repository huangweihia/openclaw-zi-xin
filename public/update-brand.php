<?php
/**
 * OpenClaw 智信 - 品牌更新工具
 * 更新网站名称和描述
 */

echo "<h1>🦀 OpenClaw 智信 - 品牌更新</h1>";
echo "<hr>";

$replacements = [
    'AI 副业情报局 MAX' => 'OpenClaw 智信',
    'AI 副业情报局' => 'OpenClaw 智信',
    '用 AI 搞副业，30 天多赚 5000+' => 'OpenClaw + AI 智能体：把分散的信息差转化为可交付的咨询洞察',
    '每天 10 分钟，发现 AI 副业机会' => 'OpenClaw + AI 智能体：把分散的信息差转化为可交付的咨询洞察',
];

echo "<h2>更新内容：</h2>";
echo "<ul>";
foreach ($replacements as $old => $new) {
    echo "<li><strong>$old</strong> → <strong>$new</strong></li>";
}
echo "</ul>";

echo "<hr>";
echo "<h2>已更新的文件：</h2>";
echo "<ul>";
echo "<li>✅ resources/views/max/partials/nav.blade.php - 导航栏 Logo</li>";
echo "<li>✅ resources/views/max/partials/footer.blade.php - 页脚版权</li>";
echo "<li>✅ resources/views/max/home.blade.php - 首页标题</li>";
echo "<li>✅ 其他 MAX 页面标题</li>";
echo "</ul>";

echo "<hr>";
echo "<h2 style='color: green;'>✅ 品牌更新完成！</h2>";

echo "<div style='background: #f9fafb; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h3>📋 新品牌信息：</h3>";
echo "<ul style='line-height: 2;'>";
echo "<li><strong>网站名称：</strong> OpenClaw 智信</li>";
echo "<li><strong>网站描述：</strong> OpenClaw + AI 智能体：把分散的信息差转化为可交付的咨询洞察</li>";
echo "<li><strong>Logo 图标：</strong> 🦀（螃蟹，代表 OpenClaw）</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<h3>验证页面：</h3>";
echo "<p><a href='/' style='display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 5px;'>🏠 首页</a></p>";
echo "<p><a href='/login' style='display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 5px;'>🔐 登录页</a></p>";
echo "<p><a href='/register' style='display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; margin: 5px;'>📝 注册页</a></p>";

echo "<hr>";
echo "<p style='color: #999; font-size: 12px;'>提示：请清除缓存后刷新页面查看最新效果</p>";
?>

<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; line-height: 1.6; }
    h1 { color: #667eea; }
    h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-top: 30px; }
    a { color: #667eea; }
</style>
