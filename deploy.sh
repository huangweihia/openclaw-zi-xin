#!/bin/bash

###############################################################################
# AI 副业情报局 MAX - 一键部署脚本
# 版本：v1.0
# 日期：2026-04-01
# 用途：自动化部署 MAX 版本
###############################################################################

set -e  # 遇到错误立即退出

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 配置变量
CONTAINER_NAME="ai-side-laravel-max"
PORT="8082"
PROJECT_PATH="/home/node/.openclaw/workspace/ai-side-laravel-max"
DB_NAME="ai-side-laravel-max"
DB_USER="root"
DB_PASSWORD=""  # 需要手动填写
IMAGE="laravelsail/php82-composer:latest"

###############################################################################
# 函数：打印信息
###############################################################################
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

###############################################################################
# 函数：检查依赖
###############################################################################
check_dependencies() {
    print_info "检查依赖..."
    
    if ! command -v docker &> /dev/null; then
        print_error "Docker 未安装，请先安装 Docker"
        exit 1
    fi
    
    if ! command -v mysql &> /dev/null; then
        print_warning "MySQL 客户端未安装，跳过数据库检查"
    fi
    
    print_success "依赖检查通过"
}

###############################################################################
# 函数：检查容器是否已存在
###############################################################################
check_container() {
    print_info "检查容器 ${CONTAINER_NAME}..."
    
    if docker ps -a --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
        print_warning "容器 ${CONTAINER_NAME} 已存在"
        read -p "是否删除并重新创建？(y/n): " choice
        case "$choice" in
            y|Y)
                print_info "删除旧容器..."
                docker stop ${CONTAINER_NAME} || true
                docker rm ${CONTAINER_NAME} || true
                print_success "旧容器已删除"
                ;;
            n|N)
                print_info "保留旧容器，退出脚本"
                exit 0
                ;;
            *)
                print_error "无效选择"
                exit 1
                ;;
        esac
    fi
}

###############################################################################
# 函数：创建数据库
###############################################################################
create_database() {
    print_info "创建数据库 ${DB_NAME}..."
    
    if [ -z "$DB_PASSWORD" ]; then
        read -sp "请输入 MySQL root 密码：" DB_PASSWORD
        echo
    fi
    
    if command -v mysql &> /dev/null; then
        mysql -u ${DB_USER} -p${DB_PASSWORD} -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
        
        if [ $? -eq 0 ]; then
            print_success "数据库创建成功"
        else
            print_warning "数据库创建失败，请手动创建"
            print_info "执行以下命令："
            echo "mysql -u root -p -e \"CREATE DATABASE \`${DB_NAME}\` DEFAULT CHARACTER SET utf8mb4;\""
        fi
    else
        print_warning "MySQL 客户端未安装，请手动创建数据库"
        print_info "执行以下命令："
        echo "mysql -u root -p -e \"CREATE DATABASE \`${DB_NAME}\` DEFAULT CHARACTER SET utf8mb4;\""
    fi
}

###############################################################################
# 函数：创建 Docker 容器
###############################################################################
create_container() {
    print_info "创建 Docker 容器..."
    print_info "容器名称：${CONTAINER_NAME}"
    print_info "端口映射：${PORT}:80"
    print_info "挂载路径：${PROJECT_PATH}:/var/www/html"
    
    docker run -d \
        --name ${CONTAINER_NAME} \
        -p ${PORT}:80 \
        -v ${PROJECT_PATH}:/var/www/html \
        -e APACHE_DOCUMENTROOT=/var/www/html/public \
        ${IMAGE}
    
    if [ $? -eq 0 ]; then
        print_success "容器创建成功"
        sleep 2  # 等待容器启动
    else
        print_error "容器创建失败"
        exit 1
    fi
}

###############################################################################
# 函数：安装依赖
###############################################################################
install_dependencies() {
    print_info "安装 PHP 依赖..."
    
    docker exec ${CONTAINER_NAME} bash -c "
        cd /var/www/html &&
        composer install --no-dev --optimize-autoloader
    "
    
    if [ $? -eq 0 ]; then
        print_success "依赖安装成功"
    else
        print_warning "依赖安装失败，请手动执行"
        print_info "docker exec -it ${CONTAINER_NAME} bash -c 'composer install'"
    fi
}

###############################################################################
# 函数：配置环境
###############################################################################
configure_env() {
    print_info "配置环境..."
    
    docker exec ${CONTAINER_NAME} bash -c "
        cd /var/www/html &&
        if [ ! -f .env ]; then
            cp .env.example .env
        fi &&
        php artisan key:generate
    "
    
    if [ $? -eq 0 ]; then
        print_success "环境配置成功"
    else
        print_warning "环境配置失败，请手动配置"
    fi
    
    print_info "请编辑 .env 文件，配置数据库连接信息"
    print_info "文件位置：${PROJECT_PATH}/.env"
}

