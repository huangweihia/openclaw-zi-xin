<?php

namespace App\Services;

use App\Models\KnowledgeBase;
use App\Models\KnowledgeDocument;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KnowledgeFetchService
{
    /**
     * 自动采集 AI 相关知识库内容
     */
    public function fetchKnowledge(): int
    {
        $this->log('📚 开始采集 AI 相关知识库...');
        
        // AI 相关主题
        $topics = [
            'AI 工具教程' => [
                'ChatGPT 使用技巧',
                'Midjourney 入门指南',
                'Stable Diffusion 部署教程',
                'LangChain 开发指南',
                'AI 绘画进阶技巧',
            ],
            '副业变现' => [
                'AI 写作变现方法',
                'AI 绘画接单技巧',
                'AI 视频制作变现',
                'AI 咨询服务',
                'AI 培训课程制作',
            ],
            '技术教程' => [
                'Python AI 开发',
                '大模型微调',
                'RAG 知识库搭建',
                'AI Agent 开发',
                '向量数据库使用',
            ],
        ];
        
        $saved = 0;
        
        foreach ($topics as $category => $titles) {
            foreach ($titles as $title) {
                try {
                    // 检查是否已存在
                    $exists = KnowledgeDocument::where('title', 'like', "%{$title}%")
                        ->exists();
                    
                    if ($exists) {
                        $this->log("⏭️ 跳过已存在：{$title}");
                        continue;
                    }
                    
                    // 生成内容
                    $content = $this->generateContent($title, $category);
                    
                    // 创建文档
                    KnowledgeDocument::create([
                        'knowledge_base_id' => $this->getOrCreateKnowledgeBase($category),
                        'title' => $title,
                        'content' => $content,
                        'file_type' => 'generated',
                        'chunks' => $this->chunkContent($content),
                    ]);
                    
                    $saved++;
                    $this->log("✅ 保存：{$title}");
                    
                } catch (\Exception $e) {
                    $this->log("❌ 采集 {$title} 失败：" . $e->getMessage());
                }
            }
        }
        
        $this->log("✅ 知识库采集完成，共保存 {$saved} 篇文章");
        
        return $saved;
    }
    
    /**
     * 获取或创建知识库
     */
    private function getOrCreateKnowledgeBase(string $category): int
    {
        // 获取第一个管理员用户
        $userId = \App\Models\User::where('role', 'admin')->value('id') ?? 1;
        
        $kb = KnowledgeBase::firstOrCreate(
            ['title' => $category],
            [
                'user_id' => $userId,
                'description' => "自动采集的{$category}相关内容",
                'category' => 'tech',
                'is_public' => true,
                'is_vip_only' => false,
            ]
        );
        
        return $kb->id;
    }
    
    /**
     * 生成内容（模拟）
     */
    private function generateContent(string $title, string $category): string
    {
        return <<<HTML
<div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.8; color: #1a202c;">
    <h1 style="font-size: 28px; margin-bottom: 20px; color: #2d3748;">{$title}</h1>
    
    <h2 style="font-size: 22px; margin: 30px 0 15px; color: #4a5568;">一、简介</h2>
    <p>本文详细介绍{$title}的完整教程，从入门到精通，帮助你快速掌握相关技能。</p>
    
    <h2 style="font-size: 22px; margin: 30px 0 15px; color: #4a5568;">二、准备工作</h2>
    <ul style="list-style: disc; padding-left: 20px;">
        <li>了解基础知识</li>
        <li>准备必要的工具和环境</li>
        <li>安装相关软件</li>
    </ul>
    
    <h2 style="font-size: 22px; margin: 30px 0 15px; color: #4a5568;">三、详细步骤</h2>
    <h3 style="font-size: 18px; margin: 20px 0 10px; color: #4a5568;">3.1 第一步</h3>
    <p>详细说明第一步的操作方法和注意事项。</p>
    
    <h3 style="font-size: 18px; margin: 20px 0 10px; color: #4a5568;">3.2 第二步</h3>
    <p>详细说明第二步的操作方法和注意事项。</p>
    
    <h3 style="font-size: 18px; margin: 20px 0 10px; color: #4a5568;">3.3 第三步</h3>
    <p>详细说明第三步的操作方法和注意事项。</p>
    
    <h2 style="font-size: 22px; margin: 30px 0 15px; color: #4a5568;">四、常见问题</h2>
    <div style="background: #f7fafc; padding: 15px; border-left: 4px solid #4299e1; border-radius: 4px; margin: 15px 0;">
        <p style="margin: 0;"><strong>Q：</strong>常见问题 1？</p>
        <p style="margin: 10px 0 0;"><strong>A：</strong>详细解答。</p>
    </div>
    
    <h2 style="font-size: 22px; margin: 30px 0 15px; color: #4a5568;">五、总结</h2>
    <p>通过本文的学习，你应该已经掌握了{$title}的核心技能。建议多加练习，熟能生巧。</p>
    
    <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%); border-radius: 8px; border: 1px solid rgba(99, 102, 241, 0.3);">
        <p style="margin: 0; color: #667eea; font-weight: 600;">💡 提示：</p>
        <p style="margin: 10px 0 0; color: #4a5568;">实践是学习的最好方法，建议立即动手尝试！</p>
    </div>
</div>
HTML;
    }
    
    /**
     * 分块内容
     */
    private function chunkContent(string $content): array
    {
        // 简单按段落分块
        return array_values(array_filter(
            preg_split('/<h[^>]*>.*?<\/h[^>]*>/s', $content)
        ));
    }
    
    /**
     * 记录日志
     */
    private function log(string $message): void
    {
        echo "[{}] {$message}\n";
    }
    
    /**
     * 执行完整采集
     */
    public function fetchAll(): int
    {
        return $this->fetchKnowledge();
    }
}
