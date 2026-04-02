<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'wechat_transaction_id')) {
                $table->string('wechat_transaction_id', 64)->nullable()->after('payment_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'wechat_transaction_id')) {
                $table->dropColumn('wechat_transaction_id');
            }
        });
    }
};
