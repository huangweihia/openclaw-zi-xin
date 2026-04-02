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
 * 真实 AI 数据采集服务
 * 使用阿里云百炼 API + web_search 能力
 */
class RealAiFetcher
{
    protected string $apiKey;
    protected string $baseUrl = 'https://dashscope.aliyuncs.com/api/v1';
    
    public function __construct()
    {
        $this->apiKey = env('DASHSCOPE_API_KEY');
        
        if (!$this->apiKey) {
            throw new \Exception('DASHSCOPE_API_KEY 未配置');
        }
    }
    
    /**
     * 获取 AI 相关文章（使用 AI 生成）
     */
    public function fetchArticles(string $topic, int $limit = 5): int
    {
        $this->log("📝 开始获取文章：{$topic}");
        
        try {
            // 让 AI 直接生成文章（基于训练数据）
            $articles = $this->aiGenerateArticles($topic, $limit);
            
            $saved = 0;
            foreach ($articles as $articleData) {
                try {
                    Article::create([
                        'title' => $articleData['title'],
                        'slug' => \Illuminate\Support\Str::slug($articleData['title']) . '-' . time() . '-' . rand(1000, 9999),
                        'summary' => $articleData['summary'],
                        'content' => $articleData['content'],
                        'cover_image' => $articleData['cover_image'] ?? null,
                        'source_url' => $articleData['url'] ?? 'https://example.com',
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
            
        } catch (\Exception $e) {
            $this->error("获取文章失败：" . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 获取 GitHub 项目
     */
    public function fetchProjects(string $topic, int $limit = 10): int
    {
        $this->log("💻 开始获取项目：{$topic}");
        
        try {
            // 搜索 GitHub 项目
            $searchResults = $this->webSearch("GitHub {$topic} stars:>1000", 15);
            
            if (empty($searchResults)) {
                return 0;
            }
            
            // AI 整理成项目数据
            $projects = $this->aiProcessProjects($searchResults, $limit);
            
            $category = \App\Models\Category::firstOrCreate(
                ['slug' => 'ai-tools'],
                ['name' => 'AI 工具']
            );
            
            $saved = 0;
            foreach ($projects as $projectData) {
                try {
                    Project::create([
                        'name' => $projectData['name'],
                        'full_name' => $projectData['name'],
                        'description' => $projectData['description'],
                        'url' => $projectData['url'],
                        'stars' => $projectData['stars'] ?? 0,
                        'forks' => $projectData['forks'] ?? 0,
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
            
        } catch (\Exception $e) {
            $this->error("获取项目失败：" . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 获取 AI 职位
     */
    public function fetchJobs(string $topic, int $limit = 10): int
    {
        $this->log("💼 开始获取职位：{$topic}");
        
        try {
            $searchResults = $this->webSearch("{$topic} 招聘 薪资 要求 site:zhipin.com OR site:lagou.com", 15);
            
            if (empty($searchResults)) {
                return 0;
            }
            
            $jobs = $this->aiProcessJobs($searchResults, $limit);
            
            $saved = 0;
            foreach ($jobs as $jobData) {
                try {
                    JobListing::create([
                        'title' => $jobData['title'],
                        'company_name' => $jobData['company_name'],
                        'salary' => $jobData['salary'] ?? '面议',
                        'city' => $jobData['city'] ?? '不限',
                        'experience' => $jobData['experience'] ?? '不限',
                        'education' => $jobData['education'] ?? '不限',
                        'description' => $jobData['description'],
                        'source_url' => $jobData['url'] ?? null,
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
            
        } catch (\Exception $e) {
            $this->error("获取职位失败：" . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 生成知识库文档
     */
    public function fetchKnowledge(string $topic, int $knowledgeBaseId): int
    {
        $this->log("📚 开始生成知识库：{$topic}");
        
        try {
            // 搜索相关资料
            $searchResults = $this->webSearch($topic, 10);
            
            // AI 生成教程文档
            $content = $this->aiGenerateDocument($topic, $searchResults);
            
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
            $this->error("生成知识库失败：" . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * 调用阿里云百炼 web_search 工具
     */
    protected function webSearch(string $query, int $count = 10): array
    {
        $this->log("🔍 搜索：{$query}");
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($this->baseUrl . '/services/search/internet-search/generation', [
            'model' => 'internet-search',
            'input' => ['query' => $query],
            'parameters' => [
                'count' => $count,
            ]
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            $results = $data['output']['results'] ?? [];
            $this->log("✅ 搜索到 " . count($results) . " 条结果");
            return $results;
        }
        
        $this->error("搜索失败：" . $response->status());
        return [];
    }
    
    /**
     * AI 处理文章数据
     */
    protected function aiProcessArticles(array $searchResults, int $limit): array
    {
        $prompt = $this->buildArticlePrompt($searchResults, $limit);
        $content = $this->callAI($prompt);
        
        return $this->parseJson($content);
    }
    
    /**
     * AI 处理项目数据
     */
    protected function aiProcessProjects(array $searchResults, int $limit): array
    {
        $prompt = $this->buildProjectPrompt($searchResults, $limit);
        $content = $this->callAI($prompt);
        
        return $this->parseJson($content);
    }
    
    /**
     * AI 处理职位数据
     */
    protected function aiProcessJobs(array $searchResults, int $limit): array
    {
        $prompt = $this->buildJobPrompt($searchResults, $limit);
        $content = $this->callAI($prompt);
        
        return $this->parseJson($content);
    }
    
    /**
     * AI 生成文档
     */
    protected function aiGenerateDocument(string $topic, array $searchResults): string
    {
        $prompt = $this->buildDocumentPrompt($topic, $searchResults);
        return $this->callAI($prompt);
    }
    
    /**
     * 调用 AI 模型
     */
    protected function callAI(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post($this->baseUrl . '/services/aigc/text-generation/generation', [
            'model' => 'qwen-turbo',
            'input' => [
                'messages' => [
                    ['role' => 'system', 'content' => '你是一个专业的数据整理助手，请严格按照要求返回 JSON 格式数据。'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ],
            'parameters' => [
                'result_format' => 'message',
                'max_tokens' => 4000,
            ]
        ]);
        
        if ($response->successful()) {
            return $response->json()['output']['choices'][0]['message']['content'] ?? '';
        }
        
        return '';
    }
    
    /**
     * 构建文章 Prompt
     */
    protected function buildArticlePrompt(array $results, int $limit): string
    {
        $sources = json_encode(array_map(function($r) {
            return ['title' => $r['title'] ?? '', 'snippet' => $r['content'] ?? '', 'url' => $r['url'] ?? ''];
        }, array_slice($results, 0, $limit * 2)), JSON_UNESCAPED_UNICODE);
        
        return <<<PROMPT
请根据以下搜索结果，整理成 {$limit} 篇 AI 相关文章。

搜索结果：
{$sources}

要求：
1. 返回 JSON 数组格式
2. 每篇文章包含：title, summary, content, url, cover_image
3. content 要详细（500 字以上）
4. 不要返回任何其他文字，只返回 JSON

返回格式：
[{"title":"标题","summary":"摘要","content":"详细内容","url":"来源链接","cover_image":"图片 URL"}]
PROMPT;
    }
    
    /**
     * 构建项目 Prompt
     */
    protected function buildProjectPrompt(array $results, int $limit): string
    {
        $sources = json_encode(array_map(function($r) {
            return ['title' => $r['title'] ?? '', 'snippet' => $r['content'] ?? '', 'url' => $r['url'] ?? ''];
        }, $results), JSON_UNESCAPED_UNICODE);
        
        return <<<PROMPT
请根据以下搜索结果，整理成 {$limit} 个 GitHub AI 项目。

搜索结果：
{$sources}

要求：
1. 返回 JSON 数组格式
2. 每个项目包含：name, description, url, stars, forks, language
3. stars 和 forks 是数字
4. 只返回 JSON，不要其他文字

返回格式：
[{"name":"项目名","description":"描述","url":"GitHub 链接","stars":1000,"forks":100,"language":"Python"}]
PROMPT;
    }
    
    /**
     * 构建职位 Prompt
     */
    protected function buildJobPrompt(array $results, int $limit): string
    {
        $sources = json_encode(array_map(function($r) {
            return ['title' => $r['title'] ?? '', 'snippet' => $r['content'] ?? '', 'url' => $r['url'] ?? ''];
        }, $results), JSON_UNESCAPED_UNICODE);
        
        return <<<PROMPT
请根据以下搜索结果，整理成 {$limit} 个 AI 相关招聘职位。

搜索结果：
{$sources}

要求：
1. 返回 JSON 数组格式
2. 每个职位包含：title, company_name, salary, city, experience, education, description, url, tags
3. tags 是数组
4. 只返回 JSON，不要其他文字

返回格式：
[{"title":"职位","company_name":"公司","salary":"薪资","city":"城市","experience":"经验","education":"学历","description":"描述","url":"链接","tags":["AI","Python"]}]
PROMPT;
    }
    
    /**
     * 构建文档 Prompt
     */
    protected function buildDocumentPrompt(string $topic, array $results): string
    {
        return <<<PROMPT
请根据以下搜索结果，生成一篇关于"{$topic}"的专业教程文档。

要求：
1. 使用 HTML 格式
2. 包含：标题、简介、正文（分章节）、总结
3. 内容详实，2000 字左右
4. 包含实用示例

直接返回 HTML 内容，不要其他文字。
PROMPT;
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
        Log::info("[RealAiFetcher] {$message}");
        echo "[{}] {$message}\n";
    }
    
    /**
     * 错误日志
     */
    protected function error(string $message): void
    {
        Log::error("[RealAiFetcher] {$message}");
        echo "[❌] {$message}\n";
    }
}
