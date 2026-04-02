<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 异步任务执行日志表
        Schema::create('async_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 任务名称
            $table->string('type'); // 任务类型：fetch_articles/fetch_projects/fetch_jobs/knowledge_fetch
            $table->string('status')->default('pending'); // pending/running/completed/failed
            $table->integer('total')->default(0); // 总数量
            $table->integer('processed')->default(0); // 已处理数量
            $table->integer('success')->default(0); // 成功数量
            $table->integer('failed')->default(0); // 失败数量
            $table->text('error_message')->nullable(); // 错误信息
            $table->json('meta')->nullable(); // 额外信息
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('async_tasks');
    }
};
