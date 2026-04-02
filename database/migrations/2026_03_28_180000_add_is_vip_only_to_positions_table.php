<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('positions')) {
            return;
        }

        Schema::table('positions', function (Blueprint $table) {
            if (! Schema::hasColumn('positions', 'is_vip_only')) {
                $table->boolean('is_vip_only')->default(false)->after('is_contact_vip')->comment('VIP 专属内容（正文仅 VIP 可见完整）');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('positions')) {
            return;
        }

        Schema::table('positions', function (Blueprint $table) {
            if (Schema::hasColumn('positions', 'is_vip_only')) {
                $table->dropColumn('is_vip_only');
            }
        });
    }
};
