<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 用户主题表 - MAX 新增
     */
    public function up(): void
    {
        Schema::create('user_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('theme_name')->default('default')->comment('主题名称：default/blue/green/orange/dark');
            $table->json('custom_colors')->nullable()->comment('自定义颜色配置');
            $table->string('font_size')->default('medium')->comment('字体大小：small/medium/large');
            $table->boolean('dark_mode')->default(false)->comment('深色模式');
            $table->boolean('follow_system')->default(false)->comment('跟随系统主题');
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * 企业微信绑定表 - MAX 新增
     */
    public function up2(): void
    {
        Schema::create('enterprise_wechat_bindings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('enterprise_wechat_userid')->comment('企业微信用户 ID');
            $table->string('open_id')->nullable()->comment('企业微信开放 ID');
            $table->boolean('status')->default(true)->comment('状态：1 正常 0 解绑');
            $table->timestamp('bound_at')->nullable()->comment('绑定时间');
            $table->timestamp('unbundled_at')->nullable()->comment('解绑时间');
            $table->timestamp('last_push_at')->nullable()->comment('最后推送时间');
            $table->timestamps();
            
            $table->index('enterprise_wechat_userid');
            $table->index('status');
        });
    }

    /**
     * 用户发布表 - MAX 新增（替代 content_submissions）
     */
    public function up3(): void
    {
        Schema::create('user_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->comment('类型：case/tool/experience/resource/question');
            $table->string('title');
            $table->longText('content');
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
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * 推送通知表 - MAX 新增
     */
    public function up4(): void
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->comment('渠道：email/wechat');
            $table->string('recipient_type')->nullable()->comment('接收者类型：user/vip/svip/all');
            $table->json('recipient_ids')->nullable()->comment('接收者 ID 列表');
            $table->string('title');
            $table->text('content');
            $table->json('data')->nullable()->comment('附加数据');
            $table->string('status')->default('pending')->comment('状态：pending/sending/sent/failed');
            $table->integer('sent_count')->default(0)->comment('已发送数量');
            $table->integer('failed_count')->default(0)->comment('失败数量');
            $table->timestamp('scheduled_at')->nullable()->comment('计划发送时间');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index('channel');
            $table->index('status');
        });
    }

    /**
     * 广告曝光表 - MAX 新增
     */
    public function up5(): void
    {
        Schema::create('ad_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('page_url')->nullable()->comment('页面 URL');
            $table->string('ip_address')->nullable()->comment('IP 地址');
            $table->text('user_agent')->nullable()->comment('User-Agent');
            $table->timestamp('created_at');
            
            $table->index('advertisement_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * 广告点击表 - MAX 新增
     */
    public function up6(): void
    {
        Schema::create('ad_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('page_url')->nullable()->comment('页面 URL');
            $table->string('ip_address')->nullable()->comment('IP 地址');
            $table->text('user_agent')->nullable()->comment('User-Agent');
            $table->timestamp('created_at');
            
            $table->index('advertisement_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_clicks');
        Schema::dropIfExists('ad_impressions');
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('user_posts');
        Schema::dropIfExists('enterprise_wechat_bindings');
        Schema::dropIfExists('user_themes');
    }
};
