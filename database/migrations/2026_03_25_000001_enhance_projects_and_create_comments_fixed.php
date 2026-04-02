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
        Schema::table('projects', function (Blueprint $table) {
            // 变现分析字段
            if (!Schema::hasColumn('projects', 'difficulty')) {
                $table->integer('difficulty')->default(3)->comment('难度 1-5');
            }
            
            if (!Schema::hasColumn('projects', 'income_range')) {
                $table->string('income_range', 191)->nullable()->comment('收入范围，如"5000-20000"');
            }
            
            if (!Schema::hasColumn('projects', 'time_commitment')) {
                $table->string('time_commitment', 191)->nullable()->comment('时间投入，如"10-20h/week"');
            }
            
            if (!Schema::hasColumn('projects', 'monetization_paths')) {
                $table->json('monetization_paths')->nullable()->comment('变现路径 JSON');
            }
            
            // 技术栈字段
            if (!Schema::hasColumn('projects', 'tech_stack')) {
                $table->json('tech_stack')->nullable()->comment('技术栈 JSON');
            }
            
            // 教程资源字段
            if (!Schema::hasColumn('projects', 'resources')) {
                $table->json('resources')->nullable()->comment('教程资源 JSON');
            }
        });
        
        // 创建评论表
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('commentable_type', 191);
                $table->unsignedBigInteger('commentable_id');
                $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
                $table->text('content');
                $table->integer('like_count')->default(0);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();
                
                $table->index(['commentable_type', 'commentable_id']);
                $table->index('user_id');
            });
        }
        
        // 创建收藏表 - 修复索引长度问题
        if (!Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('favoritable_type', 191); // 限制长度避免索引过长
                $table->unsignedBigInteger('favoritable_id');
                $table->timestamps();
                
                // 创建唯一索引，但限制字符串长度
                $table->unique(['user_id', 'favoritable_type', 'favoritable_id'], 'favorites_unique_idx');
                $table->index(['favoritable_type', 'favoritable_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'difficulty',
                'income_range',
                'time_commitment',
                'monetization_paths',
                'tech_stack',
                'resources',
            ]);
        });
        
        Schema::dropIfExists('comments');
        Schema::dropIfExists('favorites');
    }
};
