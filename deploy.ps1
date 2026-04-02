# AI 副业情报局 MAX - 一键部署脚本
# 版本：v1.2 (最简版)

Write-Host "========================================"
Write-Host "AI 副业情报局 MAX - 部署脚本"
Write-Host "========================================"
Write-Host ""

# 配置
$CONTAINER = "ai-side-laravel-max"
$PORT = "8082"
$PATH = "D:\lewan\openclaw-data\workspace\ai-side-laravel-max"

# 1. 检查 Docker
Write-Host "1. 检查 Docker..."
docker --version
if ($LASTEXITCODE -ne 0) {
    Write-Host "错误：Docker 未安装"
    exit 1
}
Write-Host "Docker 正常"
Write-Host ""

# 2. 检查容器
Write-Host "2. 检查容器..."
$exists = docker ps -a --format "{{.Names}}" | Select-String "^${CONTAINER}$"
if ($exists) {
    Write-Host "容器已存在，是否删除？(y/n)"
    $ans = Read-Host
    if ($ans -eq "y") {
        docker stop $CONTAINER
        docker rm $CONTAINER
        Write-Host "已删除"
    } else {
        Write-Host "退出"
        exit 0
    }
}
Write-Host ""

# 3. 创建数据库
Write-Host "3. 创建数据库..."
$pwd = Read-Host "MySQL 密码"
mysql -u root -p$pwd -e "CREATE DATABASE IF NOT EXISTS ai-side-laravel-max DEFAULT CHARACTER SET utf8mb4;"
Write-Host "数据库创建完成"
Write-Host ""

# 4. 创建容器
Write-Host "4. 创建容器..."
docker run -d --name $CONTAINER -p ${PORT}:80 -v "${PATH}:/var/www/html" -e APACHE_DOCUMENTROOT=/var/www/html/public laravelsail/php82-composer:latest
Write-Host "容器创建完成"
Write-Host ""

# 5. 等待
Write-Host "5. 等待容器启动..."
Start-Sleep -Seconds 3
Write-Host "完成"
Write-Host ""

# 6. 安装依赖
Write-Host "6. 安装依赖..."
docker exec $CONTAINER bash -c "cd /var/www/html && composer install --no-dev --optimize-autoloader"
Write-Host "依赖安装完成"
Write-Host ""

# 7. 生成密钥
Write-Host "7. 生成密钥..."
docker exec $CONTAINER bash -c "cd /var/www/html && php artisan key:generate"
Write-Host "密钥生成完成"
Write-Host ""

# 8. 设置权限
Write-Host "8. 设置权限..."
docker exec $CONTAINER bash -c "cd /var/www/html && chmod -R 775 storage/ bootstrap/cache/"
Write-Host "权限设置完成"
Write-Host ""

# 9. 优化缓存
Write-Host "9. 优化缓存..."
docker exec $CONTAINER bash -c "cd /var/www/html && php artisan config:cache && php artisan route:cache && php artisan view:cache"
Write-Host "缓存优化完成"
Write-Host ""

# 10. 完成
Write-Host "========================================"
Write-Host "部署完成！"
Write-Host "========================================"
Write-Host ""
Write-Host "访问地址：http://localhost:${PORT}/max"
Write-Host ""
Write-Host "下一步："
Write-Host "1. 编辑 .env 文件配置数据库"
Write-Host "2. 运行：docker exec -it ${CONTAINER} bash -c 'php artisan migrate'"
Write-Host ""
