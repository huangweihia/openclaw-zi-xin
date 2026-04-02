<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 用户发布表
     */
    public function up(): void
    {
        Schema::create('user_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->comment('类型：case/tool/experience/resource/question');
            $table->string('title');
            $table->longText('content')->comment('内容（Markdown）');
            $table->string('category')->nullable()->comment('分类');
            $table->json('tags')->nullable()->comment('标签');
            $table->string('cover_image')->nullable()->comment('封面图');
            $table->json('attachments')->nullable()->comment('附件');
            $table->string('visibility')->default('public')->comment('可见性：public/vip/private');
            $table->string('status')->default('pending')->comment('状态：pending/approved/rejected');
            $table->text('audit_note')->nullable()->comment('审核备注');
            $table->foreignId('audited_by')->nullable()->constrained('users')->nullOnDelete()->comment('审核人');
            $table->timestamp('audited_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
            $table->index('visibility');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('user_posts');
    }
};
