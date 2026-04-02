<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_slots', function (Blueprint $table) {
            $table->string('title')->nullable()->after('is_enabled');
            $table->text('body')->nullable()->after('title');
            $table->string('cta_label', 100)->nullable()->after('body');
            $table->string('image_path', 2048)->nullable()->after('image_url');
        });

        DB::table('ad_slots')->where('display_mode', 'image')->update(['display_mode' => 'standard']);
    }

    public function down(): void
    {
        Schema::table('ad_slots', function (Blueprint $table) {
            $table->dropColumn(['title', 'body', 'cta_label', 'image_path']);
        });
    }
};
