# OpenClaw 自动化采集任务配置手册

> 版本：v1.0  
> 日期：2026-04-01  
> 用途：配置 OpenClaw 定时采集任务，自动推送 AI 副业情报内容

---

## 一、任务概览

| 任务名称 | 频率 | 数据类型 | 推送目标 |
|---------|------|---------|---------|
| AI 文章采集 | 每 30 分钟 | articles | Laravel Webhook |
| GitHub 项目采集 | 每 30 分钟 | projects | Laravel Webhook |
| 副业案例生成 | 每日 10:00 | side_hustle_cases | Laravel Webhook |
| 工具变现指南 | 每日 11:00 | ai_tool_monetization | Laravel Webhook |
| 学习资料采集 | 每日 14:00 | learning_materials | Laravel Webhook |
| 每日日报推送 | 每日 09:00 | 邮件 + 企业微信 | 用户邮箱/企微 |

---

## 二、Webhook 配置

### 接收地址
```
URL: http://aifyqbj.calmpu.com/api/openclaw/webhook
Token: openclaw-ai-fetcher-2026
```

### 数据格式
```json
{
  "type": "articles | projects | side_hustle_cases | ai_tool_monetization | learning_materials",
  "items": [
    {
      "title": "标题",
      "url": "原文链接",
      "summary": "摘要",
      "content": "完整内容"
    }
  ]
}
```

---

## 三、采集任务详细配置

### 任务 1：AI 文章采集（每 30 分钟）

**目标：** 采集 AI 领域最新资讯，用于免费内容引流

**采集源：**
```
- arXiv cs.AI 最新论文
- OpenAI官方博客
- Anthropic官方博客
- Hugging Face博客
- GitHub AI 项目 README
```

**每次采集：** 10 篇

**字段要求：**
```json
{
  "title": "文章标题（必填，50 字内）",
  "url": "原文链接（必填，需验证可访问）",
  "summary": "200 字摘要（必填）",
  "content": "完整内容或 HTML（可选）",
  "cover_image": "封面图 URL（可选）",
  "published_at": "发布时间（可选）"
}
```

**过滤规则：**
- ✅ 必须是真实可访问的链接
- ✅ 内容必须与 AI 相关
- ❌ 排除首页/列表页链接
- ❌ 排除太短的网盘链接（可能是编造的）

---

### 任务 2：GitHub 项目采集（每 30 分钟）

**目标：** 采集热门 AI/ML 项目，用于免费内容引流

**采集源：**
```
- GitHub Trending（每日/每周）
- GitHub 搜索：AI/ML/LLM/RAG 关键词
- Hugging Face 热门模型
```

**每次采集：** 20 个项目

**字段要求：**
```json
{
  "name": "项目名称（必填）",
  "full_name": "完整名称 user/repo（必填）",
  "url": "GitHub 链接（必填）",
  "description": "项目描述（必填）",
  "stars": "Star 数量（数字，必填）",
  "forks": "Fork 数量（数字，可选）",
  "language": "主要语言（可选）",
  "topics": "标签数组（可选）"
}
```

**筛选标准：**
- Star 数 > 100（过滤太新的项目）
- 最近有更新（3 个月内有 commit）
- 与 AI/ML 相关

---

### 任务 3：副业案例生成（每日 10:00）

**目标：** 生成/采集真实副业案例，VIP 专属内容

**采集源：**
```
- 知乎副业话题高赞回答
- 小红书副业笔记
- 公众号副业文章
- 知识星球案例分享
```

**每日生成：** 1-2 个案例

