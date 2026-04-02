<?php

use App\Models\EmailSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('email_settings')) {
            return;
        }

        EmailSetting::query()->updateOrCreate(
            ['key' => 'email_digest_template_key'],
            ['value' => 'daily_digest', 'description' => '定时日报模板 key']
        );
        EmailSetting::query()->updateOrCreate(
            ['key' => 'email_weekly_template_key'],
            ['value' => 'weekly_summary', 'description' => '定时周报（周一）模板 key']
        );
    }

    public function down(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('email_settings')) {
            return;
        }

        EmailSetting::query()->where('key', 'email_digest_template_key')->delete();
        EmailSetting::query()->where('key', 'email_weekly_template_key')->delete();
    }
};
