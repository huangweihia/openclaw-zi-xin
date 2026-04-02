<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI 副业项目搜索服务
 * 
 * 从 GitHub 等平台搜索 AI 相关项目，生成类似"AI & 副业资讯日报"的邮件
 */
class AISideProjectService
{
    /**
     * 获取每日 AI 副业资讯（包含 3 个板块）
     */
    public function getDailyDigest(): array
    {
        return [
            'hot_projects' => $this->getHotAIProjects(),
            'side_hustles' => $this->getSideHustleIdeas(),
            'learning_resources' => $this->getLearningResources(),
        ];
    }

    /**
     * 获取热门 AI 项目 Top 10
     */
    public function getHotAIProjects(): array
    {
        $projects = [];
        
        try {
            // 获取 GitHub Trending（所有语言，因为 AI 项目多为 Python）
            $response = Http::withHeaders([
                'Accept' => 'text/html',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])->timeout(30)->get('https://github.com/trending?since=daily');

            if ($response->successful()) {
                $html = $response->body();
                $projects = $this->parseGitHubTrending($html);
            }
        } catch (\Exception $e) {
            Log::error('GitHub Trending 获取失败：' . $e->getMessage());
        }

        // 如果没有获取到，返回模拟数据
        if (empty($projects)) {
            $projects = $this->getDemoHotProjects();
        }

        return array_slice($projects, 0, 10);
    }

    /**
     * 解析 GitHub Trending 页面
     */
    protected function parseGitHubTrending(string $html): array
    {
        $projects = [];
        
        // 匹配项目卡片
        preg_match_all('/<article[^>]*>.*?<h2[^>]*>.*?<a[^>]*href="([^"]*)"[^>]*>([^<]*)<\/a>.*?<p[^>]*>([^<]*)<\/p>.*?star.*?>([^<]*)</s', $html, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $repo = trim($match[2] ?? '');
            $desc = trim(strip_tags($match[3] ?? ''));
            $stars = trim($match[4] ?? '0');
            $url = 'https://github.com' . ($match[1] ?? '');
            
            if (strlen($repo) < 3) continue;
            
            // 清理 star 数
            $stars = str_replace(',', '', $stars);
            $stars = (int)filter_var($stars, FILTER_SANITIZE_NUMBER_INT);
            
            $projects[] = [
                'rank' => count($projects) + 1,
                'name' => $repo,
                'full_name' => $repo,
                'description' => $desc ?: 'No description',
                'url' => $url,
                'stars' => $stars,
                'language' => $this->detectLanguage($repo, $desc),
                'tags' => $this->detectTags($repo, $desc),
            ];
        }

        return $projects;
    }

    /**
     * 获取副业/创收灵感
     */
    public function getSideHustleIdeas(): array
    {
        return [
            [
                'title' => 'AI 自动化服务',
                'description' => '为企业搭建 AI 工作流（客服、文档处理、数据分析）',
                'difficulty' => '中等',
                'revenue' => '¥5,000-50,000/项目',
                'platform' => 'Upwork/猪八戒/熟人推荐',
                'skills' => ['LangChain/Dify', 'API 集成', 'Prompt 工程'],
            ],
            [
                'title' => 'AI 内容创作',
                'description' => '用 AI 生成文章、视频脚本、社交媒体内容',
                'difficulty' => '入门',
                'revenue' => '¥2,000-20,000/月',
                'platform' => '小红书/抖音/公众号',
                'skills' => ['Prompt 技巧', '内容策划', '基础编辑'],
            ],
            [
                'title' => 'RAG 知识库搭建',
                'description' => '为企业定制私有知识库问答系统',
                'difficulty' => '进阶',
                'revenue' => '¥10,000-100,000/项目',
                'platform' => '企业直客/技术外包',
                'skills' => ['Python', 'LLM API', '向量数据库'],
            ],
            [
                'title' => 'AI 工具教程/课程',
                'description' => '制作 AI 工具使用教程，知识付费变现',
                'difficulty' => '入门',
                'revenue' => '¥5,000-30,000/月',
                'platform' => '小报童/知识星球/ Udemy',
                'skills' => ['内容制作', '营销推广', '课程设计'],
            ],
            [
                'title' => 'AI 插件/模板销售',
                'description' => '开发 Notion AI 模板、Chrome 插件等',
                'difficulty' => '中等',
                'revenue' => '¥3,000-50,000/月',
                'platform' => 'Gumroad/小商店',
                'skills' => ['产品设计', '前端开发', 'SEO'],
            ],
        ];
    }

