#!/bin/bash

###############################################################################
# AI 副业情报局 MAX - 快速验证脚本
# 用途：验证部署是否成功
###############################################################################

PORT="8082"
CONTAINER_NAME="ai-side-laravel-max"

echo "========================================================================"
echo "           AI 副业情报局 MAX - 快速验证"
echo "========================================================================"
echo

# 1. 检查容器状态
echo "1. 检查容器状态..."
if docker ps --format '{{.Names}}\t{{.Status}}' | grep -q "^${CONTAINER_NAME}"; then
    echo "   ✅ 容器运行正常"
else
    echo "   ❌ 容器未运行"
    echo "   提示：docker start ${CONTAINER_NAME}"
    exit 1
fi

# 2. 检查端口
echo "2. 检查端口 ${PORT}..."
if netstat -tuln 2>/dev/null | grep -q ":${PORT}"; then
    echo "   ✅ 端口已监听"
else
    echo "   ⚠️  端口未监听，请检查防火墙"
fi

# 3. 测试访问
echo "3. 测试页面访问..."
urls=(
    "http://localhost:${PORT}/max"
    "http://localhost:${PORT}/max/vip"
    "http://localhost:${PORT}/max/pricing"
    "http://localhost:${PORT}/max/cases"
)

for url in "${urls[@]}"; do
    response=$(curl -s -o /dev/null -w "%{http_code}" "${url}" 2>/dev/null || echo "000")
    
    if [ "$response" = "200" ]; then
        echo "   ✅ ${url} (HTTP ${response})"
    else
        echo "   ❌ ${url} (HTTP ${response})"
    fi
done

# 4. 检查数据库连接
echo "4. 检查数据库连接..."
docker exec ${CONTAINER_NAME} bash -c "
    cd /var/www/html &&
    php artisan tinker --execute='DB::connection()->getPdo(); echo \"OK\";'
" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "   ✅ 数据库连接正常"
else
    echo "   ⚠️  数据库连接失败，请检查 .env 配置"
fi

# 5. 检查表数量
echo "5. 检查数据表..."
table_count=$(docker exec ${CONTAINER_NAME} bash -c "
    cd /var/www/html &&
    php artisan tinker --execute='echo DB::select(\"SHOW TABLES\");'
" 2>/dev/null | grep -o 'stdClass' | wc -l)

if [ "$table_count" -gt 30 ]; then
    echo "   ✅ 数据表正常 (${table_count} 个)"
elif [ "$table_count" -gt 0 ]; then
    echo "   ⚠️  数据表不完整 (${table_count}/31 个)"
else
    echo "   ❌ 无法获取数据表，请运行迁移"
fi

echo
echo "========================================================================"
echo "验证完成！"
echo "========================================================================"
echo
echo "访问地址：http://localhost:${PORT}/max"
echo
