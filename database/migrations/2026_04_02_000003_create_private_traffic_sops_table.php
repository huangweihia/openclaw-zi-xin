<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 运营 SOP 表
     */
    public function up(): void
    {
        Schema::create('private_traffic_sops', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('SOP 标题');
            $table->string('slug')->unique()->comment('Slug');
            $table->text('summary')->nullable()->comment('SOP 摘要');
            $table->longText('content')->nullable()->comment('SOP 内容（Markdown）');
            $table->string('platform')->comment('平台：wechat/xiaohongshu/douyin/other');
            $table->string('type')->comment('类型：traffic/operation/conversion/retention');
            $table->json('checklist')->nullable()->comment('检查清单');
            $table->json('templates')->nullable()->comment('话术模板');
            $table->json('metrics')->nullable()->comment('关键指标');
            $table->json('tools')->nullable()->comment('推荐工具');
            $table->string('visibility')->default('vip')->comment('可见性：public/vip');
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamps();
            
            $table->index('platform');
            $table->index('type');
            $table->index('visibility');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('private_traffic_sops');
    }
};
