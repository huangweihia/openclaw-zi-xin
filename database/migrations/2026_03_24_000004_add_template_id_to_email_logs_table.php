<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('email_logs', 'template_id')) {
            Schema::table('email_logs', function (Blueprint $table) {
                $table->foreignId('template_id')->nullable()->after('type')->constrained('email_templates')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn('template_id');
        });
    }
};
