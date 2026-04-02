<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_listings')) {
            Schema::create('job_listings', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('company_name');
                $table->string('salary');
                $table->string('city');
                $table->string('experience');
                $table->string('education');
                $table->text('description');
                $table->string('source_url');
                $table->json('tags')->nullable();
                $table->boolean('is_full_time')->default(true);
                $table->integer('view_count')->default(0);
                $table->timestamps();
                
                $table->index(['city', 'is_full_time']);
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
