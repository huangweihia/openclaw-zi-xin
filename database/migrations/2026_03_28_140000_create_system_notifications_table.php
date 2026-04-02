<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('接收用户');
            $table->string('type', 50)->comment('article_liked/article_favorited/article_commented/comment_replied/admin_notice 等');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_from_admin')->default(false)->comment('后台发送，列表置顶且样式区分');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_from_admin', 'created_at']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
