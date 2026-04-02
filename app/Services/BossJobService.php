<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BossJobService
{
    /**
     * 采集 BOSS 直聘 AI 相关职位
     */
    public function fetchJobs(): int
    {
        $this->log('💼 开始采集 BOSS 直聘 AI 职位...');
        
        // AI 相关关键词
        $keywords = [
            'AI 工程师',
            '人工智能',
            '大模型',
            'AIGC',
            '算法工程师',
            '机器学习',
            '深度学习',
            'NLP',
            '计算机视觉',
            'AI 产品经理',
        ];
        
        $saved = 0;
        
        foreach ($keywords as $keyword) {
            try {
                // 模拟 BOSS 直聘 API 请求
                // 实际需要通过 Selenium/Playwright 爬取
                $jobs = $this->searchJobs($keyword);
                
                foreach ($jobs as $jobData) {
                    $exists = JobListing::where('title', $jobData['title'])
                        ->where('company_name', $jobData['company_name'])
                        ->exists();
                    
                    if ($exists) {
                        continue;
                    }
                    
                    JobListing::create($jobData);
                    $saved++;
                    $this->log("✅ 保存：{$jobData['title']} - {$jobData['company_name']}");
                }
            } catch (\Exception $e) {
                $this->log("❌ 采集 {$keyword} 失败：" . $e->getMessage());
            }
        }
        
        $this->log("✅ 职位采集完成，共保存 {$saved} 个职位");
        
        return $saved;
    }

    /**
     * 搜索职位（模拟）
     */
    private function searchJobs(string $keyword): array
    {
        // 由于 BOSS 直聘需要登录和反爬，这里使用模拟数据
        // 实际部署需要：
        // 1. 使用 Selenium/Playwright 模拟浏览器
        // 2. 或者使用第三方 API 服务
        
        $jobs = [
            [
                'title' => "{$keyword}（可实习）",
                'company_name' => '某 AI 科技公司',
                'salary' => '20-40K·15 薪',
                'city' => '北京',
                'experience' => '不限',
                'education' => '本科',
                'description' => "岗位职责：\n1. 负责 AI 模型的研发和优化\n2. 参与大模型应用开发\n3. 跟踪前沿技术\n\n任职要求：\n1. 计算机相关专业\n2. 熟悉 Python/PyTorch\n3. 有 AI 项目经验",
                'source_url' => 'https://www.zhipin.com/job/example1',
                'tags' => ['AI', '大模型', 'Python'],
                'is_full_time' => true,
            ],
            [
                'title' => 'AIGC 算法工程师',
                'company_name' => '某互联网公司',
                'salary' => '30-60K·16 薪',
                'city' => '上海',
                'experience' => '3-5 年',
                'education' => '硕士',
                'description' => "岗位职责：\n1. 负责 AIGC 相关算法研发\n2. 文生图/文生视频模型优化\n3. 大模型应用落地\n\n任职要求：\n1. 硕士及以上学历\n2. 有顶会论文优先\n3. 熟悉 Diffusion/Transformer",
                'source_url' => 'https://www.zhipin.com/job/detail/' . uniqid(),
                'tags' => ['AIGC', '算法', '大模型'],
                'is_full_time' => true,
            ],
            [
                'title' => 'AI 产品经理（远程）',
                'company_name' => '某创业公司',
                'salary' => '15-30K·14 薪',
                'city' => '深圳',
                'experience' => '1-3 年',
                'education' => '本科',
                'description' => "岗位职责：\n1. 负责 AI 产品规划\n2. 用户需求分析\n3. 产品功能设计\n\n任职要求：\n1. 有 AI 产品经验\n2. 熟悉大模型应用\n3. 良好的沟通能力",
                'source_url' => 'https://www.zhipin.com/job/detail/' . uniqid(),
                'tags' => ['AI', '产品', '远程'],
                'is_full_time' => true,
            ],
        ];
        
        // 添加一些随机性
        foreach ($jobs as &$job) {
            $job['city'] = ['北京', '上海', '深圳', '杭州', '广州'][array_rand(['北京', '上海', '深圳', '杭州', '广州'])];
            $job['salary'] = ['15-30K', '20-40K', '25-50K', '30-60K'][array_rand(['15-30K', '20-40K', '25-50K', '30-60K'])] . '·' . ['14 薪', '15 薪', '16 薪'][array_rand(['14 薪', '15 薪', '16 薪'])];
        }
        
        return $jobs;
    }

    /**
     * 记录日志
     */
    private function log(string $message): void
    {
        echo "[{}] {$message}\n";
    }

    /**
     * 执行完整采集流程
     */
    public function fetchAll(): int
    {
        return $this->fetchJobs();
    }
}
