<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Project;
use App\Models\JobListing;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeBase;

/**
 * 模拟数据获取服务（开发测试用）
 */
class MockAiFetcher
{
    public function fetchArticles(string $topic, int $limit = 5): int
    {
        $articles = [
            [
                'title' => "GPT-5 即将发布：OpenAI 官方透露这些关键信息",
                'summary' => "OpenAI 最近透露了 GPT-5 的一些关键特性，包括更强的推理能力、多模态理解等...",
                'content' => $this->generateContent("GPT-5"),
                'source_url' => 'https://example.com/gpt5-news-' . time(),
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => "DeepSeek V3.5 发布：国产大模型再次突破",
                'summary' => "DeepSeek V3.5 在数学、代码、多模态等多个基准测试中超越 GPT-4o...",
                'content' => $this->generateContent("DeepSeek V3.5"),
                'source_url' => 'https://example.com/deepseek-v35-' . time(),
                'is_published' => true,
                'published_at' => now(),
            ],
        ];
        
        $saved = 0;
        foreach (array_slice($articles, 0, $limit) as $articleData) {
            try {
                Article::create($articleData);
                $saved++;
            } catch (\Exception $e) {
                // 跳过重复的文章
            }
        }
        
        return $saved;
    }
    
    public function fetchProjects(string $topic, int $limit = 10): int
    {
        $projects = [
            [
                'name' => 'langchain',
                'full_name' => 'langchain-ai/langchain',
                'description' => "Building applications with LLMs through composability",
                'url' => 'https://github.com/langchain-ai/langchain-' . time(),
                'stars' => 75000,
                'forks' => 8500,
                'language' => 'Python',
            ],
            [
                'name' => 'llama.cpp',
                'full_name' => 'ggerganov/llama.cpp',
                'description' => "Port of Facebook's LLaMA model in C/C++",
                'url' => 'https://github.com/ggerganov/llama-cpp-' . time(),
                'stars' => 52000,
                'forks' => 6200,
                'language' => 'C++',
            ],
        ];
        
        $category = \App\Models\Category::firstOrCreate(['slug' => 'ai-tools'], ['name' => 'AI 工具']);
        
        $saved = 0;
        foreach (array_slice($projects, 0, $limit) as $projectData) {
            try {
                Project::create($projectData + ['category_id' => $category->id]);
                $saved++;
            } catch (\Exception $e) {
                // 跳过重复的项目
            }
        }
        
        return $saved;
    }
    
    public function fetchJobs(string $topic, int $limit = 10): int
    {
        $jobs = [
            [
                'title' => 'AI 算法工程师',
                'company_name' => '某 AI 科技公司-' . time(),
                'salary' => '30-60K·16 薪',
                'city' => '北京',
                'experience' => '3-5 年',
                'education' => '硕士',
                'description' => "负责大模型研发和优化...",
                'tags' => ['AI', '大模型', 'Python'],
            ],
            [
                'title' => 'AIGC 算法工程师',
                'company_name' => '某互联网公司-' . time(),
                'salary' => '25-50K·15 薪',
                'city' => '上海',
                'experience' => '1-3 年',
                'education' => '本科',
                'description' => "负责 AIGC 相关算法研发...",
                'tags' => ['AIGC', '算法'],
            ],
        ];
        
        $saved = 0;
        foreach (array_slice($jobs, 0, $limit) as $jobData) {
            try {
                JobListing::create($jobData);
                $saved++;
            } catch (\Exception $e) {
                // 跳过重复的职位
            }
        }
        
        return $saved;
    }
    
    public function fetchKnowledge(string $topic, int $knowledgeBaseId): int
    {
        $content = $this->generateContent($topic);
        
        KnowledgeDocument::create([
            'knowledge_base_id' => $knowledgeBaseId,
            'title' => $topic,
            'content' => $content,
            'file_type' => 'mock',
            'chunks' => preg_split('/\n\n+/', $content),
        ]);
        
        return 1;
    }
    
    private function generateContent(string $topic): string
    {
        return <<<HTML
<div style="font-family: sans-serif; line-height: 1.8;">
    <h1>{$topic} 详解</h1>
    <h2>一、简介</h2>
    <p>本文详细介绍{$topic}的相关知识和应用。</p>
    
    <h2>二、核心概念</h2>
    <p>{$topic}是当前 AI 领域的热门话题，具有重要的应用价值。</p>
    
    <h2>三、技术原理</h2>
    <p>详细技术原理说明...</p>
    
    <h2>四、应用案例</h2>
    <ul>
        <li>案例 1：xxx</li>
        <li>案例 2：xxx</li>
        <li>案例 3：xxx</li>
    </ul>
    
    <h2>五、总结</h2>
    <p>通过本文的学习，你应该已经掌握了{$topic}的核心知识。</p>
</div>
HTML;
    }
}
