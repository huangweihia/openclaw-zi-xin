<?php

namespace App\Services\GitHub;

use App\Models\Project;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubService
{
    protected string $baseUrl = 'https://api.github.com';
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.github.token') ?? env('GITHUB_TOKEN');
    }

    /**
     * 搜索热门 AI 项目
     */
    public function searchPopularProjects(string $query = 'AI agent', int $perPage = 20): array
    {
        $perPage = max(1, min($perPage, 100));

        try {
            $response = $this->githubClient()->get("{$this->baseUrl}/search/repositories", [
                'q' => $query,
                'sort' => 'stars',
                'order' => 'desc',
                'per_page' => $perPage,
            ]);

            if ($response->successful()) {
                return $response->json('items', []);
            }

            Log::error('GitHub API request failed', [
                'query' => $query,
                'per_page' => $perPage,
                'status' => $response->status(),
                'body' => $response->body(),
                'remaining' => $response->header('X-RateLimit-Remaining'),
                'reset_at' => $response->header('X-RateLimit-Reset'),
            ]);
        } catch (\Throwable $e) {
            Log::error('GitHub API exception', [
                'query' => $query,
                'per_page' => $perPage,
                'message' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * 获取热门趋势项目（近似 trending）
     */
    public function getTrending(string $since = 'daily'): array
    {
        $days = match ($since) {
            'weekly' => 7,
            'monthly' => 30,
            default => 1,
        };

        $date = now()->subDays($days)->format('Y-m-d');

        return $this->searchPopularProjects(
            "stars:>100 created:>{$date}",
            30
        );
    }

    /**
     * 保存项目到数据库
     */
    public function saveProject(array $data): ?Project
    {
        try {
            $project = Project::updateOrCreate(
                ['url' => $data['html_url']],
                [
                    'name' => $data['name'],
                    'full_name' => $data['full_name'] ?? null,
                    'description' => $data['description'] ?? null,
                    'language' => $data['language'] ?? null,
                    'stars' => $data['stargazers_count'] ?? 0,
                    'forks' => $data['forks_count'] ?? 0,
                    'score' => $this->calculateScore($data),
                    'tags' => $this->extractTags($data),
                    'monetization' => $this->analyzeMonetization($data),
                    'difficulty' => $this->assessDifficulty($data),
                    'is_featured' => ($data['stargazers_count'] ?? 0) > 10000,
                    'collected_at' => now(),
                ]
            );

            Log::info("Project saved: {$project->name}", [
                'stars' => $project->stars,
                'score' => $project->score,
            ]);

            return $project;
        } catch (\Exception $e) {
            Log::error('Failed to save project', [
                'name' => $data['name'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * 批量采集项目
     */
    public function collectProjects(array $keywords = []): int
    {
        $keywords = $keywords ?: [
            'AI agent',
            'LLM',
            'RAG',
            'langchain',
            'AI workflow',
            'chatbot',
            'generative AI',
        ];

        $collected = 0;

        foreach ($keywords as $keyword) {
            Log::info("Collecting projects for: {$keyword}");

            $projects = $this->searchPopularProjects($keyword);

            foreach ($projects as $projectData) {
                if ($this->saveProject($projectData)) {
                    $collected++;
                }

                // 避免触发 GitHub API 频控
                usleep(500000);
            }

            sleep(2);
        }

        Log::info('Collection completed', ['total' => $collected]);

        return $collected;
    }

    /**
     * 计算项目评分
     */
    protected function calculateScore(array $data): float
    {
        $stars = $data['stargazers_count'] ?? 0;
        $starScore = min($stars / 10000, 10);
        $growthScore = 5;
        $monetizationScore = $this->assessMonetizationPotential($data);

        return round($starScore * 0.3 + $growthScore * 0.3 + $monetizationScore * 0.4, 2);
    }

    /**
     * 评估变现潜力
     */
    protected function assessMonetizationPotential(array $data): float
    {
        $stars = $data['stargazers_count'] ?? 0;
        $hasWebsite = !empty($data['homepage']);
        $hasDiscussions = $data['has_discussions'] ?? false;

        if ($stars > 50000 || ($hasWebsite && $stars > 10000)) {
            return 10;
        }

        if ($stars > 10000 || $hasDiscussions) {
            return 6;
        }

        return 3;
    }

    /**
     * 变现方式分析
     */
    protected function analyzeMonetization(array $data): ?string
    {
        $description = strtolower($data['description'] ?? '');
        $methods = [];

        if (str_contains($description, 'platform') || str_contains($description, 'saas')) {
            $methods[] = 'SaaS 产品';
        }
        if (str_contains($description, 'tool') || str_contains($description, 'library')) {
            $methods[] = '工具/库收费';
        }
        if (str_contains($description, 'template') || str_contains($description, 'boilerplate')) {
            $methods[] = '模板售卖';
        }
        if (str_contains($description, 'course') || str_contains($description, 'tutorial')) {
            $methods[] = '课程/教程';
        }

        if (empty($methods)) {
            $methods[] = '商业化路径待验证';
            $methods[] = '可探索订阅/服务';
        }

        return implode(', ', $methods);
    }

    /**
     * 评估项目难度
     */
    protected function assessDifficulty(array $data): string
    {
        $description = strtolower($data['description'] ?? '');
        $stars = $data['stargazers_count'] ?? 0;

        if (str_contains($description, 'no-code') || str_contains($description, 'visual')) {
            return 'easy';
        }
        if ($stars > 50000) {
            return 'hard';
        }

        return 'medium';
    }

    /**
     * 提取关键词标签
     */
    protected function extractTags(array $data): array
    {
        $tags = [];

        if (!empty($data['language'])) {
            $tags[] = $data['language'];
        }

        $description = strtolower($data['description'] ?? '');

        $tagMappings = [
            'agent' => 'Agent',
            'llm' => 'LLM',
            'language model' => 'LLM',
            'rag' => 'RAG',
            'workflow' => 'Workflow',
            'chatbot' => 'Chatbot',
            'api' => 'API',
            'sdk' => 'SDK',
        ];

        foreach ($tagMappings as $keyword => $tag) {
            if (str_contains($description, $keyword)) {
                $tags[] = $tag;
            }
        }

        return array_values(array_unique($tags));
    }

    private function githubClient(): PendingRequest
    {
        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => config('app.name', 'Laravel') . '/GitHubCollector',
        ];

        if ($this->token) {
            $headers['Authorization'] = "token {$this->token}";
        }

        return Http::withHeaders($headers)
            ->timeout(20)
            ->connectTimeout(8)
            ->retry(2, 800);
    }
}
