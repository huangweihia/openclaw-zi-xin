<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('content')->comment('是否已编辑');
            $table->timestamp('edited_at')->nullable()->after('is_edited')->comment('编辑时间');
            $table->boolean('is_hidden')->default(false)->after('edited_at')->comment('是否隐藏');
            $table->integer('report_count')->default(0)->after('is_hidden')->comment('举报次数');
            
            $table->index('is_hidden');
            $table->index('report_count');
        });

        // 评论举报表
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason')->comment('举报原因');
            $table->text('description')->nullable()->comment('详细描述');
            $table->string('status')->default('pending')->comment('状态：pending/processed');
            $table->timestamps();
            
            $table->unique(['comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['is_edited', 'edited_at', 'is_hidden', 'report_count']);
        });
        Schema::dropIfExists('comment_reports');
    }
};
