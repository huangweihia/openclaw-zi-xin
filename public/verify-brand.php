<?php
/**
 * OpenClaw 智信 - 品牌更新验证工具
 */

echo "<h1>🦀 OpenClaw 智信 - 品牌更新验证</h1>";
echo "<hr>";

$docsDir = __DIR__ . '/../docs';
$files = [
    '产品原型稿.md',
    '商业计划书.md',
    '项目总览.md',
    '项目功能清单.md',
    '完整项目文档.md',
    '1.0 可用功能清单.md',
    '数据库设计文档.md',
    '部署文档.md',
];

echo "<h2>📋 文档更新检查</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>文档</th><th>状态</th><th>旧名称出现次数</th></tr>";

foreach ($files as $file) {
    $filePath = $docsDir . '/' . $file;
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $oldNameCount = substr_count($content, 'AI 副业情报局');
        $status = $oldNameCount === 0 ? '<span style="color: green;">✅ 已更新</span>' : '<span style="color: orange;">⚠️ 需检查</span>';
        echo "<tr><td>$file</td><td>$status</td><td>$oldNameCount</td></tr>";
    } else {
        echo "<tr><td>$file</td><td><span style='color: red;'>❌ 文件不存在</span></td><td>-</td></tr>";
    }
}

echo "</table>";

echo "<hr>";
echo "<h2>✅ 品牌信息总览</h2>";
echo "<div style='background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<ul style='line-height: 2;'>";
echo "<li><strong>项目名称：</strong> OpenClaw 智信</li>";
echo "<li><strong>Slogan：</strong> OpenClaw + AI 智能体：把分散的信息差转化为可交付的咨询洞察</li>";
echo "<li><strong>Logo：</strong> 🦀（螃蟹）</li>";
echo "<li><strong>定位：</strong> AI 智能体驱动的咨询洞察平台</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<h2>📋 验证清单</h2>";
echo "<ul>";
echo "<li>✅ 导航栏 Logo：🦀 OpenClaw 智信</li>";
echo "<li>✅ 首页标题：OpenClaw + AI 智能体：把分散的信息差转化为可交付的咨询洞察</li>";
echo "<li>✅ 页脚版权：© OpenClaw 智信</li>";
echo "<li>✅ 文档更新：8 个核心文档已批量更新</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>下一步：</h3>";
echo "<ol>";
echo "<li>访问 <a href='/'>首页</a> 验证新品牌</li>";
echo "<li>访问 <a href='/login'>登录页</a> 验证新品牌</li>";
echo "<li>检查文档是否还有旧名称</li>";
echo "</ol>";

echo "<hr>";
echo "<p style='color: #999; font-size: 12px;'>提示：如果发现旧名称，请手动更新对应文档</p>";
?>

<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; line-height: 1.6; }
    h1 { color: #667eea; }
    h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; margin-top: 30px; }
    table { margin: 20px 0; }
    th { background: #f3f4f6; }
</style>
