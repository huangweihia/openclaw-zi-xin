<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. 副业实战案例库
        Schema::create('side_hustle_cases', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // 案例标题
            $table->text('summary'); // 简短描述
            $table->longText('content'); // 完整案例内容（HTML）
            $table->string('category')->default('online'); // online/offline/hybrid
            $table->string('difficulty')->default('medium'); // easy/medium/hard
            $table->string('time_commitment')->nullable(); // 时间投入（如：每天 2 小时）
            $table->string('startup_cost')->nullable(); // 启动成本（如：0 元/500 元/5000 元+）
            $table->string('revenue_model')->nullable(); // 变现模式（广告/佣金/订阅/服务等）
            $table->integer('estimated_monthly_income')->nullable(); // 预估月收入（元）
            $table->integer('actual_income')->nullable(); // 实际收入截图验证（有值表示已验证）
            $table->json('income_screenshots')->nullable(); // 收入截图 URLs
            $table->json('steps')->nullable(); // 分步骤指南 [week1, week2, ...]
            $table->json('tools_needed')->nullable(); // 需要的工具/平台
            $table->json('common_pitfalls')->nullable(); // 常见坑/失败原因
            $table->boolean('is_verified')->default(false); // 是否已验证真实
            $table->boolean('is_vip_only')->default(true); // 仅 VIP 可看
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('save_count')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'is_vip_only']);
            $table->index('is_verified');
        });

        // 2. AI 工具变现地图
        Schema::create('ai_tool_monetization', function (Blueprint $table) {
            $table->id();
            $table->string('tool_name'); // 工具名称（如 Midjourney）
            $table->string('tool_url'); // 工具官网
            $table->string('tool_logo')->nullable(); // Logo URL
            $table->string('category')->default('image'); // image/text/video/audio/code
            $table->text('description'); // 工具简介
            $table->json('monetization_scenarios'); // 变现场景 [{name, description, difficulty, income_range}]
            $table->json('prompt_templates')->nullable(); // 提示词模板
            $table->json('delivery_standards')->nullable(); // 交付标准
            $table->json('pricing_guide')->nullable(); // 定价参考 [{service, price_range}]
            $table->json('client_channels')->nullable(); // 接单渠道 [{platform, url, commission}]
            $table->boolean('is_domestic')->default(true); // 是否国内可用
            $table->string('pricing_model')->nullable(); // 定价模式（免费/订阅/按量）
            $table->integer('popularity_score')->default(0); // 热门程度（1-100）
            $table->boolean('is_vip_only')->default(false); // 基础信息免费，高级 VIP
            $table->integer('view_count')->default(0);
            $table->timestamps();
            
            $table->index(['category', 'is_domestic']);
            $table->index('popularity_score');
        });

        // 3. 私域流量运营 SOP
        Schema::create('private_traffic_sops', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // SOP 标题
            $table->string('platform')->default('wechat'); // wechat/xiaohongshu/douyin/bilibili
            $table->string('type')->default('growth'); // growth/operation/conversion/retention
            $table->text('summary'); // 简短描述
            $table->longText('content'); // 完整 SOP 内容（HTML）
            $table->json('checklist')->nullable(); // 检查清单
            $table->json('templates')->nullable(); // 可用模板 [{name, content, usage}]
            $table->json('tools_recommended')->nullable(); // 推荐工具
            $table->json('metrics')->nullable(); // 关键指标 [{name, target, formula}]
            $table->json('case_studies')->nullable(); // 相关案例引用
            $table->integer('difficulty_level')->default(3); // 难度 1-5
            $table->integer('estimated_time')->nullable(); // 预计耗时（小时）
            $table->boolean('is_vip_only')->default(true);
            $table->integer('view_count')->default(0);
            $table->integer('save_count')->default(0);
            $table->timestamps();
            
            $table->index(['platform', 'type']);
            $table->index('difficulty_level');
        });

        // 4. 付费资源合集
        Schema::create('premium_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // 资源标题
            $table->string('type')->default('course_notes'); // course_notes/report/template/toolkit
            $table->string('source')->nullable(); // 来源（如：某付费课程名）
            $table->text('description'); // 资源描述
            $table->longText('content'); // 资源内容（HTML/Markdown）
            $table->json('files')->nullable(); // 附件文件 [{name, url, size, type}]
            $table->json('tags')->nullable(); // 标签
            $table->string('original_price')->nullable(); // 原价（如用户自己买要多少钱）
            $table->boolean('is_summarized')->default(true); // 是否是整理摘要版
            $table->string('curator_note')->nullable(); // 整理者点评
            $table->integer('quality_score')->default(5); // 质量评分 1-10
            $table->boolean('is_vip_only')->default(true);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_vip_only']);
            $table->index('quality_score');
        });

        // 用户收藏表（统一收藏）
        Schema::create('user_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('savable'); // savable_id + savable_type
            $table->timestamps();
            
            $table->unique(['user_id', 'savable_id', 'savable_type']);
            $table->index('user_id');
        });

        // 用户点赞表（统一点赞）
        Schema::create('user_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('likeable'); // likeable_id + likeable_type
            $table->timestamps();
            
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_likes');
        Schema::dropIfExists('user_saves');
        Schema::dropIfExists('premium_resources');
        Schema::dropIfExists('private_traffic_sops');
        Schema::dropIfExists('ai_tool_monetization');
        Schema::dropIfExists('side_hustle_cases');
    }
};
