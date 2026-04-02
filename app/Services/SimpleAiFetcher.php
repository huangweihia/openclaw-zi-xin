<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Project;
use App\Models\JobListing;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * 简单 AI 采集服务
 * 直接调用阿里云百炼 API 生成内容
 */
class SimpleAiFetcher
{
    protected string $apiKey;
    
    public function __construct()
    {
        $this->apiKey = env('DASHSCOPE_API_KEY');
        
        if (!$this->apiKey) {
            throw new \Exception('DASHSCOPE_API_KEY 未配置');
        }
    }
    
    /**
     * 获取 AI 相关文章
     */
    public function fetchArticles(string $topic, int $limit = 5): int
    {
        $this->log("📝 开始获取文章：{$topic}");
        
        $prompt = "请生成 {$limit} 篇关于\"{$topic}\"的 AI 相关文章。

要求：
1. 返回 JSON 数组格式
2. 每篇文章包含：title, summary, content, url
3. content 要详细（800 字以上）
4. 只返回 JSON，不要其他文字

返回格式示例：
[{\"title\":\"标题\",\"summary\":\"摘要\",\"content\":\"详细内容\",\"url\":\"https://example.com/1\"}]";

        $content = $this->callAI($prompt);
        $articles = $this->parseJson($content);
        
        $saved = 0;
        foreach ($articles as $articleData) {
            try {
                Article::create([
                    'title' => $articleData['title'] ?? '无标题',
                    'slug' => \Illuminate\Support\Str::slug($articleData['title'] ?? 'article') . '-' . time() . '-' . rand(1000, 9999),
                    'summary' => $articleData['summary'] ?? '',
                    'content' => $articleData['content'] ?? '',
                    'source_url' => $articleData['url'] ?? null,
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                $saved++;
            } catch (\Exception $e) {
                $this->error("保存文章失败：" . $e->getMessage());
            }
        }
        
        $this->log("✅ 成功保存 {$saved} 篇文章");
        return $saved;
    }
    
    /**
     * 获取 GitHub 项目
     */
    public function fetchProjects(string $topic, int $limit = 10): int
    {
        $this->log("💻 开始获取项目：{$topic}");
        
        $prompt = "请列出 {$limit} 个关于\"{$topic}\"的热门 GitHub AI 项目。

要求：
1. 返回 JSON 数组格式
2. 每个项目包含：name, description, url, stars, forks, language
3. stars 和 forks 是数字
4. 只返回 JSON，不要其他文字

返回格式示例：
[{\"name\":\"项目名\",\"description\":\"描述\",\"url\":\"https://github.com/xxx\",\"stars\":1000,\"forks\":100,\"language\":\"Python\"}]";

        $content = $this->callAI($prompt);
        $projects = $this->parseJson($content);
        
        $category = \App\Models\Category::firstOrCreate(
            ['slug' => 'ai-tools'],
            ['name' => 'AI 工具']
        );
        
        $saved = 0;
        foreach ($projects as $projectData) {
            try {
                Project::create([
                    'name' => $projectData['name'] ?? '未知项目',
                    'full_name' => $projectData['name'] ?? '未知项目',
                    'description' => $projectData['description'] ?? '暂无描述',
                    'url' => $projectData['url'] ?? '#',
                    'stars' => (int) ($projectData['stars'] ?? 0),
                    'forks' => (int) ($projectData['forks'] ?? 0),
                    'language' => $projectData['language'] ?? null,
                    'category_id' => $category->id,
                    'monetization' => 'medium',
                    'difficulty' => 'medium',
                    'is_featured' => ($projectData['stars'] ?? 0) > 5000,
                    'collected_at' => now(),
                ]);
                $saved++;
            } catch (\Exception $e) {
                $this->error("保存项目失败：" . $e->getMessage());
            }
        }
        
        $this->log("✅ 成功保存 {$saved} 个项目");
        return $saved;
    }
    
    /**
     * 获取 AI 职位
     */
    public function fetchJobs(string $topic, int $limit = 10): int
    {
        $this->log("💼 开始获取职位：{$topic}");
        
        $prompt = "请列出 {$limit} 个关于\"{$topic}\"的招聘职位。

要求：
1. 返回 JSON 数组格式
2. 每个职位包含：title, company_name, salary, city, experience, education, description, tags
3. tags 是数组
4. 只返回 JSON，不要其他文字

返回格式示例：
[{\"title\":\"职位\",\"company_name\":\"公司\",\"salary\":\"20-40K\",\"city\":\"北京\",\"experience\":\"3-5 年\",\"education\":\"本科\",\"description\":\"职位描述\",\"tags\":[\"AI\",\"Python\"]}]";

        $content = $this->callAI($prompt);
        $jobs = $this->parseJson($content);
        
        $saved = 0;
        foreach ($jobs as $jobData) {
            try {
                JobListing::create([
                    'title' => $jobData['title'] ?? '未知职位',
                    'company_name' => $jobData['company_name'] ?? '未知公司',
                    'salary' => $jobData['salary'] ?? '面议',
                    'city' => $jobData['city'] ?? '不限',
                    'experience' => $jobData['experience'] ?? '不限',
                    'education' => $jobData['education'] ?? '不限',
                    'description' => $jobData['description'] ?? '',
                    'tags' => $jobData['tags'] ?? [],
                    'is_full_time' => true,
                ]);
                $saved++;
            } catch (\Exception $e) {
                $this->error("保存职位失败：" . $e->getMessage());
            }
        }
        
        $this->log("✅ 成功保存 {$saved} 个职位");
        return $saved;
    }
    
    /**
     * 生成知识库文档
     */
    public function fetchKnowledge(string $topic, int $knowledgeBaseId): int
    {
        $this->log("📚 开始生成知识库：{$topic}");
        
        $prompt = "请生成一篇关于\"{$topic}\"的专业教程文档。

要求：
1. 使用 HTML 格式
2. 包含：标题、简介、正文（分章节）、总结
3. 内容详实，2000 字左右
4. 包含实用示例
5. 直接返回 HTML 内容，不要其他文字";

        $content = $this->callAI($prompt);
        
        try {
            KnowledgeDocument::create([
                'knowledge_base_id' => $knowledgeBaseId,
                'title' => $topic,
                'content' => $content,
                'file_type' => 'ai_generated',
                'chunks' => preg_split('/\n\n+/', $content),
            ]);
            
            $this->log("✅ 成功生成知识库文档");
            return 1;
        } catch (\Exception $e) {
            $this->error("保存知识库失败：" . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 调用 AI 模型（使用 OpenAI 兼容格式）
     */
    protected function callAI(string $prompt): string
    {
        $this->log("📡 调用 AI (OpenAI 兼容格式)...");
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post('https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions', [
            'model' => 'qwen-plus',
            'messages' => [
                ['role' => 'system', 'content' => '你是一个专业的内容生成助手，请严格按照用户要求返回指定格式的数据。'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 4000,
        ]);
        
        if ($response->successful()) {
            $content = $response->json()['choices'][0]['message']['content'] ?? '';
            $this->log("✅ AI 调用成功，返回 " . strlen($content) . " 字");
            return $content;
        }
        
        $this->error("❌ AI 调用失败：" . $response->status() . " - " . $response->body());
        return '';
    }
    
    /**
     * 解析 JSON
     */
    protected function parseJson(string $content): array
    {
        if (empty($content)) return [];
        
        // 提取 JSON 数组
        preg_match('/\[.*\]/s', $content, $matches);
        $jsonStr = $matches[0] ?? $content;
        
        $data = json_decode($jsonStr, true);
        return is_array($data) ? $data : [];
    }
    
    /**
     * 日志
     */
    protected function log(string $message): void
    {
        Log::info("[SimpleAiFetcher] {$message}");
        echo "[{}] {$message}\n";
    }
    
    /**
     * 错误日志
     */
    protected function error(string $message): void
    {
        Log::error("[SimpleAiFetcher] {$message}");
        echo "[❌] {$message}\n";
    }
}
