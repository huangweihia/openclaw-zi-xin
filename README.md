# AI 副业情报局 MAX - 项目说明

> 版本：v3.0  
> 创建日期：2026-04-01  
> 项目路径：`/home/node/.openclaw/workspace/ai-side-laravel-max`

---

## 一、项目概述

### 1.1 项目定位

基于 OpenClaw 自动化的 AI 副业情报平台，通过自动化采集 + 用户生成内容（UGC），提供：
- 📰 AI 资讯（自动采集）
- 💰 副业案例（PGC+UGC）
- 🛠️ 工具变现指南（PGC+UGC）
- 📝 运营 SOP（PGC）
- 📦 付费资源（PGC+UGC）

### 1.2 核心卖点

```
用 OpenClaw 自动化降低内容成本
用邮箱注册降低获客成本
用企业微信/邮件推送增加粘性
用内容锁定 + 优惠诱导转化 VIP
用 UGC 生态提升内容产量
```

### 1.3 技术栈

| 模块 | 技术 | 版本 |
|------|------|------|
| 框架 | Laravel | 10.x |
| 前端 | Blade + Tailwind | - |
| 数据库 | MySQL | 8.0 |
| 后台 | Filament | 3.x |
| 自动化 | OpenClaw | - |
| 推送 | 企业微信 | 免费 API |
| 邮件 | SMTP（QQ 邮箱） | 免费 |

---

## 二、项目结构

```
ai-side-laravel-max/
├── app/                        # 应用核心代码
│   ├── Http/
│   │   ├── Controllers/       # 控制器
│   │   └── Middleware/        # 中间件
│   ├── Models/                # 模型
│   └── Services/              # 服务类
├── bootstrap/                 # 启动文件
├── config/                    # 配置文件
├── database/
│   ├── migrations/           # 数据库迁移
│   └── seeders/              # 数据填充
├── docs/                      # 项目文档
│   ├── 1.0 可用功能清单.md
│   ├── 商业计划书.md
│   ├── 项目功能清单.md
│   ├── OpenClaw 自动化配置.md
│   ├── 完整项目文档.md
│   └── 推广执行手册.md
├── public/                    # 公共资源
├── resources/
│   ├── views/                # Blade 模板
│   └── ...
├── routes/                    # 路由配置
├── .env                       # 环境变量
├── composer.json              # PHP 依赖
└── artisan                    # Laravel 命令行
```

---

## 三、数据库配置

### 3.1 新建数据库

```sql
-- 创建数据库
CREATE DATABASE `ai-side-laravel-max` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建用户（可选）
CREATE USER 'max_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON `ai-side-laravel-max`.* TO 'max_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3.2 配置 .env

```bash
# 应用配置
APP_NAME="AI 副业情报局 MAX"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://aifyqbj.calmpu.com/max

# 数据库配置
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai-side-laravel-max
DB_USERNAME=root
DB_PASSWORD=your_password

# 邮件配置
MAIL_MAILER=smtp
MAIL_HOST=smtp.qq.com
MAIL_PORT=465
MAIL_USERNAME=2801359160@qq.com
MAIL_PASSWORD=uvxftlhiicvzdffa
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=2801359160@qq.com
MAIL_FROM_NAME="${APP_NAME}"

# OpenClaw Webhook
OPENCLAW_WEBHOOK_TOKEN=openclaw-ai-fetcher-2026

# 企业微信（后续配置）
WECHAT_BOT_WEBHOOK_URL=
```

---

## 四、安装步骤

### 4.1 安装依赖

```bash
cd /home/node/.openclaw/workspace/ai-side-laravel-max

# 安装 PHP 依赖
composer install --no-dev --optimize-autoloader

# 安装前端依赖（可选）
npm install
npm run build
```

### 4.2 生成应用密钥

```bash
php artisan key:generate
```

### 4.3 运行数据库迁移

```bash
# 先运行基础迁移（1.0 复用）
php artisan migrate

