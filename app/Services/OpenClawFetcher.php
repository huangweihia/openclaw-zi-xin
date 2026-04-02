<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Project;
use App\Models\JobListing;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Log;

class OpenClawFetcher
{
    /**
     * 使用 OpenClaw 子 Agent 获取 AI 相关文章
     */
    public function fetchArticles(string $topic, int $limit = 5): int
    {
        $this->log("📝 开始获取文章：{$topic}");
        
        // 调用 OpenClaw 子 Agent
        $result = $this->callOpenClawAgent($this->buildArticlePrompt($topic, $limit));
        
        if (!$result) {
            $this->error("获取文章失败");
            return 0;
        }
        
        // 解析 AI 返回的文章数据
        $articles = $this->parseArticles($result);
        
        // 保存到数据库
        $saved = 0;
        foreach ($articles as $articleData) {
            try {
                Article::firstOrCreate(
                    ['source_url' => $articleData['url'] ?? md5($articleData['title'])],
                    [
                        'title' => $articleData['title'] ?? '无标题',
                        'summary' => $articleData['summary'] ?? '',
                        'content' => $articleData['content'] ?? '',
                        'cover_image' => $articleData['cover_image'] ?? null,
                        'category_slug' => 'ai-news',
                        'source' => $articleData['source'] ?? 'AI 自动获取',
                        'is_featured' => false,
                        'published_at' => now(),
                    ]
                );
                $saved++;
            } catch (\Exception $e) {
                $this->error("保存文章失败：" . $e->getMessage());
            }
        }
        
        $this->log("✅ 成功保存 {$saved} 篇文章");
        return $saved;
    }
    
