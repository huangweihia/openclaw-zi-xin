<?php

namespace App\Services;

use App\Models\KnowledgeDocument;
use App\Models\KnowledgeSearchLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class KnowledgeSearchService
{
    /**
     * 检索知识库
     * 
     * @param string $query 搜索词
     * @param int|null $userId 用户 ID
     * @param int $limit 返回数量
     * @return array
     */
    public function search(string $query, ?int $userId = null, int $limit = 10): array
    {
        $user = $userId ? User::find($userId) : null;
        $canAccessVipKnowledge = $user && ($user->isVip() || $user->isAdmin());

        // 1. 全文检索（数据库）
        $dbResults = $this->searchFromDatabase($query, $limit, $canAccessVipKnowledge);
        
        // 2. MCP 搜索（外部信息补充）
        $mcpResults = $this->searchFromMCP($query, 5);
        
        // 3. 合并结果
        $results = array_merge($dbResults, $mcpResults);
        
        // 4. 记录搜索日志
        if ($userId) {
            $this->logSearch($userId, $query, $results);
        }
        
        return [
            'query' => $query,
            'total' => count($results),
            'from_database' => count($dbResults),
            'from_mcp' => count($mcpResults),
            'results' => $results,
        ];
    }

    /**
     * 从数据库检索
     */
    private function searchFromDatabase(string $query, int $limit, bool $canAccessVipKnowledge): array
    {
        $q = KnowledgeDocument::query()
            ->where(function ($sub) use ($query) {
                $sub->where('content', 'like', "%{$query}%")
                    ->orWhere('title', 'like', "%{$query}%");
            });

        if (!$canAccessVipKnowledge) {
            $q->whereHas('knowledgeBase', fn ($kb) => $kb->where('is_vip_only', false));
        }

        $results = $q->with('knowledgeBase.user')
            ->limit($limit)
            ->get();
        
        return $results->map(function ($doc) {
            return [
                'type' => 'document',
                'id' => $doc->id,
                'title' => $doc->title,
                'content' => Str::limit($doc->content, 300),
                'source' => $doc->knowledgeBase?->title ?? '未知知识库',
                'author' => $doc->knowledgeBase?->user?->name ?? '匿名',
                'is_vip' => $doc->knowledgeBase?->is_vip_only ?? false,
                'url' => route('knowledge.documents.show', $doc->id),
            ];
        })->toArray();
    }

    /**
     * 从阿里云百炼搜索（外部信息）
     */
    private function searchFromMCP(string $query, int $limit): array
    {
        try {
            // 使用阿里云百炼 WebSearch API
            $apiKey = config('services.dashscope.api_key', env('DASHSCOPE_API_KEY'));
            
            if (!$apiKey) {
                return [];
            }
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation', [
                'model' => 'qwen-turbo',
                'input' => [
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => '你是一个搜索助手，请提供准确、有用的信息。'
                        ],
                        [
                            'role' => 'user',
                            'content' => $query
                        ]
                    ]
                ],
                'parameters' => [
                    'result_format' => 'message',
                    'max_tokens' => 1000,
                ]
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $content = $data['output']['choices'][0]['message']['content'] ?? '';
                
                return [
                    [
                        'type' => 'ai_answer',
                        'id' => 'ai_' . md5($query),
                        'title' => 'AI 智能回答',
                        'content' => $content,
                        'source' => '阿里云百炼',
                        'author' => 'AI 助手',
                        'is_vip' => false,
                        'url' => null,
                    ]
                ];
            }
        } catch (\Exception $e) {
            \Log::error('阿里云搜索失败：' . $e->getMessage());
        }
        
        return [];
    }

    /**
     * 记录搜索日志
     */
    private function logSearch(int $userId, string $query, array $results): void
    {
        KnowledgeSearchLog::create([
            'user_id' => $userId,
            'query' => $query,
            'results' => array_map(fn($r) => ['id' => $r['id'], 'title' => $r['title']], $results),
            'result_count' => count($results),
            'source' => 'web',
        ]);
    }

    /**
     * 检查用户搜索配额
     */
    public function checkSearchQuota(int $userId): array
    {
        $subscription = \App\Models\UserSubscription::where('user_id', $userId)
            ->latest()
            ->first();
        
        if (!$subscription || !$subscription->isActive()) {
            // 免费用户
            $used = KnowledgeSearchLog::where('user_id', $userId)
                ->whereMonth('created_at', now()->month)
                ->count();
            
            return [
                'is_vip' => false,
                'quota' => 10,
                'used' => $used,
                'remaining' => max(0, 10 - $used),
                'can_search' => $used < 10,
            ];
        }
        
        // VIP 用户
        return [
            'is_vip' => true,
            'quota' => -1, // unlimited
            'used' => 0,
            'remaining' => -1,
            'can_search' => true,
        ];
    }
}
