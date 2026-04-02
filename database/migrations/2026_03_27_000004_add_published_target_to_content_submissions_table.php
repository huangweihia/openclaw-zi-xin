<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('content_submissions', 'published_model_type')) {
                $table->string('published_model_type')->nullable()->after('published_at');
            }

            if (!Schema::hasColumn('content_submissions', 'published_model_id')) {
                $table->unsignedBigInteger('published_model_id')->nullable()->after('published_model_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('content_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('content_submissions', 'published_model_id')) {
                $table->dropColumn('published_model_id');
            }
            if (Schema::hasColumn('content_submissions', 'published_model_type')) {
                $table->dropColumn('published_model_type');
            }
        });
    }
};
