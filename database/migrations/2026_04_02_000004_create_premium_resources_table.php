<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 付费资源表
     */
    public function up(): void
    {
        Schema::create('premium_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('资源标题');
            $table->string('slug')->unique()->comment('Slug');
            $table->text('summary')->nullable()->comment('资源描述');
            $table->string('type')->comment('类型：pdf/video/cloud_drive/ebook');
            $table->longText('content')->nullable()->comment('资源详情');
            $table->string('download_link')->nullable()->comment('下载链接');
            $table->string('extract_code')->nullable()->comment('提取码');
            $table->string('original_price')->nullable()->comment('原价');
            $table->json('tags')->nullable()->comment('标签');
            $table->string('visibility')->default('vip')->comment('可见性：public/vip');
            $table->integer('download_count')->default(0)->comment('下载次数');
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamps();
            
            $table->index('type');
            $table->index('visibility');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_resources');
    }
};
