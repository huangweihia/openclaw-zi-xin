<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 用户主题偏好表
     */
    public function up(): void
    {
        Schema::create('user_theme_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('theme')->default('default')->comment('主题：default/blue/green/orange/dark');
            $table->boolean('dark_mode')->default(false)->comment('深色模式');
            $table->string('font_size')->default('medium')->comment('字体大小：small/medium/large');
            $table->boolean('follow_system')->default(false)->comment('跟随系统');
            $table->timestamps();
        });
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('user_theme_preferences');
    }
};