**字段要求：**
```json
{
  "title": "案例标题（必填，如：小红书虚拟资料变现：月入 8000）",
  "summary": "简短描述（必填，100 字内）",
  "content": "完整案例内容 HTML（必填）",
  "category": "分类：online/offline/hybrid（必填）",
  "difficulty": "难度：easy/medium/hard（必填）",
  "time_commitment": "时间投入（必填，如：每天 2 小时）",
  "startup_cost": "启动成本（必填，如：0 元/500 元）",
  "revenue_model": "变现模式（必填）",
  "estimated_monthly_income": "预估月收入（数字，必填）",
  "actual_income": "实际收入（数字，已验证才填）",
  "income_screenshots": ["截图 URL 数组"]（可选）",
  "steps": [
    {"week": 1, "task": "第一周任务"},
    {"week": 2, "task": "第二周任务"}
  ]（必填）",
  "tools_needed": ["工具列表"]（必填）",
  "common_pitfalls": ["常见坑列表"]（必填）",
  "is_verified": "是否已验证（布尔，默认 false）",
  "is_vip_only": "是否 VIP 专属（布尔，默认 true）"
}
```

**内容结构（HTML）：**
```html
<h1>案例标题</h1>

<h2>背景介绍</h2>
<p>用户背景、副业起因...</p>

<h2>操作步骤</h2>
<h3>第 1 周</h3>
<ul>
  <li>任务 1</li>
  <li>任务 2</li>
</ul>

<h2>收入情况</h2>
<p>第 1 周：XXX 元</p>
<p>第 1 月：XXX 元</p>

<h2>所需工具</h2>
<ul>
  <li>工具 1</li>
  <li>工具 2</li>
</ul>

<h2>常见坑</h2>
<ul>
  <li>坑 1 + 避免方法</li>
  <li>坑 2 + 避免方法</li>
</ul>
```

---

### 任务 4：工具变现指南（每日 11:00）

**目标：** AI 工具变现地图，VIP 专属内容

**采集源：**
```
- 各 AI 工具官方博客
- Product Hunt 新产品
- 用户实际使用案例
- 接单平台（猪八戒/淘宝/Fiverr）
```

**每日生成：** 1 个工具指南

**字段要求：**
```json
{
  "tool_name": "工具名称（必填）",
  "tool_url": "官网链接（必填）",
  "tool_logo": "Logo URL（可选）",
  "category": "分类：image/text/video/audio/code（必填）",
  "description": "工具简介（必填，200 字内）",
  "monetization_scenarios": [
    {
      "name": "场景名称",
      "description": "场景描述",
      "difficulty": "难度",
      "income_range": "收入范围"
    }
  ]（必填，至少 3 个场景）",
  "prompt_templates": [
    {"name": "模板名", "prompt": "提示词内容"}
  ]（可选）",
  "delivery_standards": ["交付标准列表"]（必填）",
  "pricing_guide": [
    {"service": "服务项目", "price_range": "价格范围"}
  ]（必填）",
  "client_channels": [
    {"platform": "平台名", "url": "链接", "commission": "抽成"}
  ]（必填）",
  "is_domestic": "是否国内可用（布尔）",
  "pricing_model": "定价模式：free/subscription/pay_per_use（必填）",
  "popularity_score": "热门度 1-100（数字）",
  "is_vip_only": "是否 VIP 专属（布尔，默认 false）"
}
```

---

### 任务 5：学习资料采集（每日 14:00）

**目标：** 收集 AI 学习资源，VIP 专属内容

**采集源：**
```
- arXiv 论文
- GitHub 教程仓库
- 官方文档
- 付费课程（整理笔记）
```

**每日采集：** 5 个资料

**字段要求：**
```json
{
  "title": "资料标题（必填）",
  "resource_type": "类型：PDF/网盘/视频/电子书（必填）",
  "platform": "来源平台（必填）",
  "description": "资源描述（必填）",
  "url": "访问链接（必填，需验证）",
  "file_size": "文件大小（可选）",
  "is_vip_only": "是否 VIP 专属（布尔，默认 true）"
}
```

---

## 四、推送任务配置

### 任务 6：每日 AI 日报（每日 09:00）

**目标：** 自动推送昨日精选内容给用户

