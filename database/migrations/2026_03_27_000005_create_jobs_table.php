<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // 职位名称
            $table->string('company_name'); // 公司名称
            $table->string('location')->nullable(); // 工作地点
            $table->string('salary_range')->nullable(); // 薪资范围
            $table->text('requirements')->nullable(); // 任职要求
            $table->text('description')->nullable(); // 职位描述
            $table->string('contact_email')->nullable(); // 联系邮箱
            $table->string('contact_phone')->nullable(); // 联系电话
            $table->string('contact_wechat')->nullable(); // 联系微信
            $table->boolean('is_contact_vip')->default(false); // 是否 VIP 可见联系方式
            $table->boolean('is_published')->default(false); // 是否已发布
            $table->integer('view_count')->default(0); // 浏览次数
            $table->integer('apply_count')->default(0); // 申请次数
            $table->timestamps();
            $table->timestamp('published_at')->nullable();

            $table->index(['is_published', 'created_at']);
            $table->index('location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
