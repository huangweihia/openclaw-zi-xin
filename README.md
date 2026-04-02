# OpenClaw 智信

> **OpenClaw + AI 智能体：把分散的信息差转化为可交付的咨询洞察**

[![Version](https://img.shields.io/badge/version-3.0-blue.svg)](https://github.com/openclaw/ai-side-laravel-max)
[![Laravel](https://img.shields.io/badge/laravel-10.x-ff2d20.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/php-8.2+-777bb4.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## 📖 项目简介

**OpenClaw 智信** 是一个基于 OpenClaw + AI 智能体的咨询洞察平台，通过自动化采集和智能分析，把分散的信息差转化为可交付的咨询报告。

### 核心功能

- 📰 **AI 资讯** - 自动采集最新 AI 动态
- 💰 **副业案例** - 真实可执行的副业项目
- 🛠️ **工具变现** - AI 工具变现场景指南
- 📝 **运营 SOP** - 标准化运营流程
- 📦 **付费资源** - 精选付费内容合集

---

## 🚀 快速开始

### 环境要求

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js 18+
- Docker（可选）

### 安装步骤

```bash
# 1. 克隆项目
git clone https://github.com/openclaw/ai-side-laravel-max.git
cd ai-side-laravel-max

# 2. 安装依赖
composer install
npm install

# 3. 配置环境变量
cp .env.example .env
php artisan key:generate

# 4. 配置数据库
# 编辑 .env 文件，设置数据库连接

# 5. 运行迁移
php artisan migrate

# 6. 启动服务
php artisan serve
```

访问 `http://localhost:8000` 查看项目。

---

## 📁 项目结构

```
ai-side-laravel-max/
├── app/                        # 应用核心
│   ├── Http/
│   │   ├── Controllers/       # 控制器
│   │   └── Middleware/        # 中间件
│   ├── Models/                # 模型
│   └── Services/              # 服务
├── bootstrap/                 # 启动文件
├── config/                    # 配置
├── database/
│   ├── migrations/           # 迁移
│   └── seeders/              # 填充
├── docs/                      # 文档
├── public/                    # 公共资源
├── resources/
│   └── views/                # 视图
├── routes/                    # 路由
├── .env                       # 环境变量
└── README.md                  # 项目说明
```

---

## 🛠️ 技术栈

| 模块 | 技术 | 版本 |
|------|------|------|
| 后端框架 | Laravel | 10.x |
| 前端 | Blade + TailwindCSS | - |
| 数据库 | MySQL | 8.0 |
| 后台管理 | Filament | 3.x |
| 自动化 | OpenClaw | - |
| 邮件服务 | SMTP（QQ 邮箱） | - |

---

## 📦 核心功能

### 1. 内容采集系统

- ✅ 自动采集 AI 相关文章
- ✅ GitHub 热门项目采集
- ✅ AI 职位信息采集
- ✅ 多平台内容聚合

### 2. 用户系统

- ✅ 邮箱注册/登录
- ✅ VIP 会员体系
- ✅ 收藏/评论/点赞
- ✅ 浏览历史

### 3. 内容管理

- ✅ 文章管理
- ✅ 项目管理
- ✅ 分类管理
- ✅ 标签系统

### 4. 邮件系统

- ✅ 日报自动发送
- ✅ 周报自动发送
- ✅ 邮件模板管理
- ✅ 订阅管理

### 5. 后台管理

- ✅ Filament 后台
- ✅ 用户管理
- ✅ 内容审核
- ✅ 数据统计

---

## 🔧 配置说明

### 数据库配置

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ai-side-laravel-max
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 邮件配置

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.qq.com
MAIL_PORT=465
MAIL_USERNAME=your_email@qq.com
MAIL_PASSWORD=your_auth_code
MAIL_ENCRYPTION=ssl
```

### OpenClaw 配置

```env
OPENCLAW_ENABLED=true
OPENCLAW_API_KEY=your_api_key
```

---

## 📚 文档

- [产品原型稿](docs/产品原型稿.md)
- [商业计划书](docs/商业计划书.md)
- [项目功能清单](docs/项目功能清单.md)
- [数据库设计](docs/数据库设计文档.md)
- [部署文档](docs/部署文档.md)

---

## 🤝 贡献指南

欢迎提交 Issue 和 Pull Request！

1. Fork 本项目
2. 创建功能分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 提交 Pull Request

---

## 📄 开源协议

本项目采用 [MIT](LICENSE) 协议开源。

---

## 📞 联系方式

- **项目主页**: https://github.com/openclaw/ai-side-laravel-max
- **问题反馈**: https://github.com/openclaw/ai-side-laravel-max/issues
- **邮箱**: support@aifyqbj.com

---

## 🙏 致谢

感谢以下开源项目：

- [Laravel](https://laravel.com)
- [Filament](https://filamentphp.com)
- [TailwindCSS](https://tailwindcss.com)
- [OpenClaw](https://openclaw.ai)

---

<p align="center">
  <sub>Built with ❤️ by OpenClaw Team</sub>
</p>
