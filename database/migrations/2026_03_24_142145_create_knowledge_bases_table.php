<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 知识库表
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('general'); // general/tech/business/other
            $table->boolean('is_public')->default(false); // 是否公开
            $table->boolean('is_vip_only')->default(true); // 是否仅 VIP 可访问
            $table->timestamps();
            
            $table->index(['user_id', 'is_public']);
        });

        // 知识文档表
        Schema::create('knowledge_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path')->nullable(); // 原文件路径
            $table->text('content'); // 解析后的文本内容
            $table->string('file_type')->nullable(); // pdf/word/txt/md
            $table->integer('file_size')->nullable(); // 文件大小（字节）
            $table->json('chunks')->nullable(); // 分块存储（用于检索）
            $table->json('embedding')->nullable(); // 向量嵌入（用于相似度搜索）
            $table->integer('view_count')->default(0);
            $table->timestamps();
            
            $table->index('knowledge_base_id');
            $table->fullText('content'); // 全文索引
        });

        // 检索记录表
        Schema::create('knowledge_search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('query'); // 搜索词
            $table->json('results')->nullable(); // 检索结果
            $table->integer('result_count')->default(0);
            $table->string('source')->default('web'); // web/api
            $table->timestamps();
            
            $table->index('user_id');
        });

        // 用户订阅表（简化版）
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('plan')->default('free'); // free/vip
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('payment_method')->nullable(); // wechat/alipay
            $table->string('transaction_id')->nullable(); // 交易 ID
            $table->integer('amount')->default(0); // 金额（分）
            $table->timestamps();
            
            $table->index('user_id');
            $table->index(['user_id', 'plan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('knowledge_search_logs');
        Schema::dropIfExists('knowledge_documents');
        Schema::dropIfExists('knowledge_bases');
    }
};
