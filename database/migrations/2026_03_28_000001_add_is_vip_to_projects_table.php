<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'is_vip')) {
                $table->boolean('is_vip')->default(false)->after('is_featured');
                $table->index('is_vip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'is_vip')) {
                $table->dropIndex(['is_vip']);
                $table->dropColumn('is_vip');
            }
        });
    }
};
