<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 创建浏览历史表
        if (!Schema::hasTable('view_histories')) {
            Schema::create('view_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->morphs('viewable'); // viewable_type, viewable_id
                $table->timestamp('viewed_at')->useCurrent();
                
                $table->index(['user_id', 'viewed_at']);
                // 注意：morphs('viewable') 在 Laravel 内部通常会为(viewable_type, viewable_id)生成复合索引，
                // 若这里再手动 index() 同名列组合，会触发 MySQL 1061 Duplicate key name。
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_histories');
    }
};
