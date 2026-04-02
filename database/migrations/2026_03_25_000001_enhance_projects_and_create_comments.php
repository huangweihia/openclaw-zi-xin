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
                $table->string('income_range')->nullable()->comment('收入范围，如"5000-20000"');
            }
            
            if (!Schema::hasColumn('projects', 'time_commitment')) {
                $table->string('time_commitment')->nullable()->comment('时间投入，如"10-20h/week"');
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
                $table->morphs('commentable'); // commentable_type, commentable_id
                $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
                $table->text('content');
                $table->integer('like_count')->default(0);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();
                
                $table->index(['commentable_type', 'commentable_id']);
                $table->index('user_id');
            });
        }
        
        // 创建收藏表
        if (!Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->morphs('favoritable'); // favoritable_type, favoritable_id
                $table->timestamps();
                
                $table->unique(['user_id', 'favoritable_type', 'favoritable_id']);
                // 注意：morphs('favoritable') 在部分 Laravel/MySQL 组合下会自动生成同名复合索引，
                // 若再创建会触发 MySQL 1061 Duplicate key name。
                // 因此显式指定一个“不会与框架自动索引冲突”的索引名。
                $table->index(
                    ['favoritable_type', 'favoritable_id'],
                    'favorites_favoritable_type_favoritable_id_idx'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // 测试环境回滚时，列可能未被创建（例如依赖条件已满足/跳过），
            // 因此 down() 需要具备“存在才删除”的幂等性。
            if (Schema::hasColumn('projects', 'difficulty')) {
                $table->dropColumn('difficulty');
            }

            if (Schema::hasColumn('projects', 'income_range')) {
                $table->dropColumn('income_range');
            }

            if (Schema::hasColumn('projects', 'time_commitment')) {
                $table->dropColumn('time_commitment');
            }

            if (Schema::hasColumn('projects', 'monetization_paths')) {
                $table->dropColumn('monetization_paths');
            }

            if (Schema::hasColumn('projects', 'tech_stack')) {
                $table->dropColumn('tech_stack');
            }

            if (Schema::hasColumn('projects', 'resources')) {
                $table->dropColumn('resources');
            }
        });
        
        Schema::dropIfExists('comments');
        Schema::dropIfExists('favorites');
    }
};
