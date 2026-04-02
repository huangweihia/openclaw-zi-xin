<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 副业案例表
     */
    public function up(): void
    {
        Schema::create('side_hustle_cases', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('案例标题');
            $table->string('slug')->unique()->comment('Slug');
            $table->text('summary')->nullable()->comment('案例摘要');
            $table->longText('content')->nullable()->comment('案例内容（Markdown）');
            $table->string('category')->comment('分类：online/offline/hybrid');
            $table->string('type')->comment('副业类型：ecommerce/content/service/other');
            $table->string('startup_cost')->default('0')->comment('启动成本：0 元/500 元/5000 元');
            $table->string('time_investment')->comment('时间投入：每天 2 小时/每周 10 小时');
            $table->decimal('estimated_income', 10, 2)->comment('预估月收入（元）');
            $table->decimal('actual_income', 10, 2)->nullable()->comment('实际月收入（元，已验证）');
            $table->json('income_screenshots')->nullable()->comment('收入截图（最多 3 张）');
            $table->longText('steps')->nullable()->comment('操作步骤（Markdown）');
            $table->json('tools')->nullable()->comment('所需工具');
            $table->json('pitfalls')->nullable()->comment('常见坑 + 避免方法');
            $table->boolean('willing_to_consult')->default(false)->comment('是否愿意接受咨询');
            $table->string('contact_info')->nullable()->comment('联系方式（微信/邮箱）');
            $table->string('visibility')->default('public')->comment('可见性：public/vip/private');
            $table->string('status')->default('pending')->comment('状态：pending/approved/rejected');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('作者');
            $table->foreignId('audited_by')->nullable()->constrained('users')->nullOnDelete()->comment('审核人');
            $table->text('audit_note')->nullable()->comment('审核备注');
            $table->timestamp('audited_at')->nullable()->comment('审核时间');
            $table->integer('view_count')->default(0)->comment('浏览数');
            $table->integer('like_count')->default(0)->comment('点赞数');
            $table->integer('comment_count')->default(0)->comment('评论数');
            $table->integer('favorite_count')->default(0)->comment('收藏数');
            $table->timestamps();
            
            $table->index('category');
            $table->index('type');
            $table->index('status');
            $table->index('visibility');
            $table->index('user_id');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('side_hustle_cases');
    }
};
