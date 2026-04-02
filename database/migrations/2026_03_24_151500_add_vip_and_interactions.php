<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 修改 articles 表
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'is_vip')) {
                $table->boolean('is_vip')->default(false)->after('is_published');
            }
            if (!Schema::hasColumn('articles', 'like_count')) {
                $table->integer('like_count')->default(0);
            }
            if (!Schema::hasColumn('articles', 'favorite_count')) {
                $table->integer('favorite_count')->default(0)->after('like_count');
            }
        });

        // 修改 projects 表
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'like_count')) {
                $table->integer('like_count')->default(0)->after('stars');
            }
            if (!Schema::hasColumn('projects', 'favorite_count')) {
                $table->integer('favorite_count')->default(0)->after('like_count');
            }
        });

        // 用户积分表
        if (!Schema::hasTable('user_points')) {
            Schema::create('user_points', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->integer('balance')->default(0);
                $table->integer('total_earned')->default(0);
                $table->integer('total_spent')->default(0);
                $table->timestamps();
                
                $table->index('user_id');
            });
        }

        // 积分流水表
        if (!Schema::hasTable('point_transactions')) {
            Schema::create('point_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->integer('amount');
                $table->string('type');
                $table->string('description');
                $table->json('meta')->nullable();
                $table->timestamps();
                
                $table->index('user_id');
                $table->index('type');
            });
        }

        // 用户行为表
        if (!Schema::hasTable('user_actions')) {
            Schema::create('user_actions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->morphs('actionable');
                $table->string('type');
                $table->timestamps();
                
                $table->unique(['user_id', 'actionable_type', 'actionable_id', 'type'], 'user_actions_unique');
            });
        }

        // 评论表
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->morphs('commentable');
                $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
                $table->text('content');
                $table->integer('like_count')->default(0);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('user_actions');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('user_points');
        
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['is_vip', 'like_count', 'favorite_count']);
        });
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['like_count', 'favorite_count']);
        });
    }
};