###############################################################################
# 函数：运行迁移
###############################################################################
run_migrations() {
    print_info "运行数据库迁移..."
    
    read -p "是否现在运行迁移？(y/n): " choice
    case "$choice" in
        y|Y)
            docker exec ${CONTAINER_NAME} bash -c "
                cd /var/www/html &&
                php artisan migrate --force
            "
            
            if [ $? -eq 0 ]; then
                print_success "迁移成功"
            else
                print_error "迁移失败，请检查数据库配置"
                print_info "确保 .env 文件中数据库配置正确"
            fi
            ;;
        n|N)
            print_info "跳过迁移，稍后手动执行"
            print_info "docker exec -it ${CONTAINER_NAME} bash -c 'php artisan migrate'"
            ;;
        *)
            print_error "无效选择"
            ;;
    esac
}

###############################################################################
# 函数：设置权限
###############################################################################
set_permissions() {
    print_info "设置目录权限..."
    
    docker exec ${CONTAINER_NAME} bash -c "
        cd /var/www/html &&
        chmod -R 775 storage/ bootstrap/cache/ &&
        chown -R www-data:www-data storage/ bootstrap/cache/
    "
    
    if [ $? -eq 0 ]; then
        print_success "权限设置成功"
    else
        print_warning "权限设置失败，请手动执行"
    fi
}

###############################################################################
# 函数：优化缓存
###############################################################################
optimize_cache() {
    print_info "优化缓存..."
    
    docker exec ${CONTAINER_NAME} bash -c "
        cd /var/www/html &&
        php artisan config:cache &&
        php artisan route:cache &&
        php artisan view:cache
    "
    
    if [ $? -eq 0 ]; then
        print_success "缓存优化成功"
    else
        print_warning "缓存优化失败，请手动执行"
    fi
}

###############################################################################
# 函数：验证部署
###############################################################################
verify_deployment() {
    print_info "验证部署..."
    
    # 检查容器状态
    if docker ps --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
        print_success "容器运行正常"
    else
        print_error "容器未运行"
        exit 1
    fi
    
    # 检查端口
    if netstat -tuln 2>/dev/null | grep -q ":${PORT}"; then
        print_success "端口 ${PORT} 已监听"
    else
        print_warning "端口 ${PORT} 未监听，请检查防火墙"
    fi
    
    # 测试访问
    print_info "测试访问..."
    sleep 2
    
    if command -v curl &> /dev/null; then
        response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:${PORT}/max 2>/dev/null || echo "000")
        
        if [ "$response" = "200" ]; then
            print_success "页面访问正常 (HTTP ${response})"
        elif [ "$response" = "000" ]; then
            print_warning "无法访问页面，请检查容器日志"
        else
            print_warning "页面返回异常 (HTTP ${response})"
        fi
    else
        print_info "curl 未安装，跳过访问测试"
    fi
}

###############################################################################
# 函数：打印部署信息
###############################################################################
print_deployment_info() {
    echo
    echo "========================================================================"
    print_success "部署完成！"
    echo "========================================================================"
    echo
    print_info "容器名称：${CONTAINER_NAME}"
    print_info "访问端口：${PORT}"
    print_info "项目路径：${PROJECT_PATH}"
    echo
    print_info "访问地址："
    echo "  首页：http://localhost:${PORT}/max"
    echo "  VIP 页：http://localhost:${PORT}/max/vip"
    echo "  价格页：http://localhost:${PORT}/max/pricing"
    echo "  案例页：http://localhost:${PORT}/max/cases"
    echo "  文章页：http://localhost:${PORT}/max/articles"
    echo "  项目页：http://localhost:${PORT}/max/projects"
    echo
    print_info "常用命令："
    echo "  进入容器：docker exec -it ${CONTAINER_NAME} bash"
    echo "  查看日志：docker logs ${CONTAINER_NAME}"
    echo "  重启容器：docker restart ${CONTAINER_NAME}"
    echo "  停止容器：docker stop ${CONTAINER_NAME}"
    echo "  删除容器：docker rm ${CONTAINER_NAME}"
    echo
    print_info "后续步骤："
    echo "  1. 编辑 .env 文件，配置数据库连接"
    echo "  2. 运行迁移：docker exec -it ${CONTAINER_NAME} bash -c 'php artisan migrate'"
    echo "  3. 配置 OpenClaw Webhook"
    echo "  4. 配置企业微信推送"
    echo
    print_info "故障排查："
    echo "  查看日志：docker logs ${CONTAINER_NAME}"
    echo "  清除缓存：docker exec -it ${CONTAINER_NAME} bash -c 'php artisan cache:clear'"
    echo "  权限修复：docker exec -it ${CONTAINER_NAME} bash -c 'chmod -R 775 storage/ bootstrap/cache/'"
    echo
    echo "========================================================================"
}

###############################################################################
# 主函数
###############################################################################
main() {
    echo
    echo "========================================================================"
    echo "           AI 副业情报局 MAX - 一键部署脚本"
    echo "========================================================================"
    echo
    
    check_dependencies
    echo
    check_container
    echo
    create_database
    echo
    create_container
    echo
    install_dependencies
    echo
    configure_env
    echo
    set_permissions
    echo
    optimize_cache
    echo
    run_migrations
    echo
    verify_deployment
    echo
    print_deployment_info
}

# 执行主函数
main
