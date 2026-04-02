<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'reply_to_id')) {
                $table->foreignId('reply_to_id')
                    ->nullable()
                    ->after('parent_id')
                    ->constrained('comments')
                    ->nullOnDelete();

                $table->index(['parent_id', 'reply_to_id'], 'comments_parent_reply_to_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'reply_to_id')) {
                try {
                    $table->dropIndex('comments_parent_reply_to_idx');
                } catch (\Throwable $e) {
                }

                try {
                    $table->dropConstrainedForeignId('reply_to_id');
                } catch (\Throwable $e) {
                }
            }
        });
    }
};
