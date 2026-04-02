<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 广告位表
     */
    public function up(): void
    {
        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('广告位名称');
            $table->string('code')->unique()->comment('广告位代码');
            $table->string('position')->comment('位置：home_top/home_sidebar/article_top/article_sidebar');
            $table->string('size')->default('300x250')->comment('尺寸');
            $table->boolean('is_active')->default(true)->comment('是否启用');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            
            $table->index('position');
            $table->index('is_active');
        });

        // 初始化默认广告位
        DB::table('ad_slots')->insert([
            ['name' => '首页顶部 Banner', 'code' => 'home_top', 'position' => 'home_top', 'size' => '1920x200', 'sort' => 1],
            ['name' => '首页侧边栏', 'code' => 'home_sidebar', 'position' => 'home_sidebar', 'size' => '300x600', 'sort' => 2],
            ['name' => '文章详情页顶部', 'code' => 'article_top', 'position' => 'article_top', 'size' => '1920x100', 'sort' => 3],
            ['name' => '文章详情页侧边', 'code' => 'article_sidebar', 'position' => 'article_sidebar', 'size' => '300x600', 'sort' => 4],
        ]);
    }

    /**
     * 回滚
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_slots');
    }
};
