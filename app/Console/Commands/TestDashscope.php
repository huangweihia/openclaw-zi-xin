<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestDashscope extends Command
{
    protected $signature = 'test:dashscope';
    protected $description = '测试阿里云百炼 API 配置';

    public function handle(): int
    {
        $this->info('🔍 测试阿里云百炼 API 配置...');
        
        $apiKey = env('DASHSCOPE_API_KEY');
        
        if (!$apiKey) {
            $this->error('❌ .env 中未配置 DASHSCOPE_API_KEY');
            return Command::FAILURE;
        }
        
        $this->info('✅ API Key 已配置：' . substr($apiKey, 0, 15) . '...' . substr($apiKey, -5));
        
        // 尝试不同的 API endpoint
        $endpoints = [
            '通义千问' => 'https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation',
            'Qwen2.5' => 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions',
        ];
        
        foreach ($endpoints as $name => $endpoint) {
            $this->newLine();
            $this->info("📡 测试 {$name} API: {$endpoint}");
            
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($endpoint, [
                    'model' => 'qwen-turbo',
                    'input' => [
                        'messages' => [
                            ['role' => 'user', 'content' => '你好，请回复"测试成功"']
                        ]
                    ],
                    'parameters' => [
                        'result_format' => 'message',
                        'max_tokens' => 100,
                    ]
                ]);
                
                $this->info('📊 响应状态码：' . $response->status());
                
                if ($response->successful()) {
                    $content = $response->json()['output']['choices'][0]['message']['content'] ?? 
                               $response->json()['choices'][0]['message']['content'] ?? null;
                    $this->info('✅ API 调用成功！');
                    $this->info('📝 AI 回复：' . $content);
                    return Command::SUCCESS;
                }
                
                $this->error('❌ API 调用失败：' . $response->body());
                
            } catch (\Exception $e) {
                $this->error('❌ 异常：' . $e->getMessage());
            }
        }
        
        return Command::FAILURE;
    }
}
