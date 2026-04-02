<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'subject',
        'content',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * 获取模板内容（替换变量）
     */
    public function render(array $data = []): string
    {
        $content = (string) $this->content;

        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', (string) $value, $content);
        }

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function (array $m) use ($data): string {
            $k = $m[1];

            return array_key_exists($k, $data) ? (string) $data[$k] : $m[0];
        }, $content) ?? $content;
    }

    /**
     * 获取所有可用模板
     */
    public static function getAvailableTemplates(): array
    {
        return [
            'daily_digest' => [
                'name' => '每日资讯日报',
                'subject' => '🤖 AI & 副业资讯日报 - {{date}}',
                'description' => '每天早上 10 点自动发送的 AI 副业资讯',
                'variables' => ['date', 'projects', 'side_hustles', 'resources'],
            ],
            'weekly_summary' => [
                'name' => '每周精选汇总',
                'subject' => '📊 本周 AI 副业精选 - {{week}}',
                'description' => '每周一发送的上周内容汇总',
                'variables' => ['week', 'top_projects', 'articles'],
            ],
            'welcome' => [
                'name' => '欢迎邮件',
                'subject' => '🎉 欢迎加入 AI 副业情报局！',
                'description' => '用户注册后发送的欢迎邮件',
                'variables' => ['name', 'email'],
            ],
        ];
    }
}
