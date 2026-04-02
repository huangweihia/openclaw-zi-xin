<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI 工具变现表
     */
    public function up(): void
    {
        Schema::create('ai_tool_monetization', function (Blueprint $table) {
            $table->id();
            $table->string('tool_name')->comment('工具名称');
            $table->string('slug')->unique()->comment('Slug');
            $table->string('tool_url')->nullable()->comment('工具链接');
            $table->string('category')->comment('分类：image/text/video/audio/code');
            $table->boolean('available_in_china')->default(false)->comment('国内是否可用');
            $table->string('pricing_model')->comment('定价模式：free/subscription/pay_as_you_go');
            $table->longText('content')->nullable()->comment('变现指南（Markdown）');
            $table->json('monetization_scenes')->nullable()->comment('变现场景（5-10 个）');
            $table->json('prompt_templates')->nullable()->comment('提示词模板');
            $table->json('pricing_reference')->nullable()->comment('定价参考');
            $table->json('channels')->nullable()->comment('接单渠道（国内/国外）');
            $table->json('delivery_standards')->nullable()->comment('交付标准');
            $table->string('visibility')->default('public')->comment('可见性：public/vip');
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamps();
            
            $table->index('category');
            $table->index('visibility');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tool_monetization');
    }
};
