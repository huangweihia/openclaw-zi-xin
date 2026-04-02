<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'like_count')) {
                $table->integer('like_count')->default(0)->after('content');
            }

            if (!Schema::hasColumn('comments', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false)->after('like_count');
            }

            if (!Schema::hasColumn('comments', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('commentable_id')
                    ->constrained('comments')
                    ->cascadeOnDelete();
            }
        });

        Schema::table('comments', function (Blueprint $table) {
            try {
                $table->index(['commentable_type', 'commentable_id', 'is_hidden'], 'comments_target_hidden_idx');
            } catch (\Throwable $e) {
                // 索引已存在时忽略
            }

            try {
                $table->index(['user_id', 'is_hidden'], 'comments_user_hidden_idx');
            } catch (\Throwable $e) {
                // 索引已存在时忽略
            }

            try {
                $table->index(['parent_id', 'created_at'], 'comments_parent_created_idx');
            } catch (\Throwable $e) {
                // 索引已存在时忽略
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            try {
                $table->dropIndex('comments_target_hidden_idx');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('comments_user_hidden_idx');
            } catch (\Throwable $e) {
            }

            try {
                $table->dropIndex('comments_parent_created_idx');
            } catch (\Throwable $e) {
            }
        });
    }
};
