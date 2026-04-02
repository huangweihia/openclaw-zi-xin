<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WechatBotService
{
    /**
     * 企业微信机器人 Webhook URL
     * 配置在 .env 文件中
     */
    protected string $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = (string) (config('services.wechat_bot.webhook_url') ?? '');
    }

    /**
     * 发送文本消息
     */
    public function sendText(string $content, array $mentionedList = []): bool
    {
        $payload = [
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
                'mentioned_list' => $mentionedList, // @特定用户
            ],
        ];

        return $this->send($payload);
    }

    /**
     * 发送 Markdown 消息
     */
    public function sendMarkdown(string $content): bool
    {
        $payload = [
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => $content,
            ],
        ];

        return $this->send($payload);
    }

    /**
     * 发送图文链接消息
     */
    public function sendLink(string $title, string $description, string $url, ?string $imageUrl = null): bool
    {
        $payload = [
            'msgtype' => 'link',
            'link' => [
                'title' => $title,
                'text' => $description,
                'picUrl' => $imageUrl,
                'messageUrl' => $url,
            ],
        ];

        return $this->send($payload);
    }

    /**
     * 发送图文卡片消息（推荐）
     */
    public function sendNewsCard(array $articles): bool
    {
        $payload = [
            'msgtype' => 'news',
            'news' => [
                'articles' => $articles,
            ],
        ];

        return $this->send($payload);
    }

    /**
     * 发送 AI 日报（组合消息）
     */
    public function sendDailyDigest(array $data): bool
    {
        $date = now()->format('Y-m-d');
        
        $markdown = "## 📰 AI 副业情报 · 每日简报\n";
        $markdown .= "> 日期：{$date}\n\n";
        
        // 热门项目
        if (!empty($data['projects'])) {
            $markdown .= "### 🔥 热门项目\n";
            foreach (array_slice($data['projects'], 0, 5) as $project) {
                $markdown .= "- **{$project['name']}** (⭐ {$project['stars']})\n";
                $markdown .= "  {$project['description']}\n";
            }
            $markdown .= "\n";
        }
        
        // 新案例
        if (!empty($data['cases'])) {
            $markdown .= "### 💰 新案例上架\n";
            foreach (array_slice($data['cases'], 0, 3) as $case) {
                $markdown .= "- {$case['title']}\n";
                $markdown .= "  预估月收入：{$case['estimated_monthly_income']}元\n";
            }
            $markdown .= "\n";
        }
        
        // 新工具
        if (!empty($data['tools'])) {
            $markdown .= "### 🛠️ 新工具推荐\n";
            foreach (array_slice($data['tools'], 0, 3) as $tool) {
                $markdown .= "- **{$tool['tool_name']}**: {$tool['description']}\n";
            }
            $markdown .= "\n";
        }
        
        $markdown .= "---\n";
        $markdown .= "👉 访问网站查看更多：https://aifyqbj.calmpu.com/max";

        return $this->sendMarkdown($markdown);
    }

    /**
     * 发送新内容通知
     */
    public function sendNewContentNotification(string $type, string $title, string $url): bool
    {
        $icons = [
            'case' => '💰',
            'tool' => '🛠️',
            'sop' => '📱',
            'resource' => '📦',
            'article' => '📰',
        ];

        $icon = $icons[$type] ?? '🔔';
        $typeNames = [
            'case' => '新案例',
            'tool' => '新工具',
            'sop' => '新 SOP',
            'resource' => '新资源',
            'article' => '新文章',
        ];

        $markdown = "{$icon} **{$typeNames[$type]} 上架**\n\n";
        $markdown .= "**{$title}**\n\n";
        $markdown .= "[👉 点击查看详情]({$url})\n\n";
        $markdown .= "---\n";
        $markdown .= "VIP 会员专属内容，立即查看 →";

        return $this->sendMarkdown($markdown);
    }

    /**
     * 发送 SVIP 定制报告
     */
    public function sendSvipReport(string $title, string $content, array $attachments = []): bool
    {
        $markdown = "## 👑 SVIP 专属报告\n\n";
        $markdown .= "**{$title}**\n\n";
        $markdown .= $content;
        $markdown .= "\n\n---\n";
        $markdown .= "📊 报告由 OpenClaw 自动生成 · 如有疑问请联系管理员";

        $success = $this->sendMarkdown($markdown);

        // 如果有附件（文件链接），再发一条
        if ($success && !empty($attachments)) {
            foreach ($attachments as $file) {
                $this->sendText("📎 附件：{$file['name']}\n下载：{$file['url']}");
            }
        }

        return $success;
    }

    /**
     * 通用发送方法
     */
    protected function send(array $payload): bool
    {
        if ($this->webhookUrl === '') {
            Log::debug('企业微信 Webhook 未配置，跳过发送');

            return false;
        }

        try {
            $response = Http::timeout(10)->post($this->webhookUrl, $payload);
            
            $result = $response->json();
            
            if (($result['errcode'] ?? 1) === 0) {
                Log::info('✅ 企业微信消息发送成功', ['result' => $result]);
                return true;
            }
            
            Log::error('❌ 企业微信消息发送失败', [
                'errcode' => $result['errcode'] ?? 'unknown',
                'errmsg' => $result['errmsg'] ?? 'unknown',
            ]);
            
            return false;
        } catch (\Exception $e) {
            Log::error('❌ 企业微信消息发送异常', [
                'message' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
}
