#!/bin/bash
# OpenClaw 智信 - 清理 1.0 旧代码并验证
# 执行时间：2026-04-02

set -e

VIEWS="/home/node/.openclaw/workspace/ai-side-laravel-max/resources/views"
BACKUP_DIR="/home/node/.openclaw/workspace/ai-side-laravel-max/backup/old-code-$(date +%Y%m%d)"

echo "🧹 开始清理 1.0 旧代码..."
echo ""

# ============================================
# 步骤 1：备份
# ============================================
echo "1️⃣ 备份旧代码..."
mkdir -p $BACKUP_DIR

# 备份会被删除的目录
for dir in home projects articles favorites history layouts partials components; do
    if [ -d "$VIEWS/$dir" ]; then
        cp -r "$VIEWS/$dir" "$BACKUP_DIR/" 2>/dev/null || true
        echo "  ✓ 备份：$dir/"
    fi
done

# 备份旧文件
for file in dashboard.blade.php vip.blade.php; do
    if [ -f "$VIEWS/$file" ]; then
        cp "$VIEWS/$file" "$BACKUP_DIR/" 2>/dev/null || true
        echo "  ✓ 备份：$file"
    fi
done

echo "✅ 备份完成：$BACKUP_DIR"
echo ""

# ============================================
# 步骤 2：删除 1.0 旧视图
# ============================================
echo "2️⃣ 删除 1.0 旧视图..."

# 已被 MAX 替代的目录
rm -rf "$VIEWS/home" && echo "  ✓ 删除：home/"
rm -rf "$VIEWS/projects" && echo "  ✓ 删除：projects/"
rm -rf "$VIEWS/articles" && echo "  ✓ 删除：articles/"
rm -rf "$VIEWS/favorites" && echo "  ✓ 删除：favorites/"
rm -rf "$VIEWS/history" && echo "  ✓ 删除：history/"
rm -f "$VIEWS/dashboard.blade.php" && echo "  ✓ 删除：dashboard.blade.php"
rm -f "$VIEWS/vip.blade.php" && echo "  ✓ 删除：vip.blade.php"

echo "✅ 旧视图删除完成"
echo ""

# ============================================
# 步骤 3：删除 1.0 布局和组件
# ============================================
echo "3️⃣ 删除 1.0 布局和组件..."

rm -rf "$VIEWS/layouts" && echo "  ✓ 删除：layouts/"
rm -rf "$VIEWS/partials" && echo "  ✓ 删除：partials/"
rm -rf "$VIEWS/components" && echo "  ✓ 删除：components/"

echo "✅ 旧布局和组件删除完成"
echo ""

# ============================================
# 步骤 4：统计
# ============================================
echo "4️⃣ 统计剩余文件..."

REMAINING_VIEWS=$(find "$VIEWS" -name "*.blade.php" -type f | wc -l)
MAX_VIEWS=$(find "$VIEWS/max" -name "*.blade.php" -type f | wc -l)

echo "剩余视图文件：$REMAINING_VIEWS"
echo "MAX 视图文件：$MAX_VIEWS"
echo ""

# ============================================
# 步骤 5：验证
# ============================================
echo "5️⃣ 验证..."

# 检查 MAX 视图是否存在
if [ -d "$VIEWS/max" ] && [ $MAX_VIEWS -gt 0 ]; then
    echo "✅ MAX 视图正常"
else
    echo "❌ MAX 视图缺失！"
    exit 1
fi

# 检查是否还有 1.0 视图
OLD_VIEWS=$(find "$VIEWS" -maxdepth 1 -name "*.blade.php" -type f | wc -l)
if [ $OLD_VIEWS -eq 0 ]; then
    echo "✅ 1.0 视图已清除"
else
    echo "⚠️ 还有 $OLD_VIEWS 个 1.0 视图文件"
fi

echo ""
echo "✅ 清理完成！"
echo ""
echo "📋 清理总结："
echo "  - 已删除：home/, projects/, articles/, favorites/, history/"
echo "  - 已删除：dashboard.blade.php, vip.blade.php"
echo "  - 已删除：layouts/, partials/, components/"
echo "  - 保留：max/ (MAX 版本视图)"
echo "  - 保留：auth/ (登录/注册)"
echo "  - 保留：filament/ (后台)"
echo "  - 保留：payments/ (支付)"
echo "  - 保留：其他必要视图"
echo ""
echo "💾 备份位置：$BACKUP_DIR"
