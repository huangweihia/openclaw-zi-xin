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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company');
            $table->string('salary')->nullable();
            $table->string('location')->default('杭州');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('source')->default('boss'); // boss, zhipin, lagou
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_sent')->default(false); // 是否已发送邮件
            $table->timestamps();
            
            $table->index(['source', 'is_sent']);
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
