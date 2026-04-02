<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Models\EmailLog;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 开始生成真实测试数据...');
        
        $this->command->info('📁 创建分类...');
        $this->createCategories();
        
        $this->command->info('👥 创建用户...');
        $users = $this->createUsers();
        
        $this->command->info('🚀 创建真实项目数据...');
        $this->createProjects();
        
        $this->command->info('📰 创建真实文章数据...');
        $this->createArticles($users);
        
        $this->command->info('📧 创建邮件日志...');
        $this->createEmailLogs();
        
        $this->command->info('');
        $this->command->info('✅ 测试数据生成完成！');
        $this->command->info('   分类：' . Category::count());
        $this->command->info('   用户：' . User::count());
        $this->command->info('   项目：' . Project::count());
        $this->command->info('   文章：' . Article::count());
        $this->command->info('   邮件日志：' . EmailLog::count());
    }

    private function createCategories(): void
    {
        $cats = [
            ['name' => 'AI 工具', 'slug' => 'ai-tools', 'description' => 'AI 相关工具和平台'],
            ['name' => '副业项目', 'slug' => 'side-projects', 'description' => '副业和创业项目'],
            ['name' => '学习资源', 'slug' => 'learning', 'description' => '教程和学习资料'],
            ['name' => '行业资讯', 'slug' => 'news', 'description' => 'AI 行业动态'],
            ['name' => '变现案例', 'slug' => 'monetization', 'description' => '成功案例分享'],
        ];

        foreach ($cats as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }

    private function createUsers(): array
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => '管理员', 'password' => bcrypt('password123'), 'role' => 'admin']
        );

        $vipUsers = [
            ['name' => '张三', 'email' => 'vip1@example.com'],
            ['name' => '李四', 'email' => 'vip2@example.com'],
            ['name' => '王五', 'email' => 'vip3@example.com'],
        ];

        foreach ($vipUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password123'),
                    'role' => 'vip',
                    'subscription_ends_at' => now()->addMonths(6),
                ]
            );
        }

        $normalUsers = [
            ['name' => '小明', 'email' => 'user1@example.com'],
            ['name' => '小红', 'email' => 'user2@example.com'],
            ['name' => '小刚', 'email' => 'user3@example.com'],
        ];

        foreach ($normalUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                ['name' => $userData['name'], 'password' => bcrypt('password123'), 'role' => 'user']
            );
        }

        return User::all()->toArray();
    }

    private function createProjects(): void
    {
        $projects = [
            [
                'name' => 'chatgpt-next-web',
                'full_name' => 'ChatGPT Next Web',
                'description' => '一键免费部署你的私人 ChatGPT 网页应用，支持 GPT4、Gemini Pro、Ollama 等模型',
                'url' => 'https://github.com/ChatGPTNextWeb/ChatGPT-Next-Web',
                'language' => 'TypeScript',
                'stars' => 68500,
                'forks' => 9200,
                'score' => 9.5,
                'tags' => ['GPT', 'Chatbot', 'Next.js', 'AI'],
                'monetization' => 'high',
                'difficulty' => 'easy',
                'revenue' => '5000-20000 元/月',
                'is_featured' => true,
            ],
            [
                'name' => 'stable-diffusion-webui',
                'full_name' => 'Stable Diffusion WebUI',
                'description' => '最流行的 Stable Diffusion 网页界面，支持文生图、图生图、ControlNet 等功能',
                'url' => 'https://github.com/AUTOMATIC1111/stable-diffusion-webui',
                'language' => 'Python',
                'stars' => 128000,
                'forks' => 26500,
                'score' => 9.8,
                'tags' => ['AI 绘画', 'Stable Diffusion', 'Python', '图像处理'],
                'monetization' => 'high',
                'difficulty' => 'medium',
                'revenue' => '10000-50000 元/月',
                'is_featured' => true,
            ],
            [
                'name' => 'langchain',
                'full_name' => 'LangChain',
                'description' => '构建大语言模型应用的框架，支持 RAG、Agent、记忆等功能，快速开发 AI 应用',
                'url' => 'https://github.com/langchain-ai/langchain',
                'language' => 'Python',
                'stars' => 82000,
                'forks' => 11800,
                'score' => 9.6,
                'tags' => ['LLM', 'RAG', 'Agent', 'Python'],
                'monetization' => 'high',
                'difficulty' => 'hard',
                'revenue' => '20000-100000 元/月',
                'is_featured' => true,
            ],
            [
                'name' => 'ollama',
                'full_name' => 'Ollama',
                'description' => '本地运行大语言模型的工具，支持 Llama2、Mistral 等模型，一键部署',
                'url' => 'https://github.com/ollama/ollama',
                'language' => 'Go',
                'stars' => 65000,
                'forks' => 4200,
                'score' => 9.3,
                'tags' => ['LLM', '本地部署', 'Go', 'AI'],
                'monetization' => 'medium',
                'difficulty' => 'easy',
                'revenue' => '3000-15000 元/月',
                'is_featured' => true,
            ],
            [
                'name' => 'midjourney-bot',
                'full_name' => 'Midjourney 代练机器人',
                'description' => '自动化 Midjourney 绘图任务，支持批量生成、自动 upscale，适合接商单',
                'url' => 'https://github.com/example/midjourney-bot',
                'language' => 'Python',
                'stars' => 3200,
                'forks' => 680,
                'score' => 7.8,
                'tags' => ['Midjourney', 'AI 绘画', '自动化', '副业'],
                'monetization' => 'high',
                'difficulty' => 'medium',
                'revenue' => '8000-30000 元/月',
                'is_featured' => false,
            ],
            [
                'name' => 'ai-content-writer',
                'full_name' => 'AI 内容写作助手',
                'description' => '基于 GPT-4 的自媒体写作工具，支持公众号、小红书、知乎等多平台文案生成',
                'url' => 'https://github.com/example/ai-content-writer',
                'language' => 'JavaScript',
                'stars' => 2800,
                'forks' => 520,
                'score' => 7.5,
                'tags' => ['GPT', '写作', '自媒体', '内容生成'],
                'monetization' => 'high',
                'difficulty' => 'easy',
                'revenue' => '5000-20000 元/月',
                'is_featured' => false,
            ],
            [
                'name' => 'rag-chatbot',
                'full_name' => '企业知识库问答机器人',
                'description' => '基于 RAG 技术的企业知识库问答系统，支持 PDF、Word、Excel 等文档解析',
                'url' => 'https://github.com/example/rag-chatbot',
                'language' => 'Python',
                'stars' => 4500,
                'forks' => 890,
                'score' => 8.2,
                'tags' => ['RAG', '知识库', '企业服务', 'Chatbot'],
                'monetization' => 'high',
                'difficulty' => 'hard',
                'revenue' => '20000-100000 元/项目',
                'is_featured' => true,
            ],
            [
                'name' => 'ai-video-generator',
                'full_name' => 'AI 短视频生成系统',
                'description' => '批量生成短视频，支持数字人、配音、字幕，适合抖音/快手/TikTok 运营',
                'url' => 'https://github.com/example/ai-video-generator',
                'language' => 'Python',
                'stars' => 5600,
                'forks' => 1200,
                'score' => 8.5,
                'tags' => ['AI 视频', '数字人', '自媒体', '批量生成'],
                'monetization' => 'high',
                'difficulty' => 'medium',
                'revenue' => '10000-50000 元/月',
                'is_featured' => true,
            ],
            [
                'name' => 'notion-ai-template',
                'full_name' => 'Notion AI 模板合集',
                'description' => '精选 Notion AI 模板，包括学习笔记、项目管理、内容规划等，提高生产力',
                'url' => 'https://github.com/example/notion-ai-template',
                'language' => 'Markdown',
                'stars' => 1800,
                'forks' => 320,
                'score' => 7.0,
                'tags' => ['Notion', 'AI', '模板', '生产力'],
                'monetization' => 'medium',
                'difficulty' => 'easy',
                'revenue' => '2000-8000 元/月',
                'is_featured' => false,
            ],
            [
                'name' => 'ai-trading-bot',
                'full_name' => 'AI 量化交易机器人',
                'description' => '基于机器语言的加密货币交易机器人，支持多交易所、多策略，自动化交易',
                'url' => 'https://github.com/example/ai-trading-bot',
                'language' => 'Python',
                'stars' => 6200,
                'forks' => 1500,
                'score' => 8.8,
                'tags' => ['AI', '量化交易', '加密货币', '自动化'],
                'monetization' => 'high',
                'difficulty' => 'hard',
                'revenue' => '50000-500000 元/月',
                'is_featured' => true,
            ],
        ];

        foreach ($projects as $project) {
            try {
                Project::firstOrCreate(
                    ['name' => $project['name']],
                    $project
                );
                $this->command->info("  ✅ " . $project['full_name']);
            } catch (\Exception $e) {
                $this->command->warn("  ⚠️ 项目创建失败：" . $project['full_name']);
            }
        }
    }

    private function createArticles(array $users): void
    {
        $articles = [
            [
                'title' => '2024 年最适合普通人的 10 个 AI 副业项目，月入过万不是梦',
                'slug' => 'top-10-ai-side-hustles-2024',
                'summary' => '盘点 2024 年门槛最低、变现最快的 AI 副业项目，包含详细教程和变现路径，总有一个适合你',
                'content' => '<p>随着 AI 技术的普及，越来越多的副业机会涌现。本文精选了 10 个最适合普通人的 AI 副业项目...</p>',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'side-projects')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'view_count' => 12580,
                'like_count' => 856,
            ],
            [
                'title' => '从零开始做 AI 绘画，我如何 3 个月月入 2 万的真实经历',
                'slug' => 'ai-art-monthly-income-story',
                'summary' => '真实案例分享，从 0 到 1 的完整过程，包含接单渠道、定价策略、客户维护等实战经验',
                'content' => '<p>3 个月前我还是一个 AI 小白，现在已经靠 AI 绘画月入 2 万+。这篇文章详细记录了我的成长历程...</p>',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'monetization')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'view_count' => 28900,
                'like_count' => 2150,
            ],
            [
                'title' => 'Midjourney 新手入门教程（2024 最新版），从注册到出图全流程',
                'slug' => 'midjourney-tutorial-2024',
                'summary' => '从账号注册、基础命令、参数详解到高级技巧，手把手教你使用 Midjourney 创作精美图片',
                'content' => '<p>本教程包含：账号注册、基础命令、参数详解、高级技巧、商业案例解析...</p>',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'learning')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(7),
                'view_count' => 45600,
                'like_count' => 3280,
            ],
            [
                'title' => 'GPT-4 提示词工程：写出高质量 Prompt 的 7 个核心技巧',
                'slug' => 'gpt4-prompt-engineering-tips',
                'summary' => '掌握这些技巧，让 AI 输出更符合你的预期，提高工作和学习效率',
                'content' => '<p>提示词工程是 AI 时代的核心技能之一。本文总结 7 个核心技巧...</p>',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'learning')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'view_count' => 38200,
                'like_count' => 2650,
            ],
            [
                'title' => 'AI 工具周报：第 12 期（3.17-3.24）Sora 开放测试、Claude 3 发布',
                'slug' => 'ai-tools-weekly-12',
                'summary' => '本周 AI 圈发生了很多大事：Sora 开放测试、Claude 3 发布、GPT-5 传闻、国内大模型新动态',
                'content' => '<p>本周 AI 圈发生了很多大事，让我们一起来看看...</p>',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'news')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(1),
                'view_count' => 15800,
                'like_count' => 920,
            ],
            [
                'title' => 'Stable Diffusion 本地部署完整指南（Windows/Mac/Linux）',
                'slug' => 'stable-diffusion-local-setup',
                'summary' => '全平台部署教程，包含常见问题解决、优化配置、模型下载等，新手也能轻松上手',
                'content' => '<p>Stable Diffusion 是最流行的开源 AI 绘画工具。本文详细介绍本地部署的完整流程...</p>',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'learning')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(14),
                'view_count' => 52300,
                'like_count' => 4150,
            ],
            [
                'title' => 'AI 写作变现：从入门到精通，公众号/知乎/小红书全平台攻略',
                'slug' => 'ai-writing-monetization-guide',
                'content' => '<p>AI 写作是目前门槛最低的 AI 副业。本文详细介绍各平台的变现方法...</p>',
                'summary' => '公众号文案、知乎回答、小红书笔记，全方位变现指南，包含实际案例和收入数据',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'monetization')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(20),
                'view_count' => 31500,
                'like_count' => 2380,
            ],
            [
                'title' => '2024 AI 行业趋势分析：普通人如何抓住这波红利',
                'slug' => 'ai-industry-trends-2024',
                'summary' => '深度分析 2024 年 AI 行业的发展方向和机会，帮助普通人找到适合自己的切入点',
                'content' => '<p>2024 年 AI 行业将呈现以下趋势...</p>',
                'author_id' => $users[0]['id'] ?? 1,
                'category_id' => Category::where('slug', 'news')->first()?->id,
                'is_published' => true,
                'published_at' => now()->subDays(30),
                'view_count' => 68900,
                'like_count' => 5200,
            ],
        ];

        foreach ($articles as $article) {
            try {
                Article::firstOrCreate(
                    ['slug' => $article['slug']],
                    $article
                );
                $this->command->info("  ✅ " . $article['title']);
            } catch (\Exception $e) {
                $this->command->warn("  ⚠️ 文章创建失败：" . $article['title']);
            }
        }
    }

    private function createEmailLogs(): void
    {
        $emails = [
            ['recipient' => '2801359160@qq.com', 'subject' => '🤖 AI 副业资讯日报 - 2026-03-24', 'type' => 'job_daily', 'status' => 'sent'],
            ['recipient' => '2801359160@qq.com', 'subject' => '🤖 AI 副业资讯日报 - 2026-03-23', 'type' => 'job_daily', 'status' => 'sent'],
            ['recipient' => 'vip1@example.com', 'subject' => '🎉 欢迎加入 AI 副业情报局！', 'type' => 'welcome', 'status' => 'sent'],
        ];

        foreach ($emails as $email) {
            EmailLog::create([
                'recipient' => $email['recipient'],
                'subject' => $email['subject'],
                'content' => '测试邮件内容...',
                'type' => $email['type'],
                'status' => $email['status'],
                'sent_at' => now()->subDays(rand(0, 7)),
            ]);
        }
    }
}
