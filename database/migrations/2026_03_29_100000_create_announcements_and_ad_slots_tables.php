<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('marquee_text', 500)->nullable()->comment('顶部滚动条文案，空则用 title');
            $table->longText('body')->nullable()->comment('详情页正文，支持 HTML');
            $table->boolean('is_active')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order', 'published_at']);
        });

        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('global');
            $table->boolean('is_enabled')->default(false);
            $table->string('display_mode', 20)->default('image');
            $table->string('image_url', 2048)->nullable();
            $table->text('html_content')->nullable();
            $table->string('link_url', 2048)->nullable();
            $table->timestamps();
        });

        DB::table('ad_slots')->insert([
            'name' => 'global',
            'is_enabled' => false,
            'display_mode' => 'image',
            'image_url' => null,
            'html_content' => null,
            'link_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_slots');
        Schema::dropIfExists('announcements');
    }
};
