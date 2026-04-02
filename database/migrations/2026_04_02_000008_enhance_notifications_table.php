<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 通知表增强字段
     */
    public function up(): void
    {
        // 为 system_notifications 表添加新字段
        Schema::table('system_notifications', function (Blueprint $table) {
            $table->string('action_url')->nullable()->after('content')->comment('操作链接');
            $table->boolean('is_read')->default(false)->after('action_url')->comment('是否已读');
            $table->timestamp('read_at')->nullable()->after('is_read')->comment('阅读时间');
            
            $table->index('is_read');
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::table('system_notifications', function (Blueprint $table) {
            $table->dropColumn(['action_url', 'is_read', 'read_at']);
        });
    }
};