**推送渠道：**
- 邮件（SMTP）
- 企业微信（Webhook）

**内容结构：**
```
## 📰 AI 副业情报 · 每日简报
> 日期：YYYY-MM-DD

### 🔥 热门项目（Top 5）
- 项目 1（⭐ stars）
- 项目 2（⭐ stars）
...

### 💰 新案例上架
- 案例 1（预估月收入）
- 案例 2（预估月收入）

### 🛠️ 新工具推荐
- 工具 1：简介
- 工具 2：简介

---
👉 访问网站查看更多：http://aifyqbj.calmpu.com/max
```

**推送逻辑：**
```
1. 查询昨日新增内容
2. 筛选高质量内容（stars>1000/已验证案例）
3. 生成 Markdown 格式
4. 调用邮件 API 发送
5. 调用企业微信 Webhook 推送
```

---

## 五、OpenClaw Cron 配置示例

### 添加到 OpenClaw 的 cron 任务

```json
[
  {
    "name": "ai-articles-fetch",
    "schedule": {"kind": "every", "everyMs": 1800000},
    "payload": {
      "kind": "agentTurn",
      "message": "采集 AI 文章 10 篇，推送到 http://aifyqbj.calmpu.com/api/openclaw/webhook，type=articles"
    },
    "sessionTarget": "isolated"
  },
  {
    "name": "github-projects-fetch",
    "schedule": {"kind": "every", "everyMs": 1800000},
    "payload": {
      "kind": "agentTurn",
      "message": "采集 GitHub 热门 AI 项目 20 个，推送到 http://aifyqbj.calmpu.com/api/openclaw/webhook，type=projects"
    },
    "sessionTarget": "isolated"
  },
  {
    "name": "side-hustle-cases-generate",
    "schedule": {"kind": "cron", "expr": "0 10 * * *", "tz": "Asia/Shanghai"},
    "payload": {
      "kind": "agentTurn",
      "message": "生成 2 个副业案例，推送到 http://aifyqbj.calmpu.com/api/openclaw/webhook，type=side_hustle_cases"
    },
    "sessionTarget": "isolated"
  },
  {
    "name": "ai-tool-monetization-generate",
    "schedule": {"kind": "cron", "expr": "0 11 * * *", "tz": "Asia/Shanghai"},
    "payload": {
      "kind": "agentTurn",
      "message": "生成 1 个 AI 工具变现指南，推送到 http://aifyqbj.calmpu.com/api/openclaw/webhook，type=ai_tool_monetization"
    },
    "sessionTarget": "isolated"
  },
  {
    "name": "learning-materials-fetch",
    "schedule": {"kind": "cron", "expr": "0 14 * * *", "tz": "Asia/Shanghai"},
    "payload": {
      "kind": "agentTurn",
      "message": "采集 5 个 AI 学习资料，推送到 http://aifyqbj.calmpu.com/api/openclaw/webhook，type=learning_materials"
    },
    "sessionTarget": "isolated"
  },
  {
    "name": "daily-digest-send",
    "schedule": {"kind": "cron", "expr": "0 9 * * *", "tz": "Asia/Shanghai"},
    "payload": {
      "kind": "systemEvent",
      "text": "发送每日 AI 日报：1.查询昨日新增内容 2.生成 Markdown 简报 3.调用 Laravel 命令发送邮件和企业微信推送"
    },
    "sessionTarget": "current"
  }
]
```

---

## 六、质量检查清单

### 内容质量检查

```
□ 标题是否吸引人（包含关键词 + 数字）
□ 链接是否真实可访问
□ 内容是否有价值（不是废话）
□ 格式是否正确（JSON 符合 schema）
□ 是否去重（不与已有内容重复）
□ VIP 内容是否标记 is_vip_only=true
```

### 推送质量检查