# 运行 MAX 新增迁移
php artisan migrate --path=database/migrations/max
```

### 4.4 设置权限

```bash
# 存储目录权限
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# 头像上传目录
chmod -R 775 public/avatars
```

### 4.5 配置 Web 服务器

**Nginx 配置示例：**

```nginx
server {
    listen 80;
    server_name aifyqbj.calmpu.com;
    root /home/node/.openclaw/workspace/ai-side-laravel-max/public;
    index index.php index.html;

    location /max {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

---

## 五、1.0 复用说明

### 5.1 直接复用（90% 代码）

```
✅ 用户系统（Auth）
✅ 互动系统（点赞/评论/收藏）
✅ 浏览历史
✅ 邮件系统
✅ 支付系统
✅ 后台 Filament 资源
✅ 通知系统
✅ 静态页面
```

### 5.2 改造复用（50% 代码）

```
⚠️ 投稿系统 → 用户发布系统
⚠️ 知识库 → Premium 内容
```

### 5.3 全新开发（0% 复用）

```
❌ 企业微信推送
❌ 广告位管理
❌ 换肤功能
❌ 数据监控面板
```

详见：`docs/1.0 可用功能清单.md`

---

## 六、开发计划

### 第 1 阶段（基础功能，1-2 周）

```
□ 配置数据库和环境
□ 运行 1.0 基础迁移
□ 测试用户系统（注册/登录）
□ 测试内容浏览（文章/项目）
□ 测试互动功能（点赞/评论/收藏）
□ 配置企业微信推送
```

### 第 2 阶段（Premium 内容，2-3 周）

```
□ 创建 4 个 Premium 模型
□ 创建后台 Filament 资源
□ 创建前端展示页面
□ 配置 OpenClaw 自动采集
□ 测试推送功能
```

### 第 3 阶段（用户发布，1-2 周）

```
□ 前端发布表单
□ 后台审核流程
□ 发布状态通知
□ 积分奖励机制
```

### 第 4 阶段（优化功能，2 周）

```
□ 广告位管理
□ 公告系统
□ 留言系统
□ 换肤功能
□ 数据监控面板
```

---

## 七、核心功能模块

### 7.1 用户系统

| 功能 | 状态 | 说明 |
|------|------|------|
| 邮箱注册 | ✅ 复用 | 邮箱验证码 |
| 邮箱登录 | ✅ 复用 | 支持记住登录 |
| 个人资料 | ✅ 复用 | dashboard |
| 头像上传 | ✅ 复用 | 支持图片上传 |
| 企业微信绑定 | ⏸️ 待开发 | 接收推送 |

### 7.2 内容系统

| 功能 | 状态 | 说明 |
|------|------|------|
| 文章浏览 | ✅ 复用 | 免费+VIP |
| 项目浏览 | ✅ 复用 | GitHub 项目 |
| 副业案例 | ⏸️ 待开发 | VIP 专属 |
| 工具变现 | ⏸️ 待开发 | 部分 VIP |
| 运营 SOP | ⏸️ 待开发 | VIP 专属 |
| 付费资源 | ⏸️ 待开发 | VIP 专属 |
| 用户发布 | ⏸️ 待开发 | 需审核 |

### 7.3 互动系统

| 功能 | 状态 | 说明 |
|------|------|------|
| 点赞 | ✅ 复用 | 多态关联 |
| 评论 | ✅ 复用 | 支持 Markdown |
| 收藏 | ✅ 复用 | 多态关联 |
| 浏览历史 | ✅ 复用 | 自动记录 |

### 7.4 推送系统

| 功能 | 状态 | 说明 |
|------|------|------|
| 邮件推送 | ✅ 复用 | SMTP |
| 企业微信推送 | ⏸️ 待开发 | Webhook |
| 推送控制后台 | ⏸️ 待开发 | 后台管理 |

---

## 八、OpenClaw 集成

### 8.1 Webhook 接收

**路由：** `POST /api/openclaw/webhook`

**Token：** `openclaw-ai-fetcher-2026`

**数据类型：**
- `articles` - AI 文章
- `projects` - GitHub 项目
- `side_hustle_cases` - 副业案例
- `ai_tool_monetization` - 工具变现
- `learning_materials` - 学习资料

### 8.2 定时任务

| 任务 | 频率 | 说明 |
|------|------|------|
| AI 文章采集 | 30 分钟 | 10 篇/次 |
| GitHub 项目采集 | 30 分钟 | 20 个/次 |
| 副业案例生成 | 每日 10:00 | 1-2 个/天 |
| 工具变现指南 | 每日 11:00 | 1 个/天 |
| 学习资料采集 | 每日 14:00 | 5 个/天 |
| 每日日报推送 | 每日 09:00 | 邮件 + 企微 |

详见：`docs/OpenClaw 自动化配置.md`

---

## 九、会员体系

### 9.1 免费版

```
价格：0 元

权益：
- 网站内容浏览（70%）
- 每周邮件日报
- 基础搜索功能
- 发布内容（需审核）
- 点赞/评论/收藏
```

### 9.2 VIP 会员

```
价格：199 元/年 或 29 元/月

权益：
- 免费版全部
- 副业案例库（50+）
- AI 工具变现地图（20+）
- 运营 SOP（10+）
- 付费资源合集
- 每日邮件推送
- 企业微信推送
- 专属社群
- 发布资源分享（VIP 专属）
- 无广告体验
```

### 9.3 SVIP 私教

```
价格：999 元/年（限 50 人）

权益：
- VIP 全部
- 定制数据采集（每周报告）
- 竞品分析周报
- 独家脚本共享
- 远程协助（1 小时/月）
- 付费工具共享账号
- 优先审核
```

---

## 十、文档索引

| 文档 | 用途 |
|------|------|
| `docs/1.0 可用功能清单.md` | 1.0 复用功能参考 |
| `docs/商业计划书.md` | 商业模式和财务预测 |
| `docs/项目功能清单.md` | 完整功能需求文档 |
| `docs/OpenClaw 自动化配置.md` | OpenClaw 任务配置 |
| `docs/完整项目文档.md` | 技术和数据库文档 |
| `docs/推广执行手册.md` | 推广策略和 SOP |

---

## 十一、快速启动命令

```bash
# 进入项目目录
cd /home/node/.openclaw/workspace/ai-side-laravel-max

# 安装依赖
composer install --no-dev --optimize-autoloader

# 生成密钥
php artisan key:generate

# 运行迁移
php artisan migrate

# 启动开发服务器
php artisan serve --host=0.0.0.0 --port=8082

# 访问地址
# http://localhost:8082
```

---

_最后更新：2026-04-01_

_版本：v1.0_