    /**
     * 使用 OpenClaw 子 Agent 获取 GitHub 项目
     */
    public function fetchProjects(string $topic, int $limit = 10): int
    {
        $this->log("💻 开始获取项目：{$topic}");
        
        $result = $this->callOpenClawAgent($this->buildProjectPrompt($topic, $limit));
        
        if (!$result) {
            $this->error("获取项目失败");
            return 0;
        }
        
        $projects = $this->parseProjects($result);
        
        // 获取或创建分类
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'ai-tools'],
            ['name' => 'AI 工具']
        );
        
        $saved = 0;
        foreach ($projects as $projectData) {
            try {
                Project::firstOrCreate(
                    ['url' => $projectData['url'] ?? md5($projectData['name'])],
                    [
                        'name' => $projectData['name'] ?? '未知项目',
                        'full_name' => $projectData['full_name'] ?? $projectData['name'],
                        'description' => $projectData['description'] ?? '暂无描述',
                        'stars' => (int) ($projectData['stars'] ?? 0),
                        'forks' => (int) ($projectData['forks'] ?? 0),
                        'language' => $projectData['language'] ?? null,
                        'category_id' => $category->id,
                        'monetization' => 'medium',
                        'difficulty' => 'medium',
                        'is_featured' => ($projectData['stars'] ?? 0) > 5000,
                        'collected_at' => now(),
                    ]
                );
                $saved++;
            } catch (\Exception $e) {
                $this->error("保存项目失败：" . $e->getMessage());
            }
        }
        
        $this->log("✅ 成功保存 {$saved} 个项目");
        return $saved;
    }
    
    /**
     * 使用 OpenClaw 子 Agent 获取 AI 职位
     */
    public function fetchJobs(string $topic, int $limit = 10): int
    {
        $this->log("💼 开始获取职位：{$topic}");
        
        $result = $this->callOpenClawAgent($this->buildJobPrompt($topic, $limit));
        
        if (!$result) {
            $this->error("获取职位失败");
            return 0;
        }
        
        $jobs = $this->parseJobs($result);
        
        $saved = 0;
        foreach ($jobs as $jobData) {
            try {
                JobListing::firstOrCreate(
                    [
                        'title' => $jobData['title'] ?? '未知职位',
                        'company_name' => $jobData['company_name'] ?? '未知公司'
                    ],
                    [
                        'salary' => $jobData['salary'] ?? '面议',
                        'city' => $jobData['city'] ?? '不限',
                        'experience' => $jobData['experience'] ?? '不限',
                        'education' => $jobData['education'] ?? '不限',
                        'description' => $jobData['description'] ?? '',
                        'source_url' => $jobData['source_url'] ?? null,
                        'tags' => $jobData['tags'] ?? [],
                        'is_full_time' => true,
                    ]
                );
                $saved++;
            } catch (\Exception $e) {
                $this->error("保存职位失败：" . $e->getMessage());
            }
        }
        
        $this->log("✅ 成功保存 {$saved} 个职位");
        return $saved;
    }
    
    /**
     * 使用 OpenClaw 子 Agent 生成知识库文档
     */
    public function fetchKnowledge(string $topic, int $knowledgeBaseId): int
    {
        $this->log("📚 开始生成知识库：{$topic}");
        
        $result = $this->callOpenClawAgent($this->buildKnowledgePrompt($topic));
        
        if (!$result) {
            $this->error("生成知识库失败");
            return 0;
        }
        
        try {
            KnowledgeDocument::create([
                'knowledge_base_id' => $knowledgeBaseId,
                'title' => $topic,
                'content' => $result,
                'file_type' => 'ai_generated',
                'chunks' => $this->chunkContent($result),
            ]);
            
            $this->log("✅ 成功生成知识库文档");
            return 1;
        } catch (\Exception $e) {
            $this->error("保存知识库失败：" . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 调用 OpenClaw 子 Agent
     * 
     * 通过执行 OpenClaw 命令来获取 AI 搜索结果
     */
    private function callOpenClawAgent(string $prompt): ?string
    {
        try {
            // 使用 OpenClaw CLI（推荐，使用已验证的 API Key）
            $workspace = base_path('../');
            $sessionId = 'ai-fetch-' . time();
            $escapedPrompt = str_replace('"', '\\"', $prompt);
            
            $command = "cd {$workspace} && openclaw agent --session-id {$sessionId} --message \"{$escapedPrompt}\" --json 2>&1";
            
            $this->log("🔧 执行 OpenClaw CLI 命令...");
            $output = shell_exec($command);
            
            if ($output) {
                $data = json_decode($output, true);
                if ($data && isset($data['result']['payloads'][0]['text'])) {
                    $result = $data['result']['payloads'][0]['text'];
                    $this->log("✅ OpenClaw 调用成功，返回内容长度：" . strlen($result));
                    return $result;
                }
            }
            
            $this->log("⚠️ OpenClaw 无有效输出");
            return null;
            
        } catch (\Exception $e) {
            $this->error("调用 OpenClaw 失败：" . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 构建文章获取 Prompt
     */
    private function buildArticlePrompt(string $topic, int $limit): string
    {
        return <<<PROMPT
你是一个专业的内容采集助手。请使用 web_search 搜索最新的 AI 相关文章，主题："{$topic}"

请严格按以下要求返回：
1. 搜索 5-10 个相关网页
2. 整理成 {$limit} 篇文章
3. **只返回 JSON 数组**，不要任何其他文字
4. 每篇文章包含以下字段：
   - title: 文章标题
   - summary: 文章摘要（200 字以内）
   - content: 文章内容（500 字以上）
   - source: 来源网站
   - url: 原文链接
   - cover_image: 封面图片 URL

返回格式示例：
[{"title":"标题","summary":"摘要","content":"内容","source":"机器之心","url":"https://...","cover_image":"https://..."}]

现在开始搜索并返回 JSON：
PROMPT;
    }
    
    /**
     * 构建项目获取 Prompt
     */
    private function buildProjectPrompt(string $topic, int $limit): string
    {
        return <<<PROMPT
请搜索 GitHub 上最热门的 AI 项目，关键词："{$topic}"

要求：
1. 搜索 GitHub Trending 和相关技术网站
2. 整理成 {$limit} 个项目
3. 每个项目包含以下字段（JSON 格式）：
   - name: 项目名称
   - full_name: 完整名称（user/repo）
   - description: 项目描述
   - url: GitHub 链接
   - stars: 星星数量（数字）
   - forks: Fork 数量（数字）
   - language: 主要编程语言

请只返回 JSON 数组，不要其他内容。
PROMPT;
    }
    
    /**
     * 构建职位获取 Prompt
     */
    private function buildJobPrompt(string $topic, int $limit): string
    {
        return <<<PROMPT
请搜索最新的 AI 相关招聘职位，关键词："{$topic}"

要求：
1. 搜索招聘网站（BOSS 直聘、拉勾等）
2. 整理成 {$limit} 个职位
3. 每个职位包含以下字段（JSON 格式）：
   - title: 职位名称
   - company_name: 公司名称
   - salary: 薪资范围
   - city: 城市
   - experience: 经验要求
   - education: 学历要求
   - description: 职位描述
   - source_url: 原文链接
   - tags: 技能标签数组

请只返回 JSON 数组，不要其他内容。
PROMPT;
    }
    
    /**
     * 构建知识库 Prompt
     */
    private function buildKnowledgePrompt(string $topic): string
    {
        return <<<PROMPT
请生成一篇关于"{$topic}"的专业教程文档。

要求：
1. 使用 web_search 搜索相关资料
2. 生成结构清晰的技术文档
3. 使用 HTML 格式
4. 包含：标题、简介、正文（分章节）、总结
5. 内容详实，包含实用示例
6. 2000 字左右

请直接返回 HTML 格式的文档内容。
PROMPT;
    }
    
    /**
     * 解析文章 JSON
     */
    private function parseArticles(string $content): array
    {
        $data = $this->parseJson($content);
        
        // 如果解析失败，返回空数组
        if (empty($data)) {
            $this->error("⚠️ 文章 JSON 解析失败，原始内容：" . substr($content, 0, 200));
            return [];
        }
        
        return $data;
    }
    
    /**
     * 解析项目 JSON
     */
    private function parseProjects(string $content): array
    {
        return $this->parseJson($content);
    }
    
    /**
     * 解析职位 JSON
     */
    private function parseJobs(string $content): array
    {
        return $this->parseJson($content);
    }
    
    /**
     * 通用 JSON 解析
     */
    private function parseJson(string $content): array
    {
        if (empty($content)) {
            return [];
        }
        
        // 尝试提取 JSON 数组部分
        preg_match('/\[.*\]/s', $content, $matches);
        $jsonStr = $matches[0] ?? $content;
        
        // 尝试解析
        $data = json_decode($jsonStr, true);
        
        if (is_array($data)) {
            return $data;
        }
        
        // 如果还不是数组，尝试提取 JSON 对象
        preg_match('/\{.*\}/s', $content, $matches);
        $jsonStr = $matches[0] ?? $content;
        $data = json_decode($jsonStr, true);
        
        return is_array($data) ? $data : [];
    }
    
    /**
     * 分块内容
     */
    private function chunkContent(string $content): array
    {
        return array_values(array_filter(preg_split('/\n\n+/', $content)));
    }
    
    /**
     * 日志记录
     */
    private function log(string $message): void
    {
        Log::info("[OpenClawFetcher] {$message}");
        echo "[{}] {$message}\n";
    }
    
    /**
     * 错误记录
     */
    private function error(string $message): void
    {
        Log::error("[OpenClawFetcher] {$message}");
        echo "[❌] {$message}\n";
    }
}