```
□ 推送时间是否准时
□ 内容是否精选（不是全部推送）
□ 链接是否正确
□ 邮件是否进入垃圾箱（监控退信率）
□ 企业微信消息是否成功送达
```

---

## 七、异常处理

### 常见错误及处理

| 错误 | 原因 | 处理 |
|------|------|------|
| Webhook 返回 401 | Token 错误 | 检查 Token 配置 |
| Webhook 返回 500 | Laravel 报错 | 查看 Laravel 日志 |
| 采集内容为空 | 源站反爬 | 更换采集源/加延迟 |
| 链接无效 | AI 编造链接 | 加强 URL 验证 |
| 推送失败 | SMTP 配置错误 | 检查邮箱配置 |

### 监控告警

```
- 采集失败率 > 20% → 发送告警通知
- 推送失败率 > 10% → 发送告警通知
- Webhook 连续失败 3 次 → 暂停任务并通知
```

---

## 八、执行示例

### 直接发送给 OpenClaw 的指令

**采集 AI 文章：**
```
请执行 AI 文章采集任务：

1. 访问以下来源采集最新 AI 资讯：
   - https://openai.com/blog
   - https://www.anthropic.com/news
   - https://huggingface.co/blog
   - https://arxiv.org/list/cs.AI/recent

2. 采集 10 篇文章，每篇包含：
   - title: 文章标题
   - url: 原文链接（必须真实可访问）
   - summary: 200 字摘要
   - content: 完整内容或主要段落

3. 验证 URL 有效性：
   - 排除首页/列表页
   - 排除太短的网盘链接
   - 确保链接可访问

4. 推送到 Laravel：
   POST http://aifyqbj.calmpu.com/api/openclaw/webhook
   Headers: X-API-Token: openclaw-ai-fetcher-2026
   Body: {"type": "articles", "items": [...]}

5. 汇报结果：成功数量、失败数量、跳过数量
```

**生成副业案例：**
```
请生成 2 个副业实战案例：

1. 案例主题（选 2 个）：
   - 小红书虚拟资料变现
   - AI 代写服务
   - 抖音中视频计划
   - 闲鱼无货源电商
   - 知识星球运营

2. 每个案例包含：
   - title: 吸引人的标题（含收入数字）
   - summary: 100 字简介
   - content: 完整 HTML 内容（背景 + 步骤 + 收入 + 工具 + 避坑）
   - category: online/offline/hybrid
   - difficulty: easy/medium/hard
   - time_commitment: 每天投入时间
   - startup_cost: 启动成本
   - revenue_model: 变现模式
   - estimated_monthly_income: 预估月收入
   - steps: 分周任务数组（至少 4 周）
   - tools_needed: 所需工具列表
   - common_pitfalls: 常见坑列表
   - is_verified: false（除非有真实截图）
   - is_vip_only: true

3. 内容要求：
   - 真实可信（不要夸大）
   - 步骤详细（可操作）
   - 包含避坑指南
   - HTML 格式美观

4. 推送到 Laravel：
   POST http://aifyqbj.calmpu.com/api/openclaw/webhook
   Headers: X-API-Token: openclaw-ai-fetcher-2026
   Body: {"type": "side_hustle_cases", "items": [...]}

5. 汇报结果
```

---

## 九、优化建议

### 内容深度优化

```
1. 增加数据支撑
   - 收入截图（打码）
   - 时间记录
   - 操作日志

2. 增加对比分析
   - 不同方法对比
   - 投入产出比
   - 风险评估

3. 增加实操指导
   - 第一步做什么
   - 遇到问题怎么解决
   - 资源链接
```

### 采集效率优化

```
1. 批量采集
   - 一次请求获取多篇
   - 减少 API 调用次数

2. 智能去重
   - 标题相似度检测
   - URL 去重
   - 内容指纹

3. 质量评分
   - 根据来源权威性打分
   - 根据内容长度筛选
   - 根据互动数据排序
```

---

_最后更新：2026-04-01_