    /**
     * 获取学习资源推荐
     */
    public function getLearningResources(): array
    {
        return [
            [
                'name' => 'RAG_Techniques',
                'description' => 'RAG 技术大全，含代码示例',
                'stars' => '26,139',
                'url' => 'https://github.com/run-llama/llama_index',
                'tags' => ['RAG', 'Tutorial'],
            ],
            [
                'name' => 'awesome-llm-apps',
                'description' => 'LLM 应用实例集合',
                'stars' => '102,963',
                'url' => 'https://github.com/awesome-llm-apps',
                'tags' => ['Examples', 'Apps'],
            ],
            [
                'name' => 'LangChain Docs',
                'description' => '官方文档 + 教程，学习 Agent 开发',
                'stars' => 'Official',
                'url' => 'https://python.langchain.com/',
                'tags' => ['Framework', 'Docs'],
            ],
            [
                'name' => 'Hugging Face Course',
                'description' => '免费 NLP/LLM 课程，从入门到进阶',
                'stars' => 'Free',
                'url' => 'https://huggingface.co/learn',
                'tags' => ['Course', 'Free'],
            ],
        ];
    }

    /**
     * 模拟热门项目数据
     */
    protected function getDemoHotProjects(): array
    {
        return [
            ['rank' => 1, 'name' => 'transformers', 'full_name' => 'huggingface/transformers', 'description' => 'State-of-the-art Machine Learning for Pytorch, TensorFlow, and JAX', 'url' => 'https://github.com/huggingface/transformers', 'stars' => 158153, 'language' => 'Python', 'tags' => ['LLM', 'Deep Learning', 'NLP']],
            ['rank' => 2, 'name' => 'langflow', 'full_name' => 'langflow-ai/langflow', 'description' => 'Build and deploy AI-powered agents and workflows visually', 'url' => 'https://github.com/langflow-ai/langflow', 'stars' => 145957, 'language' => 'Python', 'tags' => ['Agent', 'Low-Code', 'Workflow']],
            ['rank' => 3, 'name' => 'dify', 'full_name' => 'langgenius/dify', 'description' => 'Production-ready platform for agentic workflow development', 'url' => 'https://github.com/langgenius/dify', 'stars' => 133752, 'language' => 'TypeScript', 'tags' => ['Agent', 'RAG', 'No-Code']],
            ['rank' => 4, 'name' => 'ollama', 'full_name' => 'ollama/ollama', 'description' => 'Get up and running with Llama 3.2, Mistral, and other large language models', 'url' => 'https://github.com/ollama/ollama', 'stars' => 98234, 'language' => 'Go', 'tags' => ['LLM', 'Local', 'Inference']],
            ['rank' => 5, 'name' => 'langchain', 'full_name' => 'langchain-ai/langchain', 'description' => 'Building applications with LLMs through composability', 'url' => 'https://github.com/langchain-ai/langchain', 'stars' => 87654, 'language' => 'Python', 'tags' => ['Framework', 'Agent', 'RAG']],
            ['rank' => 6, 'name' => 'anything-llm', 'full_name' => 'Mintplex-Labs/anything-llm', 'description' => 'All-in-one AI application for your entire company', 'url' => 'https://github.com/Mintplex-Labs/anything-llm', 'stars' => 65432, 'language' => 'JavaScript', 'tags' => ['Enterprise', 'RAG', 'Chatbot']],
            ['rank' => 7, 'name' => 'comfyui', 'full_name' => 'comfyanonymous/ComfyUI', 'description' => 'The most powerful and modular diffusion model GUI and backend', 'url' => 'https://github.com/comfyanonymous/ComfyUI', 'stars' => 54321, 'language' => 'Python', 'tags' => ['Image', 'Stable Diffusion', 'GUI']],
            ['rank' => 8, 'name' => 'open-webui', 'full_name' => 'open-webui/open-webui', 'description' => 'User-friendly WebUI for LLMs', 'url' => 'https://github.com/open-webui/open-webui', 'stars' => 43210, 'language' => 'Python', 'tags' => ['UI', 'Chatbot', 'Self-hosted']],
            ['rank' => 9, 'name' => 'lobe-chat', 'full_name' => 'lobehub/lobe-chat', 'description' => 'An open-source, modern-design ChatGPT/LLMs UI/Framework', 'url' => 'https://github.com/lobehub/lobe-chat', 'stars' => 38765, 'language' => 'TypeScript', 'tags' => ['Chatbot', 'UI', 'Framework']],
            ['rank' => 10, 'name' => 'maxun', 'full_name' => 'getmaxun/maxun', 'description' => 'Open Source No-Code Web Scraping Platform', 'url' => 'https://github.com/getmaxun/maxun', 'stars' => 32109, 'language' => 'TypeScript', 'tags' => ['Scraping', 'No-Code', 'Automation']],
        ];
    }

