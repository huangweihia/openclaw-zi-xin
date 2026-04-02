<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 广告表
     */
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_slot_id')->constrained()->cascadeOnDelete();
            $table->string('title')->comment('广告标题');
            $table->text('content')->nullable()->comment('广告内容（HTML）');
            $table->string('image_url')->nullable()->comment('广告图片');
            $table->string('link_url')->nullable()->comment('链接地址');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->integer('impression_count')->default(0)->comment('曝光数');
            $table->integer('click_count')->default(0)->comment('点击数');
            $table->timestamps();
            
            $table->index('ad_slot_id');
            $table->index('is_active');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
