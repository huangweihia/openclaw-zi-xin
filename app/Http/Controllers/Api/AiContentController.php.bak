<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Project;
use App\Models\JobListing;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeBase;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiContentController extends Controller
{
    /**
     * 接收 OpenClaw 推送的 AI 内容 方便统计数据
     */
    public function storeContent(Request $request)
    {
        Log::info("📥 ==================== API 接收请求 ====================");
        Log::info("📥 请求时间：" . now()->format('Y-m-d H:i:s'));
        Log::info("📥 Token: " . $request->header('X-API-Token'));
        Log::info("📥 Content-Type: " . $request->header('Content-Type'));
        
        // 验证 Token（简单认证）
        $token = $request->header('X-API-Token');
        $expectedToken = env('OPENCLAW_WEBHOOK_TOKEN', 'openclaw-ai-fetcher-2026');
        
        Log::info("📥 期望 Token: " . $expectedToken);
        Log::info("📥 Token 匹配：" . ($token === $expectedToken ? '✅' : '❌'));
        
        if ($token !== $expectedToken) {
            Log::error("❌ Token 认证失败");
            return response()->json(['success' => false, 'message' => '认证失败'], 401);
        }
        
        $data = $request->json()->all();
        $typeRaw = strtolower((string) ($data['type'] ?? ''));
        // OpenClaw / 外部脚本可能用不同别名
        $type = match ($typeRaw) {
            'job', 'position', 'positions' => 'jobs',
            'article' => 'articles',
            'project' => 'projects',
            default => $typeRaw,
        };

        Log::info("📥 数据类型：" . $type);
        Log::info("📥 数据数量：" . count($data['items'] ?? []));
        Log::info("📥 完整数据：" . json_encode($data, JSON_UNESCAPED_UNICODE));
        
        try {
            switch ($type) {
                case 'articles':
                    Log::info("📝 开始保存文章...");
                    $result = $this->saveArticles($data['items'] ?? []);
                    Log::info("✅ 文章保存完成：" . json_encode($result));
                    return $result;
                    
                case 'projects':
                    Log::info("💻 开始保存项目...");
                    $result = $this->saveProjects($data['items'] ?? []);
                    Log::info("✅ 项目保存完成：" . json_encode($result));
                    return $result;
                    
                case 'jobs':
                    Log::info("💼 开始保存职位...");
                    $result = $this->saveJobs($data['items'] ?? []);
                    Log::info("✅ 职位保存完成：" . json_encode($result));
                    return $result;
                    
                case 'knowledge':
                    Log::info("📚 开始保存知识库...");
                    $result = $this->saveKnowledge($data['items'] ?? []);
                    Log::info("✅ 知识库保存完成：" . json_encode($result));
                    return $result;
                    
                case 'learning_materials':
                    Log::info("📖 开始保存学习资料...");
                    $result = $this->saveLearningMaterials($data['items'] ?? []);
                    Log::info("✅ 学习资料保存完成：" . json_encode($result));
                    return $result;
                    
                case 'platform_articles':
                    Log::info("📰 开始保存多平台文章...");
                    $result = $this->savePlatformArticles($data['items'] ?? []);
                    Log::info("✅ 多平台文章保存完成：" . json_encode($result));
                    return $result;
                    
                default:
                    Log::error("❌ 未知类型：" . $type);
                    return response()->json(['success' => false, 'message' => '未知类型'], 400);
            }
        } catch (\Exception $e) {
            Log::error("❌ 保存失败：" . $e->getMessage());
            Log::error("❌ 错误堆栈：" . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * 保存文章
     */
    protected function saveArticles(array $items)
    {
        Log::info("📝 开始保存文章，数量：" . count($items));
        $saved = 0;
        $failed = 0;
        
        foreach ($items as $index => $item) {
            try {
                Log::info("📝 [{$index}] 保存文章：" . ($item['title'] ?? '无标题'));
                
                $article = Article::firstOrCreate(
                    ['source_url' => $item['url'] ?? md5($item['title'])],
                    [
                        'title' => $item['title'] ?? '无标题',
                        'slug' => \Illuminate\Support\Str::slug($item['title']) . '-' . time() . '-' . rand(1000, 9999),
                        'summary' => $item['summary'] ?? '',
                        'content' => $item['content'] ?? '',
                        'cover_image' => $item['cover_image'] ?? null,
                        'is_published' => true,
                        'published_at' => now(),
                    ]
                );
                
                Log::info("✅ [{$index}] 文章保存成功，ID: " . $article->id);
                $saved++;
            } catch (\Exception $e) {
                Log::error("❌ [{$index}] 保存文章失败：" . $e->getMessage());
                Log::error("❌ 文章数据：" . json_encode($item, JSON_UNESCAPED_UNICODE));
                $failed++;
            }
        }
        
        Log::info("📝 文章保存完成：成功 {$saved}, 失败 {$failed}");
        
        return response()->json([
            'success' => true,
            'message' => "成功保存 {$saved} 篇文章",
            'saved' => $saved,
            'failed' => $failed
        ]);
    }
    
    /**
     * 保存项目
     */
    protected function saveProjects(array $items)
    {
        $category = Category::firstOrCreate(
            ['slug' => 'ai-tools'],
            ['name' => 'AI 工具']
        );
        
        $saved = 0;
        foreach ($items as $item) {
            try {
                Project::firstOrCreate(
                    ['url' => $item['url'] ?? md5($item['name'])],
                    [
                        'name' => $item['name'] ?? '未知项目',
                        'full_name' => $item['name'] ?? '未知项目',
                        'description' => $item['description'] ?? '暂无描述',
                        'stars' => (int) ($item['stars'] ?? 0),
                        'forks' => (int) ($item['forks'] ?? 0),
                        'language' => $item['language'] ?? null,
                        'category_id' => $category->id,
                        'monetization' => 'medium',
                        'difficulty' => 'medium',
                        'is_featured' => ($item['stars'] ?? 0) > 5000,
                        'collected_at' => now(),
                    ]
                );
                $saved++;
            } catch (\Exception $e) {
                Log::error("保存项目失败：" . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "成功保存 {$saved} 个项目",
            'saved' => $saved
        ]);
    }
    
    /**
     * 保存职位
     */
    protected function saveJobs(array $items)
    {
        Log::info("💼 开始保存职位，数量：" . count($items));
        
        $adminUser = \App\Models\User::where('role', 'admin')->first();
        $firstUser = \App\Models\User::query()->orderBy('id')->first();
        $userId = $adminUser?->id ?? $firstUser?->id;
        if (! $userId) {
            Log::error('💼 无可用用户，无法写入职位（请先创建管理员）');

            return response()->json(['success' => false, 'message' => '数据库中无用户，无法创建职位'], 500);
        }
        Log::info("💼 使用 user_id: {$userId}");
        
        $saved = 0;
        $failed = 0;
        
        foreach ($items as $index => $item) {
            try {
                Log::info("💼 [{$index}] 保存职位：" . ($item['title'] ?? '无标题') . " - " . ($item['company_name'] ?? '未知公司'));
                
                // 优先使用 url 去重，如果没有则使用 source_url
                $uniqueUrl = $item['url'] ?? $item['source_url'] ?? null;
                
                // 如果有 URL，先检查是否已存在
                if ($uniqueUrl) {
                    $exists = \App\Models\Job::where('source_url', $uniqueUrl)->exists();
                    if ($exists) {
                        Log::info("⏭️ [{$index}] 职位已存在，跳过：" . $uniqueUrl);
                        continue;
                    }
                }
                
                $desc = $item['description'] ?? $item['content'] ?? '';
                $job = \App\Models\Job::create([
                    'user_id' => $userId,
                    'title' => $item['title'] ?? '未知职位',
                    'company_name' => $item['company_name'] ?? $item['company'] ?? '未知公司',
                    'location' => $item['city'] ?? $item['location'] ?? '不限',
                    'salary_range' => $item['salary'] ?? $item['salary_range'] ?? '面议',
                    'requirements' => $item['requirements'] ?? $desc,
                    'description' => $desc,
                    'source_url' => $uniqueUrl ? \Illuminate\Support\Str::limit($uniqueUrl, 255, '') : null,
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                
                Log::info("✅ [{$index}] 职位保存成功，ID: " . $job->id . ", URL: " . ($uniqueUrl ?? '无'));
                $saved++;
            } catch (\Exception $e) {
                Log::error("❌ [{$index}] 保存职位失败：" . $e->getMessage());
                Log::error("❌ 职位数据：" . json_encode($item, JSON_UNESCAPED_UNICODE));
                $failed++;
            }
        }
        
        Log::info("💼 职位保存完成：成功 {$saved}, 失败 {$failed}");
        
        return response()->json([
            'success' => true,
            'message' => "成功保存 {$saved} 个职位",
            'saved' => $saved,
            'failed' => $failed
        ]);
    }
    
    /**
     * 保存知识库
     */
    protected function saveKnowledge(array $items)
    {
        $adminUser = \App\Models\User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = \App\Models\User::first();
        }
        $userId = $adminUser ? $adminUser->id : 1;
        
        $knowledgeBase = KnowledgeBase::firstOrCreate(
            ['title' => 'AI 技术教程'],
            [
                'user_id' => $userId,
                'description' => 'AI 自动生成的技术文档',
                'category' => 'tech',
                'is_public' => true,
            ]
        );
        
        $saved = 0;
        foreach ($items as $item) {
            try {
                KnowledgeDocument::create([
                    'knowledge_base_id' => $knowledgeBase->id,
                    'title' => $item['title'] ?? '无标题',
                    'content' => $item['content'] ?? '',
                    'file_type' => 'ai_generated',
                    'chunks' => preg_split('/\n\n+/', $item['content'] ?? ''),
                ]);
                $saved++;
            } catch (\Exception $e) {
                Log::error("保存知识库失败：" . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "成功保存 {$saved} 篇知识库文档",
            'saved' => $saved
        ]);
    }
    
    /**
     * 保存学习资料（PDF/网盘/视频教程）
     */
    protected function saveLearningMaterials(array $items)
    {
        Log::info("📖 开始保存学习资料，数量：" . count($items));
        
        $adminUser = \App\Models\User::where('role', 'admin')->first();
        if (!$adminUser) {
            $adminUser = \App\Models\User::first();
        }
        $userId = $adminUser ? $adminUser->id : 1;
        
        // 获取或创建"AI 学习资料库"知识库
        $knowledgeBase = KnowledgeBase::firstOrCreate(
            ['title' => 'AI 学习资料库'],
            [
                'user_id' => $userId,
                'description' => '收集 AI 相关的 PDF 文档、网盘资源、视频教程、电子书等各种学习资料',
                'category' => 'learning_materials',
                'is_public' => true,
                'is_vip_only' => false,
            ]
        );
        
        $saved = 0;
        $failed = 0;
        
        foreach ($items as $index => $item) {
            try {
                Log::info("📖 [{$index}] 保存学习资料：" . ($item['title'] ?? '无标题'));
                
                // 检查是否已存在（通过标题或 URL）
                $exists = KnowledgeDocument::where('title', $item['title'] ?? '')
                    ->orWhere('content', 'like', '%' . ($item['url'] ?? '') . '%')
                    ->exists();
                
                if ($exists) {
                    Log::info("⏭️ [{$index}] 学习资料已存在，跳过");
                    continue;
                }
                
                // 构建 HTML 内容
                $resourceType = $item['resource_type'] ?? '其他';
                $resourceIcon = match($resourceType) {
                    'PDF' => '📄',
                    '网盘' => '💾',
                    '视频' => '🎥',
                    '电子书' => '📚',
                    default => '📎'
                };
                
                // 提前处理空合并运算，避免在 heredoc 中使用 ??
                $platform = $item['platform'] ?? '未知';
                $description = $item['description'] ?? '暂无详细描述';
                $url = $item['url'] ?? '#';
                $urlText = $item['url'] ?? '暂无链接';
                $updateTime = now()->format('Y-m-d H:i:s');
                
                $content = <<<HTML
<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.8; color: #1a202c;">
    <h1 style="font-size: 28px; margin-bottom: 20px; color: #2d3748;">{$resourceIcon} {$item['title']}</h1>
    
    <div style="background: linear-gradient(135deg, rgba(66, 153, 225, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); padding: 20px; border-radius: 8px; margin: 20px 0;">
        <p style="margin: 0;"><strong>资源类型：</strong>{$resourceType}</p>
        <p style="margin: 10px 0 0;"><strong>来源平台：</strong>{$platform}</p>
        <p style="margin: 10px 0 0;"><strong>更新时间：</strong>{$updateTime}</p>
    </div>
    
    <h2 style="font-size: 22px; margin: 30px 0 15px; color: #4a5568;">📝 资源描述</h2>
    <p style="line-height: 1.8;">{$description}</p>
    
    <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, rgba(72, 187, 120, 0.1) 0%, rgba(66, 153, 225, 0.1) 100%); border-radius: 8px; border: 1px solid rgba(72, 187, 120, 0.3);">
        <p style="margin: 0; color: #2f855a; font-weight: 600;">🔗 访问链接：</p>
        <p style="margin: 10px 0 0;">
            <a href="{$url}" target="_blank" style="color: #3182ce; text-decoration: none; word-break: break-all;">
                {$urlText}
            </a>
        </p>
    </div>
</div>
HTML;
                
                KnowledgeDocument::create([
                    'knowledge_base_id' => $knowledgeBase->id,
                    'title' => $item['title'] ?? '无标题',
                    'content' => $content,
                    'file_type' => strtolower($resourceType),
                    'chunks' => preg_split('/\n\n+/', $item['description'] ?? ''),
                ]);
                
                Log::info("✅ [{$index}] 学习资料保存成功，ID: " . ($knowledgeBase->id ?? 'unknown'));
                $saved++;
            } catch (\Exception $e) {
                Log::error("❌ [{$index}] 保存学习资料失败：" . $e->getMessage());
                Log::error("❌ 学习资料数据：" . json_encode($item, JSON_UNESCAPED_UNICODE));
                $failed++;
            }
        }
        
        Log::info("📖 学习资料保存完成：成功 {$saved}, 失败 {$failed}");
        
        return response()->json([
            'success' => true,
            'message' => "成功保存 {$saved} 个学习资料",
            'saved' => $saved,
            'failed' => $failed
        ]);
    }
    
    /**
     * 保存多平台文章（知乎/简书/掘金等）
     */
    protected function savePlatformArticles(array $items)
    {
        Log::info("📰 开始保存多平台文章，数量：" . count($items));
        
        $saved = 0;
        $failed = 0;
        
        // 按平台分组统计
        $platformStats = [];
        
        foreach ($items as $index => $item) {
            try {
                $platform = $item['platform'] ?? '未知平台';
                Log::info("📰 [{$index}] 保存{$platform}文章：" . ($item['title'] ?? '无标题'));
                
                // 检查是否已存在（通过 URL）
                if (!empty($item['url'])) {
                    $exists = Article::where('source_url', $item['url'])->exists();
                    if ($exists) {
                        Log::info("⏭️ [{$index}] 文章已存在，跳过");
                        continue;
                    }
                }
                
                // 获取或创建平台分类
                $platformSlug = 'platform-' . strtolower($platform);
                $category = Category::firstOrCreate(
                    ['slug' => $platformSlug],
                    ['name' => $platform . '精选']
                );
                
                $article = Article::create([
                    'category_id' => $category->id,
                    'title' => $item['title'] ?? '无标题',
                    'slug' => \Illuminate\Support\Str::slug($item['title']) . '-' . time() . '-' . rand(1000, 9999),
                    'summary' => $item['summary'] ?? '',
                    'content' => $this->buildPlatformArticleContent($item),
                    'source_url' => $item['url'] ?? null,
                    'cover_image' => null,
                    'is_published' => true,
                    'published_at' => now(),
                ]);
                
                Log::info("✅ [{$index}] {$platform}文章保存成功，ID: " . $article->id);
                $saved++;
                
                // 统计
                $platformStats[$platform] = ($platformStats[$platform] ?? 0) + 1;
            } catch (\Exception $e) {
                Log::error("❌ [{$index}] 保存文章失败：" . $e->getMessage());
                Log::error("❌ 文章数据：" . json_encode($item, JSON_UNESCAPED_UNICODE));
                $failed++;
            }
        }
        
        Log::info("📰 多平台文章保存完成：成功 {$saved}, 失败 {$failed}");
        Log::info("📊 平台统计：" . json_encode($platformStats, JSON_UNESCAPED_UNICODE));
        
        return response()->json([
            'success' => true,
            'message' => "成功保存 {$saved} 篇多平台文章",
            'saved' => $saved,
            'failed' => $failed,
            'platform_stats' => $platformStats
        ]);
    }
    
    /**
     * 构建多平台文章内容 HTML
     */
    protected function buildPlatformArticleContent(array $item): string
    {
        $platform = $item['platform'] ?? '未知';
        $platformColors = [
            '知乎' => '#0084ff',
            '简书' => '#ea6f5a',
            '掘金' => '#1e80ff',
            '机器之心' => '#000000',
            '量子位' => '#0066cc',
        ];
        $color = $platformColors[$platform] ?? '#666666';
        
        return <<<HTML
<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.8; color: #1a202c;">
    <div style="display: flex; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid {$color};">
        <span style="display: inline-block; width: 40px; height: 40px; background: {$color}; color: white; border-radius: 8px; text-align: center; line-height: 40px; font-weight: bold; margin-right: 15px;">{$platform[0]}</span>
        <div>
            <h1 style="font-size: 24px; margin: 0; color: #2d3748;">{$item['title']}</h1>
            <p style="margin: 5px 0 0; color: #718096; font-size: 14px;">来源：{$platform} | 作者：{$item['author'] ?? '佚名'}</p>
        </div>
    </div>
    
    <h2 style="font-size: 20px; margin: 25px 0 15px; color: #4a5568; border-left: 4px solid {$color}; padding-left: 12px;">文章摘要</h2>
    <p style="line-height: 1.8; color: #2d3748;">{$item['summary'] ?? '暂无摘要'}</p>
    
    <div style="margin-top: 30px; padding: 20px; background: #f7fafc; border-radius: 8px;">
        <p style="margin: 0; color: #4a5568; font-weight: 600;">📌 原文链接：</p>
        <p style="margin: 10px 0 0;">
            <a href="{$item['url'] ?? '#'}" target="_blank" style="color: {$color}; text-decoration: none; word-break: break-all;">
                {$item['url'] ?? '暂无链接'}
            </a>
        </p>
    </div>
    
    <div style="margin-top: 20px; padding: 15px; background: linear-gradient(135deg, rgba({$this->hexToRgb($color)}, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-radius: 8px; border: 1px solid rgba({$this->hexToRgb($color)}, 0.3);">
        <p style="margin: 0; color: {$color}; font-weight: 600;">💡 提示：</p>
        <p style="margin: 10px 0 0; color: #4a5568; font-size: 14px;">点击原文链接查看完整内容，支持原创作者！</p>
    </div>
</div>
HTML;
    }
    
    /**
     * 十六进制颜色转 RGB
     */
    protected function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "{$r}, {$g}, {$b}";
    }
}