    /**
     * 检测项目语言
     */
    protected function detectLanguage(string $name, string $desc): string
    {
        $keywords = [
            'Python' => ['python', 'pytorch', 'tensorflow', 'fastapi'],
            'TypeScript' => ['typescript', 'react', 'vue', 'nextjs'],
            'Go' => ['golang', 'go'],
            'Rust' => ['rust'],
            'JavaScript' => ['javascript', 'node', 'npm'],
        ];
        
        $text = strtolower($name . ' ' . $desc);
        
        foreach ($keywords as $lang => $words) {
            foreach ($words as $word) {
                if (strpos($text, $word) !== false) {
                    return $lang;
                }
            }
        }
        
        return 'Unknown';
    }

    /**
     * 检测项目标签
     */
    protected function detectTags(string $name, string $desc): array
    {
        $tags = [];
        $text = strtolower($name . ' ' . $desc);
        
        $tagMap = [
            'LLM' => ['llm', 'language model', 'gpt', 'transformer'],
            'RAG' => ['rag', 'retrieval', 'vector'],
            'Agent' => ['agent', 'autonomous', 'workflow'],
            'Deep Learning' => ['deep learning', 'neural', 'cnn', 'rnn'],
            'NLP' => ['nlp', 'natural language', 'text'],
            'Low-Code' => ['low-code', 'no-code', 'visual', 'drag'],
            'Chatbot' => ['chat', 'chatbot', 'conversation'],
            'Image' => ['image', 'diffusion', 'stable diffusion', 'midjourney'],
        ];
        
        foreach ($tagMap as $tag => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $tags[] = $tag;
                    break;
                }
            }
        }
        
        return array_slice(array_unique($tags), 0, 4);
    }

    /**
     * 生成邮件内容（按照截图样式）
     */
    public function generateDailyDigestEmail(array $data): string
    {
        $date = now()->format('Y-m-d');
        $weekday = now()->locale('zh_CN')->isoFormat('dddd');
        
        $content = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif; background: #1a1a2e; color: #eaeaea; line-height: 1.6; }
        .container { max-width: 650px; margin: 0 auto; background: #16213e; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 35px 30px; text-align: center; }
        .header h1 { font-size: 24px; margin-bottom: 8px; }
        .header p { opacity: 0.9; font-size: 14px; }
        .section { padding: 25px 30px; border-bottom: 1px solid #2a2a4a; }
        .section-title { font-size: 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .project-item { background: #1a1a2e; border-left: 3px solid #667eea; padding: 18px; margin-bottom: 15px; border-radius: 0 8px 8px 0; }
        .project-rank { font-size: 14px; color: #667eea; font-weight: bold; margin-bottom: 8px; }
        .project-name { font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 6px; }
        .project-name a { color: #667eea; text-decoration: none; }
        .project-name a:hover { text-decoration: underline; }
        .project-desc { font-size: 13px; color: #a0a0a0; margin-bottom: 12px; }
        .project-meta { display: flex; flex-wrap: wrap; gap: 10px; font-size: 12px; }
        .meta-item { display: flex; align-items: center; gap: 5px; }
        .language { color: #fbbf24; }
        .stars { color: #fbbf24; }
        .tag { background: #2a2a4a; color: #a0a0a0; padding: 3px 10px; border-radius: 12px; }
        
        /* 副业灵感卡片 */
        .hustle-card { background: linear-gradient(145deg, #1a1a2e 0%, #2a2a4a 100%); padding: 20px; border-radius: 10px; margin-bottom: 15px; }
        .hustle-title { font-size: 16px; font-weight: 600; color: #fff; margin-bottom: 8px; }
        .hustle-desc { font-size: 13px; color: #a0a0a0; margin-bottom: 12px; }
        .hustle-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; }
        .hustle-item { color: #a0a0a0; }
        .hustle-item strong { color: #667eea; }
        .hustle-skills { margin-top: 12px; display: flex; flex-wrap: wrap; gap: 6px; }
        .skill-tag { background: #667eea; color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 11px; }
        
        /* 学习资源 */
        .resource-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #2a2a4a; }
        .resource-item:last-child { border-bottom: none; }
        .resource-info { flex: 1; }
        .resource-name { font-size: 14px; font-weight: 600; color: #fff; margin-bottom: 4px; }
        .resource-desc { font-size: 12px; color: #a0a0a0; }
        .resource-meta { text-align: right; font-size: 12px; }
        .resource-stars { color: #fbbf24; margin-bottom: 4px; }
        .resource-tags { display: flex; gap: 5px; justify-content: flex-end; }
        
        .footer { background: #0f0f1a; padding: 25px 30px; text-align: center; font-size: 12px; color: #666; }
        .footer p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🤖 AI & 副业资讯日报</h1>
            <p>{$date} | {$weekday} · 精选 GitHub 热门项目 + 变现灵感</p>
        </div>
        
        <!-- 热门 AI 项目 -->
        <div class='section'>
            <div class='section-title'>🔥 热门 AI 项目 Top 10</div>";
        
        foreach ($data['hot_projects'] as $project) {
            $tags = implode(' · ', $project['tags'] ?: ['AI']);
            $content .= "<div class='project-item'>
                <div class='project-rank'>#" . $project['rank'] . "</div>
                <div class='project-name'><a href='" . htmlspecialchars($project['url']) . "' target='_blank'>" . htmlspecialchars($project['name']) . "</a></div>
                <div class='project-desc'>" . htmlspecialchars($project['description']) . "</div>
                <div class='project-meta'>
                    <span class='meta-item language'>🐍 " . htmlspecialchars($project['language']) . "</span>
                    <span class='meta-item stars'>⭐ " . number_format($project['stars']) . "</span>
                    <span class='tag'>" . htmlspecialchars($tags) . "</span>
                </div>
            </div>";
        }
        
        $content .= "</div>
        
        <!-- 副业/创收灵感 -->
        <div class='section'>
            <div class='section-title'>💰 副业/创收灵感</div>";
        
        foreach ($data['side_hustles'] as $hustle) {
            $skills = implode(' · ', $hustle['skills']);
            $content .= "<div class='hustle-card'>
                <div class='hustle-title'>" . htmlspecialchars($hustle['title']) . "</div>
                <div class='hustle-desc'>" . htmlspecialchars($hustle['description']) . "</div>
                <div class='hustle-grid'>
                    <div class='hustle-item'><strong>难度</strong> " . htmlspecialchars($hustle['difficulty']) . "</div>
                    <div class='hustle-item'><strong>变现方式</strong> " . htmlspecialchars($hustle['revenue']) . "</div>
                    <div class='hustle-item'><strong>平台</strong> " . htmlspecialchars($hustle['platform']) . "</div>
                </div>
                <div class='hustle-skills'>
                    <span class='skill-tag'>" . htmlspecialchars($skills) . "</span>
                </div>
            </div>";
        }
        
        $content .= "</div>
        
        <!-- 学习资源推荐 -->
        <div class='section'>
            <div class='section-title'>📚 学习资源推荐</div>";
        
        foreach ($data['learning_resources'] as $resource) {
            $tags = implode(' · ', $resource['tags']);
            $content .= "<div class='resource-item'>
                <div class='resource-info'>
                    <div class='resource-name'>" . htmlspecialchars($resource['name']) . "</div>
                    <div class='resource-desc'>" . htmlspecialchars($resource['description']) . "</div>
                </div>
                <div class='resource-meta'>
                    <div class='resource-stars'>⭐ " . htmlspecialchars($resource['stars']) . "</div>
                    <div class='resource-tags'>
                        <span class='tag'>" . htmlspecialchars($tags) . "</span>
                    </div>
                </div>
            </div>";
        }
        
        $content .= "</div>
        
        <div class='footer'>
            <p>此邮件由 AI 自动生成 | 数据来源：GitHub API</p>
            <p>如有疑问请回复此邮件</p>
            <p style='margin-top: 15px; opacity: 0.6;'>© " . date('Y') . " AI 副业情报局</p>
        </div>
    </div>
</body>
</html>";
        
        return $content;
    }
}
